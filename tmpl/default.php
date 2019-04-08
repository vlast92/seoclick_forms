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

$key = 0;

$form_id = "seoclick-form_". $module->id . '_' . rand(1, 9999999);
?>
    <div id="<?= $form_id ?>" class="form-wrap seoclick-forms <?= $moduleclass_sfx; ?>">
        <form class="form-validate">
			<?php if ($formTitle): ?>
                <div class="form-title"><?= $formTitle; ?></div>
			<?php endif; ?>
            <div class="message-container"></div>
			<?php foreach ($formFields as $formField): ?>
				<?php $name = "form_fields" . $key++; ?>
                <div class="field-wrap  <?= $formField['css'] ?>">
					<?php if ($formField['label']): ?>
                    <label>
                    <span class="fieldName">
                        <?= $formField['label']; ?>
	                    <?php if ($formField['required']): ?><span class="required">*</span><?php endif; ?>
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
									<?php endif; ?>
                                />
							<?php endswitch; ?>
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
            <div class="field-wrap submit-button-wrap"><input type="submit" class="<?=$submitCss?>" value="<?= $submitText; ?>"/></div>
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