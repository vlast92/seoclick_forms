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

$module_assets = '/modules/mod_seoclick_forms/assets';

$document = JFactory::getDocument();
if ($params->get("load_styles"))
{
	$document->addStyleSheet($module_assets . '/css/seoclick_forms_styles.min.css?v='
		. filemtime($_SERVER['DOCUMENT_ROOT'] . $module_assets . '/css/seoclick_forms_styles.min.css'));
}
JHtml::_('jquery.framework');
$document->addScript($module_assets . '/js/seoclick_forms.js?v='
	. filemtime($_SERVER['DOCUMENT_ROOT'] . $module_assets . '/js/seoclick_forms.min.js'));

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$joomlaRecapcha  = $params->get("joomla_recapcha");
if (!$joomlaRecapcha)
{
	$document->addScript('https://www.google.com/recaptcha/api.js', 'text/javascript', true, true);
	$sitekey   = $params->get("joomla_recapcha_sitekey");
	$secretkey = $params->get("joomla_recapcha_secretkey");
}
$formFields     = json_decode(json_encode($params->get("form_fields")), true);
$layout         = $params->get('layout', 'default');
$showButtonText = $params->get('show_button_text', JText::_("MOD_SEOCLICK_FORMS_SHOW_FORM_DEFAULT_LABEL"));
$showButtonCss  = $params->get('show_button_css');
$formTitle      = $params->get("title", false);
$phoneMask      = $params->get("phone_mask", false);
$submitText     = $params->get("submit_text", jText::_("MOD_SEOCLICK_FORMS_SUBMIT_TEXT_DEFAULT"));
$submitCss      = $params->get("submit_css");

$namesArr = array();

require JModuleHelper::getLayoutPath('mod_seoclick_forms', $params->get('layout', 'default'));
