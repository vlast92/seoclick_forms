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

$radioButtons = explode("\r\n", $formField['radio_buttons']);

foreach ($radioButtons as $key => $radioButton)
{
	$buttons[$key] = JHTML::_('select.option', $radioButton, $radioButton);
	$buttons[$key]->id = $name . $key . $module->id;
}

$attribs['class'] = "advancedRadio";
if ($formField['required']) $attribs['required'] = "true";

echo JHTML::_('select.radiolist', $buttons, $name, $attribs);