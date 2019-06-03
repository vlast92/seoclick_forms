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
<input class="file-input"
	<?php if ($formField['required']): ?>
        required=""
	<?php endif; ?>
	<?php if ($formField['multiple']): ?>
        multiple="multiple"
	<?php endif; ?>
	<?php if ($formField['filesize']): ?>
        data-size="<?= $formField['filesize'] ?>"
	<?php endif; ?>
       accept="<?= $formField["filetypes"] ?>"
       name="atachment[]"
       type="file" style="display:none;"/>
<button class="custom-fileinput <?= $formField['button_css'] ?>"><?= JText::_('MOD_SEOCLICK_FORMS_FILEINPUT_SELECT_FILES_LABEL') ?></button>
<span class="custom-filelist" data-nofile="<?= JText::_("MOD_SEOCLICK_FORMS_FILEINPUT_NOFILES_LABEL") ?>"
      data-file="<?= 'Файл: ' ?>"
      data-files="<?= JText::_('MOD_SEOCLICK_FORMS_FILEINPUT_FILES_COUNT_LABEL') ?>"><?= JText::_("MOD_SEOCLICK_FORMS_FILEINPUT_NOFILES_LABEL") ?></span>
