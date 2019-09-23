<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;

$metricsCode = str_replace("'", "\"", $metricsCode);
$formParams = "var $form_id = {
                    recaptchaEnabled: '$joomlaRecapchaEnabled',
                    recaptchaType: '$joomlaRecapchaType',
                    metricsCode: '$metricsCode'
                };";
$document->addScriptDeclaration($formParams);