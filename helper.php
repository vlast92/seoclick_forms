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
		$phonePattern = '/^[\+ \- \( \d \) \s]{7,}/',
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

		self::$formData = $_POST;

		self::getModuleParams();

		if(self::$moduleParams->get("use_recaptcha") && !self::checkCaptchaResponse()){
			JLog::add(JText::_('MOD_SEOCLICK_FORM_CAPTCHA_ERROR'), JLog::ERROR, 'mod_seoclick_forms');

			return JText::_("MOD_SEOCLICK_FORM_CAPTCHA_ERROR");
		}

		if (!self::getEmail())
		{
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
		if (self::$moduleParams->get("joomla_recapcha"))
		{
			switch (self::$moduleParams->get("recaptcha_type")){
				case "invisible":
					JPluginHelper::importPlugin('captcha', 'recaptcha_invisible');
					$dispatcher = JEventDispatcher::getInstance("recaptcha_invisible");
					break;
				default:
					JPluginHelper::importPlugin('captcha', 'recaptcha');
					$dispatcher = JEventDispatcher::getInstance();
			}
			$answer     = $dispatcher->trigger('onCheckAnswer');

			if (!$answer[0]) return false;

			return true;
		}
		else
		{
			$response   = self::$formData['g-recaptcha-response'];
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
		if (!$messageContent)
		{
			JLog::add(JText::_('MOD_SEOCLICK_FORM_VALIDATION_ERROR'), JLog::ERROR, 'mod_seoclick_forms');

			return JText::_("MOD_SEOCLICK_FORM_VALIDATION_ERROR");
		}

		$from     = self::$moduleParams->get("mailfrom", "noreply@domain.com");
		$fromName = self::$moduleParams->get("mailfromname", "Site");

		$subject      = self::$moduleParams->get("mailsubject", "Message");
		$messageStart = '<html><head><title>' . $subject . '</title></head><body>';
		$messageEnd   = '</body></html>';
		$message      = $messageStart . $messageContent . $messageEnd;

		$boundary = "--" . md5(uniqid(time()));//разделитель
		$headers  = "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"\r\n";
		$headers  .= "MIME-Version: 1.0;\r\n";
		$headers  .= "From: " . $fromName . " <" . $from . ">\r\n";

		/*Прикрепление текста к письму*/
		$multipart = "--" . $boundary . "\r\n";
		$multipart .= "Content-Type: text/html; charset=utf-8\r\n";
		$multipart .= "Content-Transfer-Encoding: base64\r\n";
		$multipart .= "\r\n";
		$multipart .= chunk_split(base64_encode($message));

		if (!empty($_FILES))
		{
			$filesMarkup = self::addFiles($_FILES, $boundary);

			if ($filesMarkup['message']) return $filesMarkup['message'];
			$multipart .= $filesMarkup['fileMarkup'];
		}

		if (!mail(self::$email, $subject, $multipart, $headers))
		{
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
		$messageContent = "";
		$formData       = self::$formData;
		$formFields     = json_decode(json_encode(self::$moduleParams->get("form_fields")), true);

		foreach ($formData as $name => $formField)
		{
			$field_params = $formFields[$name];
			if(!$field_params) continue;

			$formField = self::checkData($formField, $field_params['type'], $field_params['maxlength'], $field_params['pattern']);
			if (!$formField) return false;

			$mailLabel = $field_params['mail_label'];

			if ($field_params['type'] == 'line_text')
			{
				$messageContent .= "\n<p>" . $mailLabel . "</p>";
			}
			else
			{
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
		if($type == "line_text" or $type == "hidden" or $type == "data" or $type == "select")
		{
			$data = self::clearData($data);
			return $data;
		}
		if (empty($pattern))
		{
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
				default:
					$pattern = false;
			}
		}
		else
		{
			$pattern = '/' . $pattern . '/u';
		}

		if (mb_strlen($data) > $maxLength) return false;

		if ($pattern && !preg_match($pattern, $data)) return false;

		return $data = self::clearData($data);
	}

	private static function clearData($data)
	{

		$data = htmlspecialchars($data);
		$data = nl2br($data);
		$data = trim($data);

		return $data;
	}

	private static function addFiles($files, $boundary)
	{

		$response      = array('fileMarkup' => '', 'message' => false);
		$filesMarkup   = '';
		$allowedFilesMimes = array();
		$filesMimes = json_decode(file_get_contents(__DIR__ ."/assets/mime_types.json"), true);
		$fieldSettings = false;

		$formFields = json_decode(json_encode(self::$moduleParams->get("form_fields")), true);
		foreach ($formFields as $formField)
		{
			if ($formField['type'] !== 'file') continue;

			$fieldSettings = true;
			$maxFileSize   = $formField['filesize'];
			$allowedFilesTypes = explode(',',$formField['filetypes']);

			foreach ($allowedFilesTypes as $allowedFileType){
				$allowedFileType = trim(str_replace(array(".","\t"), '', $allowedFileType));
				$allowedFilesMimes[$allowedFileType] = $filesMimes[$allowedFileType];
			}

			foreach ($files as $file)
			{
				if ($file["size"] / 1000 > $maxFileSize)
				{
					$response['message'] = jText::_("MOD_SEOCLICK_FORMS_ERROR_FILESIZE");
					return $response;
				}

				$filePath = $file['tmp_name'];
				$filename = $file['name'];
				preg_match('/[.]{1}[\w]{2,}$/', $filename, $fileType);

				$fileType = str_replace('.', '', $fileType[0]);
				$fileMime = mime_content_type($filePath);

				if(!in_array($fileMime, $allowedFilesMimes[$fileType])){
					$response['message'] = jText::_("MOD_SEOCLICK_FORMS_MIME_ERROR");
					return $response;
				}

				if (!$file = file_get_contents($filePath))
				{
					$response['message'] = jText::_("MOD_SEOCLICK_FORMS_ERROR_NOFILE");
					return $response;
				}

				$filesMarkup .= "\r\n--" . $boundary . "\r\n";
				$filesMarkup .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"\r\n";
				$filesMarkup .= "Content-Transfer-Encoding: base64\r\n";
				$filesMarkup .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n";
				$filesMarkup .= "\r\n";
				$filesMarkup .= chunk_split(base64_encode($file));
			}
			$filesMarkup            .= "\r\n--" . $boundary . "--\r\n";
			$response['fileMarkup'] = $filesMarkup;
		}
		if (!$fieldSettings)
		{
			$response['message'] = jText::_("MOD_SEOCLICK_FORMS_ERROR_FILE_NOSETTINGS");

			return $response;
		}

		return $response;
	}

	public static function addModuleAsset($path, $type){

		$module_assets = '/modules/mod_seoclick_forms/assets';
		$module_overrides = '/modules/mod_seoclick_forms/overrides';
		$document = JFactory::getDocument();
		$application = JFactory::getApplication();

		if(is_file(JPATH_BASE . $module_overrides . $path)){
			$url = $module_overrides . $path . '?v='
				. filemtime(JPATH_BASE . $module_overrides . $path);
		}elseif(is_file(JPATH_BASE . $module_assets . $path)){
			$url = $module_assets . $path . '?v='
				. filemtime(JPATH_BASE . $module_assets . $path);
		}else{
			$application->enqueueMessage(JText::sprintf('MOD_SEOCLICK_FORMS_FILE_NOT_FOUND', JPATH_BASE . $module_overrides . $path), 'error');
			$application->enqueueMessage(JText::sprintf('MOD_SEOCLICK_FORMS_FILE_NOT_FOUND', JPATH_BASE . $module_assets . $path), 'error');
		}

		switch ($type){
			case 'css':
				$document->addStyleSheet($url);
				break;
			case 'js':
				$document->addScript($url);
				break;
		}
	}
}