<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;
?>
<select name="<?= $name; ?>">
    <option selected
            disabled><?= jText::_("MOD_SEOCLICK_FORMS_SELECT_CHOOSE_OPTION") ?></option>
	<?php $options = explode("\r\n", $formField['select_options']);
	foreach ($options as $option):?>
        <option value="<?= $option ?>"><?= $option ?></option>
	<?php endforeach; ?>
</select>