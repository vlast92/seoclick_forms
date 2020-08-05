<?php
/**
 * @package     seoclick_forms
 * @subpackage  tmp
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;

JHTML::_('formbehavior.chosen');

if($formField['placeholder']){
	$selectText = $formField['placeholder'];
}else{
    $selectText = jText::_("MOD_SEOCLICK_FORMS_SELECT_CHOOSE_OPTION");
}
$selectOptions = explode("\r\n", $formField['select_options']);

$options[] = JHTML::_('select.option', '1', $selectText, 'value', 'text', true);
foreach ($selectOptions as $option)
{
	$options[] = JHTML::_('select.option', $option, $option);
}

echo JHTML::_('select.genericlist', $options, $name, array('class' => 'advancedSelect'), 'value', 'text', 1, $name . rand(1, 1000));