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
ModSeoclickFormsHelper::addModuleAsset('/js/popup.min.js', 'js');


$key = 0;
$form_id = "seoclick-form_". $module->id;
?>
    <div class="seoclickFormsShowpopUpForm <?=$showButtonCss?>" data-form="<?= $form_id ?>"><?= $showButtonText ?></div>
    <div id="<?= $form_id ?>" class="seoclick-forms seoclick-forms-popup-wrap <?= $moduleclass_sfx; ?>">
        <form class="form-validate" data-moduleid="<?=$module->id?>">
            <div class="container">
				<?php if ($formTitle): ?>
                    <div class="form-title"><?= $formTitle; ?></div>
				<?php endif; ?>
                <div class="text-field"><?=$formText?></div>
                <div class="message-container"></div>
				<?php foreach ($formFields as $formField): ?>
					<?php $name = "form_fields" . $key++; ?>
                    <div class="field-wrap  <?= $formField['css'] ?>">
						<?php if ($formField['label']): ?>
                        <label>
                            <span class="fieldName">
                                <?= $formField['label']; ?>
	                            <?php if ($formField['required']): ?><span class="required">*</span><?php endif;?>
                            </span>
							<?php endif; ?>
	                        <?php switch ($formField['type']):
		                        case "line_text":?>
			                        <?php break;
                                case "textarea":
			                        ?>
                                    <textarea
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
				                        <?php if ($formField['placeholder']): ?>
                                            placeholder="<?= $formField['placeholder']; ?>"
                                            title="<?= $formField['placeholder']; ?>"
				                        <?php endif; ?>></textarea>
			                        <?php break; ?>
		                        <?php case "select": ?>
                                    <select name="<?= $name; ?>">
                                        <option selected
                                                disabled><?= jText::_("MOD_SEOCLICK_FORMS_SELECT_CHOOSE_OPTION") ?></option>
				                        <?php $options = explode("\r\n", $formField['select_options']);
				                        foreach ($options as $option):?>
                                            <option value="<?= $option ?>"><?= $option ?></option>
				                        <?php endforeach; ?>
                                    </select>
			                        <?php break; ?>
		                        <?php case "file": ?>
                                    <input class="file-input"
				                        <?php if ($formField['required']): ?>
                                            required=""
				                        <?php endif; ?>
				                        <?php if ($formField['myltiple']):?>
                                            multiple="multiple"
				                        <?php endif; ?>
				                        <?php if ($formField['filesize']):?>
                                            data-size="<?=$formField['filesize']?>"
				                        <?php endif; ?>
                                           accept="<?=$formField["filetypes"]?>"
                                           name="atachment[]"
                                           type="file" />
			                        <?php break; ?>
		                        <?php case "date": ?>
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
                                            type="text"
                                            onfocus="this.type='date'"
				                        <?php if ($formField['placeholder']): ?>
                                            placeholder="<?= $formField['placeholder']; ?>"
                                            title="<?= $formField['placeholder']; ?>"
				                        <?php endif; ?>
                                    />
			                        <?php break; ?>
		                        <?php default: ?>
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
				                        <?php if ($formField['placeholder']): ?>
                                            placeholder="<?= $formField['placeholder']; ?>"
                                            title="<?= $formField['placeholder']; ?>"
				                        <?php endif; ?>
                                    />
		                        <?php endswitch; ?>
							<?php if ($formField['label']): ?>
                        </label>
					<?php endif; ?>
                    </div>
				<?php endforeach; ?>
	            <?php
	            if($joomlaRecapchaEnabled){

		            if (empty($sitekey) || empty($secretkey))
		            {
			            echo JText::_("MOD_SEOCLICK_FORMS_RECAPTCHA_KEY_ERROR");
		            }

		            switch ($joomlaRecapchaType){
			            case 'invisible':
				            $script = "
                                jQuery(function($){
                                     var invisibleRecaptcha = '<div class=\"g-recaptcha seoclick invisible-recaptcha\" data-badge=\"$joomlaRecapchaPosition\" data-sitekey=\"$sitekey\" data-callback=\"submitSeoclickForm\" data-size=\"invisible\"></div>';
                                     
				                     if(!$('.invisible-recaptcha').length) $('body').append(invisibleRecaptcha);
                                });
				            ";
				            $document->addScriptDeclaration($script);

				            break;
			            default:
				            ?>
                            <div class="g-recaptcha seoclick"
                                 data-sitekey="<?=$sitekey?>"
                                 data-theme="light"
                                 data-size="normal">
                            </div>
			            <?php
		            }
	            }?>
                <input type="hidden" name="module-name" value="<?= $module->title ?>"/>
                <div class="field-wrap submit-button-wrap"><input type="submit" class="<?=$submitCss?>" value="<?= $submitText; ?>"/></div>
            </div>
        </form>
    </div>
<?php
$script = "var seoclickForm_$module->id = {
    recaptchaEnabled: '$joomlaRecapchaEnabled',
    recaptchaType: '$joomlaRecapchaType'
};";
$document->addScriptDeclaration($script);

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