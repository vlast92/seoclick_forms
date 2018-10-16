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

$document->addStyleSheet($module_assets . '/css/popup_form_styles.min.css?v='
	. filemtime($_SERVER['DOCUMENT_ROOT'] . $module_assets . '/css/popup_form_styles.min.css'));
$document->addScript($module_assets . '/js/popup.min.js?v='
	. filemtime($_SERVER['DOCUMENT_ROOT'] . $module_assets . '/js/popup.min.js'));

$key = 0;
$form_id = "seoclick-form_" . rand(1, 9999999);
?>
    <div class="seoclickFormsShowpopUpForm" data-form="<?= $form_id ?>"><?= $showButtonText ?></div>
    <div id="<?= $form_id ?>" class="seoclick-forms seoclick-forms-popup-wrap <?= $moduleclass_sfx; ?>">
        <form class="form-validate">
            <div class="container">
				<?php if ($formTitle): ?>
                    <div class="form-title"><?= $formTitle; ?></div>
				<?php endif; ?>
                <div class="message-container"></div>
				<?php foreach ($formFields as $formField): ?>
					<?php $name = "form_fields" . $key++; ?>
                    <div class="field-wrap">
						<?php if ($formField['label']): ?>
                        <label><span class="fieldName"><?= $formField['label']; ?></span>
							<?php endif; ?>
							<?php if ($formField['type'] == "textarea"): ?>
                                <textarea
                                        maxlength="<?= $formField['maxlength']; ?>"
                                        class="validate"
                                        data-validate="<?= $formField['type']; ?>"
                                        name="<?= $name; ?>"
									<?php if ($formField['required']): ?>
                                        required=""
									<?php endif; ?>
									<?php if ($formField['placeholder']): ?>
                                        placeholder="<?= $formField['placeholder']; ?>"
									<?php endif; ?>></textarea>
							<?php elseif ($formField['type'] == "select"): ?>
                                <select name="<?= $name; ?>">
                                    <option selected
                                            disabled><?= jText::_("MOD_SEOCLICK_FORMS_SELECT_CHOOSE_OPTION") ?></option>
									<?php $options = explode("\r\n", $formField['select_options']);
									foreach ($options as $option):?>
                                        <option value="<?= $option ?>"><?= $option ?></option>
									<?php endforeach; ?>
                                </select>
							<?php else: ?>
                                <input
                                        maxlength="<?= $formField['maxlength']; ?>"
                                        class="validate"
                                        data-validate="<?= $formField['type']; ?>"
                                        name="<?= $name; ?>"
									<?php if ($formField['required']): ?>
                                        required=""
									<?php endif; ?>
									<?php if ($formField['type'] == "email"): ?>
                                        type="email"
									<?php elseif ($formField['type'] == "date"): ?>
                                        type="date"
									<?php elseif($formField['type'] == "hidden"):?>
                                        type="hidden"
									<?php else: ?>
                                        type="text"
									<?php endif; ?>
									<?php if ($formField['placeholder']): ?>
                                        placeholder="<?= $formField['placeholder']; ?>"
									<?php endif; ?>
                                />
							<?php endif; ?>
							<?php if ($formField['label']): ?>
                        </label>
					<?php endif; ?>
                    </div>
				<?php endforeach; ?>
				<?php
				if ($joomlaRecapcha)
				{
					$recapcha = JCaptcha::getInstance('recaptcha');
					if ($recapcha)
					{
						echo $recapcha->display('captcha', 'captcha', 'captcha');
					}
					else
					{
						echo JText::_("MOD_SEOCLICK_FORM_RECAPCHA_NOT_ACTIVE");
					}
				}
                elseif (empty($sitekey) || empty($secretkey))
				{
					echo JText::_("MOD_SEOCLICK_FORMS_RECAPTCHA_KEY_ERROR");
				}
				else
				{
					echo "<div class=\"g-recaptcha\" data-sitekey=\"$sitekey\"></div>";
				} ?>
                <input type="hidden" name="module-name" value="<?= $module->title ?>"/>
                <div class="field-wrap submit-button-wrap"><input type="submit" value="<?= $submitText; ?>"/></div>
            </div>
        </form>
    </div>
<?php
if ($phoneMask)
{
	$script = 'jQuery(function($) {
	          
	        $("#' . $form_id . '").find("[data-validate = \'phone\']").mask("' . $phoneMask . '");
	    });';

	$document->addScript($module_assets . '/js/jquery.maskedinput-1.2.2.min.js');
	$document->addScriptDeclaration($script);
}