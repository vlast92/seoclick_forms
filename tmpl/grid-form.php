<?php
/**
 * @package    seoclick_forms
 *
 * @author     Alexey Popucheuev <vlasteg@mail.ru>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://seoclick.by
 */
/*TODO Сделать на bootstrap*/
defined('_JEXEC') or die;

$form_id = "seoclick_form_". $module->id . "_" . rand(1, 10000);
?>
<div id="<?=$form_id?>" class="form-wrap seoclick-forms <?= $moduleclass_sfx; ?>">
    <form class="form-validate" data-moduleid="<?= $form_id ?>">
		<?php if ($formTitle): ?>
			<?php require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'form_title');?>
		<?php endif; ?>
	    <?php if ($formText): ?>
		    <?php require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'form_text');?>
	    <?php endif; ?>
        <div class="message-container"></div>
        <div class="g-grid">
			<?php foreach ($formFields as $name => $formField): ?>
                <div class="field-wrap <?= $formField['css'] ?>">
					<?php if ($formField['label'] and $formField['type'] !== 'checkbox' and $formField['type'] !== 'line_text'): ?>
                    <label>
                        <span class="fieldName">
                            <?= $formField['label']; ?>
                            <?php if ($formField['required']): ?><span class="required">*</span><?php endif;?>
                        </span>
						<?php endif; ?>
	                    <?php switch ($formField['type'])
	                    {
		                    case "line_text":
			                    require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'field_line');
			                    break;
		                    case "checkbox":
			                    require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'field_checkbox');
			                    break;
		                    case "textarea":
			                    require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'field_textarea');
			                    break;
		                    case "select":
			                    require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'field_select');
			                    break;
		                    case "radio":
			                    require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'field_radio');
			                    break;
		                    case "file":
			                    require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'field_file');
			                    break;
		                    case "date":
			                    require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'field_date');
			                    break;
		                    default:
			                    require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'field_default');
	                    } ?>
						<?php if ($formField['label'] and $formField['type'] !== 'checkbox' and $formField['type'] !== 'line_text'): ?>
                    </label>
				<?php endif; ?>
                </div>
			<?php endforeach; ?>
        </div>
	    <?php
	    if ($joomlaRecapchaEnabled)
	    {
		    require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'field_recaptcha');
	    }
	    require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'field_module_id');
	    ?>
        <div class="field-wrap submit-button-wrap"><input type="submit" class="<?=$submitCss?>" value="<?= $submitText; ?>"/></div>
    </form>
</div>
<?php
require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'form_params');

$document->addScriptDeclaration($script);
if ($phoneMask)
{
	$script = 'jQuery(function($) {
	          
	        $("#'.$form_id.'").find("[data-validate = \'phone\']").mask("' . $phoneMask . '");
	    });';

	ModSeoclickFormsHelper::addModuleAsset('/js/jquery.maskedinput-1.2.2.min.js', 'js');
	$document->addScriptDeclaration($script);
}

$additionalJs = "jQuery(document).ready(function($){ var $ = jQuery, form = $('#$form_id');" . $additionalJs . "});";
$document->addScriptDeclaration($additionalJs);