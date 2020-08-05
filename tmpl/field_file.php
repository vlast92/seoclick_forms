<?php
/**
 * @package     seoclick_forms
 * @subpackage  tmp
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;
?>
<input class="file-input"
	<?php if ($formField['multiple']): ?>
        multiple="multiple"
	<?php endif; ?>
	<?php if ($formField['filesize']): ?>
        data-size="<?= $formField['filesize'] ?>"
	<?php endif; ?>
       accept="<?= $formField["filetypes"] ?>"
       name="atachment[]"
       type="file" style="display:none;"/>
<button class="custom-fileinput <?= $formField['button_css'] ?>" <?php if ($formField['required']): ?>data-required="true"<?php endif; ?> >
	<?php if ($formField['multiple']): ?>
		<?= JText::_('MOD_SEOCLICK_FORMS_FILEINPUT_SELECT_FILES_LABEL'); ?>
    <?php else: ?>
        <?= JText::_('MOD_SEOCLICK_FORMS_FILEINPUT_SELECT_FILE_LABEL'); ?>
    <?php endif; ?>
</button>
<span class="custom-filelist" data-nofile="<?= JText::_("MOD_SEOCLICK_FORMS_FILEINPUT_NOFILES_LABEL") ?>"
      data-file="<?= JText::_('MOD_SEOCLICK_FORMS_FILEINPUT_FILE_LABEL') ?>"
      data-files="<?= JText::_('MOD_SEOCLICK_FORMS_FILEINPUT_FILES_COUNT_LABEL') ?>"><?= JText::_("MOD_SEOCLICK_FORMS_FILEINPUT_NOFILES_LABEL") ?></span>
