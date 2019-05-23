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
$form_id = "seoclick-form_". $module->id . "_" . rand(1, 10000);
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
	                        <?php switch ($formField['type'])
	                        {
		                        case "line_text":
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