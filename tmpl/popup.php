<?php
/**
 * @package    seoclick_forms
 *
 * @author     Alexey Popucheuev <vlasteg@mail.ru>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://seoclick.by
 */

defined('_JEXEC') or die;

ModSeoclickFormsHelper::addModuleAsset('/css/popup_form_styles.min.css', 'css');
if($debug)
{
	ModSeoclickFormsHelper::addModuleAsset('/js/popup.js', 'js');
}else{
	ModSeoclickFormsHelper::addModuleAsset('/js/popup.min.js', 'js');
}

$form_id = "seoclick_form_". $module->id . "_" . rand(1, 10000);
?>
    <div class="seoclickFormsShowpopUpForm <?=$showButtonCss?>" data-form="<?= $form_id ?>"><?= $showButtonText ?></div>
    <div style="display: none;">
        <div id="<?= $form_id ?>" class="seoclick-forms seoclick-forms-popup-wrap <?= $moduleclass_sfx; ?>">
            <form class="form-validate" data-moduleid="<?= $form_id ?>">
                <div class="container">
				    <?php if ($formTitle): ?>
					    <?php require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'form_title');?>
				    <?php endif; ?>
				    <?php if ($formText): ?>
					    <?php require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'form_text');?>
				    <?php endif; ?>
                    <div class="message-container"></div>
				    <?php foreach ($formFields as $name => $formField): ?>
                        <div class="field-wrap  <?= $formField['css'] ?>">
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
				    <?php
				    if ($joomlaRecapchaEnabled)
				    {
					    require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'field_recaptcha');
				    } ?>
                    <input type="hidden" name="module-name" value="<?= htmlspecialchars($module->title) ?>"/>
                    <div class="field-wrap submit-button-wrap"><input type="submit" class="<?=$submitCss?>" value="<?= $submitText; ?>"/></div>
                </div>
            </form>
        </div>
    </div>
<?php
require JModuleHelper::getLayoutPath('mod_seoclick_forms', 'form_params');

if ($phoneMask)
{
	$script = 'jQuery(function($) {
	          
	        $("#' . $form_id . '").find("[data-validate = \'phone\']").mask("' . $phoneMask . '");
	    });';

	ModSeoclickFormsHelper::addModuleAsset('/js/jquery.maskedinput-1.2.2.min.js', 'js');
	$document->addScriptDeclaration($script);
}

$additionalJs = "jQuery(document).ready(function($){ var $ = jQuery, form = $('#$form_id');" . $additionalJs . "});";
$document->addScriptDeclaration($additionalJs);