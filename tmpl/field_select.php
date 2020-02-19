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
    <option selected disabled>
        <?php if($formField['placeholder']): ?>
            <?=$formField['placeholder']?>
        <?php else: ?>
	        <?= jText::_("MOD_SEOCLICK_FORMS_SELECT_CHOOSE_OPTION") ?>
        <?php endif; ?>
    </option>
	<?php $options = explode("\r\n", $formField['select_options']);
	foreach ($options as $option):?>
        <option value="<?= $option ?>"><?= $option ?></option>
	<?php endforeach; ?>
</select>