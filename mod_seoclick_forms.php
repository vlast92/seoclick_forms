<?php
/**
 * @package    seoclick_forms
 *
 * @author     Alexey Popucheuev <vlasteg@mail.ru>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://seoclick.by
 */
defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/helper.php';

$document = JFactory::getDocument();
if ($params->get("load_styles"))
{
	ModSeoclickFormsHelper::addModuleAsset('/css/seoclick_forms_styles.min.css', 'css');
}
JHtml::_('jquery.framework');
$debug = $params->get("debug_mode", 0);
if ($debug)
{
	ModSeoclickFormsHelper::addModuleAsset('/js/seoclick_forms.js', 'js');
}
else
{
	ModSeoclickFormsHelper::addModuleAsset('/js/seoclick_forms.min.js', 'js');
}


$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$joomlaRecapchaEnabled  = $params->get("use_recaptcha");
$joomlaRecapchaType     = $params->get("recaptcha_type");
$joomlaRecapchaPosition = $params->get("recaptcha_position");
$joomlaRecapcha         = $params->get("joomla_recapcha");
if ($joomlaRecapchaEnabled)
{
	$lang = JFactory::getLanguage();
	$document->addScript('https://www.google.com/recaptcha/api.js?onload=renderRecaptcha&render=explicit&hl=' . $lang->getTag(), 'text/javascript', true, true);

	if ($joomlaRecapchaType == 'invisible' && $joomlaRecapcha)
	{
		$invisibleRecaptcha     = JPluginHelper::getPlugin('captcha', 'recaptcha_invisible');
		$invisibleCaptchaParams = new JRegistry($invisibleRecaptcha->params);
		$sitekey                = $invisibleCaptchaParams->get("public_key");
		$secretkey              = $invisibleCaptchaParams->get("private_key");
	}
	elseif ($joomlaRecapcha)
	{
		$recaptcha       = JPluginHelper::getPlugin('captcha', 'recaptcha');
		$recaptchaParams = new JRegistry($recaptcha->params);
		$sitekey         = $recaptchaParams->get("public_key");
		$secretkey       = $recaptchaParams->get("private_key");
	}
	else
	{
		$sitekey   = $params->get("joomla_recapcha_sitekey");
		$secretkey = $params->get("joomla_recapcha_secretkey");
	}
}

//Print language variables
$script = "
var mod_seoclick_forms_language_variables = {
    'default_site_tooltip_text' : '" . JText::_("MOD_SEOCLICK_FORMS_DEFAULT_SITE_TOOLTIP") . "',
    'default_email_tooltip_text' : '" . JText::_("MOD_SEOCLICK_FORMS_DEFAULT_EMAIL_TOOLTIP") . "',
    'default_phone_tooltip_text' : '" . JText::_("MOD_SEOCLICK_FORMS_DEFAULT_PHONE_TOOLTIP") . "',
    'file_size_error' : {
	    'file_size_error_text_1' : '" . JText::_("MOD_SEOCLICK_FORMS_FILE_SIZE_1") . "',
	    'file_size_error_text_2' : '" . JText::_("MOD_SEOCLICK_FORMS_FILE_SIZE_2") . "',
	    'file_size_error_text_3' : '" . JText::_("MOD_SEOCLICK_FORMS_FILE_SIZE_3") . "',
	    'file_size_error_text_4' : '" . JText::_("MOD_SEOCLICK_FORMS_FILE_SIZE_4") . "'
    },
    'captcha_validation_error' : '" . JText::_("MOD_SEOCLICK_FORM_CAPTCHA_ERROR") . "',
    'no_files_error' : '" . JText::_("MOD_SEOCLICK_FORM_NO_FILES_ERROR") . "',
    'recaptcha_check_text' : '" . JText::_("MOD_SEOCLICK_FORM_RECAPTCHA_CHECK") . "',
    'sending_text' : '" . JText::_("MOD_SEOCLICK_FORMS_SENDING_TEXT") . "'
};
";
$document->addScriptDeclaration($script);

$formFields     = json_decode(json_encode($params->get("form_fields")), true);
$layout         = $params->get('layout', 'default');
$showButtonText = $params->get('show_button_text', JText::_("MOD_SEOCLICK_FORMS_SHOW_FORM_DEFAULT_LABEL"));
$showButtonCss  = $params->get('show_button_css');
$formTitle      = $params->get("title", false);
$formText       = $params->get("form_text", false);
$phoneMask      = $params->get("phone_mask", false);
$submitText     = $params->get("submit_text", jText::_("MOD_SEOCLICK_FORMS_SUBMIT_TEXT_DEFAULT"));
$submitCss      = $params->get("submit_css");
$additionalJs   = $params->get("additional_js", false);
$metricsCode    = $params->get("metrics_code", false);

$namesArr = array();

require JModuleHelper::getLayoutPath('mod_seoclick_forms', $params->get('layout', 'default'));
