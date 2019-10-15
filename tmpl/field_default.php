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
<input
	maxlength="<?= $formField['maxlength']; ?>"
	<?php if ($formField['validation']): ?>
		class="validate"
		data-validate="<?= $formField['type']; ?>"
		<?php if (!$formField['default_validation']):?>
			<?php if ($formField['pattern']): ?>
				pattern="<?= $formField['pattern'] ?>"
			<?php endif; ?>
			<?php if ($formField['tooltip']): ?>
				data-tooltip="<?= $formField['tooltip'] ?>"
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
	name="<?= $name; ?>"
	<?php if ($formField['required']): ?>
		required=""
	<?php endif; ?>
	<?php if ($formField['type'] == "email"): ?>
		type="email"
	<?php elseif ($formField['type'] == "date"): ?>
		type="date"
	<?php elseif ($formField['type'] == "hidden"): ?>
		type="hidden"
	<?php else: ?>
		type="text"
	<?php endif; ?>
    <?php if($formField['def_value']): ?>
        value="<?=$formField['def_value']?>"
    <?php endif ?>
	<?php if ($formField['placeholder']): ?>
		placeholder="<?= $formField['placeholder']; ?>"
		title="<?= $formField['placeholder']; ?>"
	<?php endif; ?>
/>
