<?php
/**
 * @package     seoclick-forms
 * @subpackage  tmp
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
				data-tooltip="<?= htmlspecialchars($formField['tooltip']) ?>"
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
	name="<?= $name; ?>"
	<?php if ($formField['required']): ?>
		required=""
	<?php endif; ?>
	type="text"
	onfocus="this.type='date'"
	<?php if ($formField['placeholder']): ?>
		placeholder="<?= htmlspecialchars($formField['placeholder']); ?>"
		title="<?= htmlspecialchars($formField['placeholder']); ?>"
	<?php endif; ?>
/>
