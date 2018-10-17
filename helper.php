<?php
/**
 * @package    seoclick_forms
 *
 * @author     Alexey Popucheuev <vlasteg@mail.ru>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://seoclick.by
 */

class ModSeoclickFormsHelper
{
	static private $formData, $moduleParams, $email,
		$sitePattern = '/^[\w \.]+[\.]{1}[\D]{2,4}$/',
		$phonePattern = '/^[\d \+]{1,5}[\( \d \) \s]{1,10}[-?\d \s]+$/',
		$emailPattern = '/^[\w \.]+[@]{1}[\w]+[\.]{1}[\D]{2,4}$/';

	/*
	 * Функция принимает данные через AJAX
	 * проверяет их и производит вызов
	 * отправки почты. Возвращает строку состояния.
	 */
	public static function getAjax()
	{
		JLog::addLogger(
			array(
				'text_file' => 'mod_seoclick_forms.log.php'
			),
			JLog::ALL,
			array('mod_seoclick_forms')
		);

		self::$formData = $_POST["data"];

		self::getModuleParams();

		if (!self::checkCaptchaResponse()){
			JLog::add(JText::_('MOD_SEOCLICK_FORM_CAPTCHA_ERROR'), JLog::ERROR, 'mod_seoclick_forms');
			return JText::_("MOD_SEOCLICK_FORM_CAPTCHA_ERROR");
		}

		if (!self::getEmail()){
			JLog::add(JText::_('MOD_SEOCLICK_FORMS_NO_EMAIL'), JLog::ERROR, 'mod_seoclick_forms');
			return JText::_("MOD_SEOCLICK_FORMS_NO_EMAIL");
		}

		$response = self::sendEmail();

		return $response;
	}

	/*
	 * Функция проверки ответа recaptcha.
	 * Возвращает boolean значение
	 */
	private static function checkCaptchaResponse()
	{
		if(self::$moduleParams->get("joomla_recapcha")){
			JPluginHelper::importPlugin('captcha');
			$dispatcher = JEventDispatcher::getInstance();
			$answer     = $dispatcher->trigger('onCheckAnswer');

			if (!$answer[0]) return false;

			return true;
		}else{
			$response  = $_POST['g-recaptcha-response'];
			$google_url = "https://www.google.com/recaptcha/api/siteverify";
			$secret     = self::$moduleParams->get("joomla_recapcha_secretkey");
			$ip         = $_SERVER['REMOTE_ADDR'];
			$url        = $google_url . "?secret=" . $secret . "&response=" . $response . "&remoteip=" . $ip;
			$json       = file_get_contents($url);
			$response   = json_decode($json, true);

			return $response['success'];
		}
	}

	/*
	 * Функция получает из модуля список параметров
	 * и записывает их в статическую переменную
	 */
	private static function getModuleParams()
	{
		$module             = JModuleHelper::getModule('seoclick_forms', self::$formData['module-name']);
		$params             = new JRegistry();
		self::$moduleParams = $params->loadString($module->params);
	}

	/*
	 * Функция проверки наличия
	 * email'a получателя и запись его
	 * в статическую переменную.
	 * Возвращает boolean значение.
	 */
	private static function getEmail()
	{
		self::$email = self::$moduleParams->get("email", false);

		if (!self::$email)
		{
			$config      = JFactory::getConfig();
			self::$email = $config->get('mailfrom', false);
		}

		if (!self::$email) return false;

		return true;
	}

	/*
	 * Функция отправки почты.
	 * Возвращает строку со статусом выполнения.
	 */
	private static function sendEmail()
	{
		$messageContent = self::getMessageContent();
		if (!$messageContent) {
			JLog::add(JText::_('MOD_SEOCLICK_FORM_VALIDATION_ERROR'), JLog::ERROR, 'mod_seoclick_forms');
			return JText::_("MOD_SEOCLICK_FORM_VALIDATION_ERROR");
		}

		$from     = self::$moduleParams->get("mailfrom", "noreply@domain.com");
		$fromName = self::$moduleParams->get("mailfromname", "Site");

		$subject      = self::$moduleParams->get("mailsubject", "Message");
		$messageStart = '<html><head><title>' . $subject . '</title></head><body>';
		$messageEnd   = '</body></html>';
		$message      = $messageStart . $messageContent . $messageEnd;
		$headers      = "Content-type: text/html; charset=utf-8 \r\n";
		$headers      .= "From: " . $fromName . " <" . $from . ">\r\n";

		if (!mail(self::$email, $subject, $message, $headers)){
			JLog::add(JText::_('MOD_SEOCLICK_FORM_SENDING_ERROR'), JLog::ERROR, 'mod_seoclick_forms');
			return JText::_("MOD_SEOCLICK_FORM_SENDING_ERROR");
		}

		return JText::_("MOD_SEOCLICK_FORMS_SUCCESS");
	}
	/*
	 * Функция генерирует содержимое письма
	 * и возвращает его либо false
	 */
	private static function getMessageContent()
	{
		$messageContent  = "";
		$formData        = self::$formData;
		$formFields      = json_decode(json_encode( self::$moduleParams->get("form_fields")), true);

		foreach ($formData as $name => $formField)
		{
			$field_params = $formFields[$name];

			if ($name == "g-recaptcha-response" || $name == "module-name" || empty($formField) && $field_params['type'] != 'line_text'){
				continue;
			}

			$formField = self::checkData($formField, $field_params['type'], $field_params['maxlength'], $field_params['pattern']);
			if (!$formField) return false;

			$mailLabel = $field_params['mail_label'];

			if($field_params['type'] == 'line_text'){
				$messageContent .= "\n<p>" . $mailLabel . "</p>";
			}else{
				$messageContent .= "\n<p>" . $mailLabel . ":&nbsp;" . $formField . "</p>";
			}

		}

		return $messageContent;
	}

	/*
	 * Функция проверки данных.
	 * Функция получает поле формы ,его тип, максимум символов и регулярное выражение.
	 * Возвращает отформатированую строку либо false
	 */
	private static function checkData($data, $type, $maxLength, $pattern)
	{
		if(empty($pattern)){
			switch ($type)
			{
				case "site":
					$pattern = self::$sitePattern;
					break;
				case "phone":
					$pattern = self::$phonePattern;
					break;
				case "email":
					$pattern = self::$emailPattern;
					break;
				case "line_text":
					$data = self::clearData($data);
					return $data;
				default:
					$pattern = false;
			}
		}else{
			$pattern = '/'.$pattern.'/';
		}

		if (strlen($data) > $maxLength) return false;

		if ($pattern && !preg_match($pattern, $data)) return false;

		return $data = self::clearData($data);
	}

	private static function clearData($data){

		$data = htmlspecialchars($data);
		$data = nl2br($data);
		$data = trim($data);

		return $data;
	}
}