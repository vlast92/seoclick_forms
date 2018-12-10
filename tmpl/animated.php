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

$document->addStyleSheet($module_assets . '/css/animated_form_styles.min.css?v='
	. filemtime($_SERVER['DOCUMENT_ROOT'] . $module_assets . '/css/animated_form_styles.min.css'));
$document->addScript($module_assets . '/js/anime.min.js');
$document->addScript($module_assets . '/js/seoclick-animated-form.min.js?v='
	. filemtime($_SERVER['DOCUMENT_ROOT'] . $module_assets . '/js/seoclick-animated-form.min.js'));

$key = 0;

$form_id = "seoclick-form_". $module->id . '_' . rand(1, 9999999);
?>
<div id="<?=$form_id?>" class="form-wrap seoclick-forms <?= $moduleclass_sfx; ?>">
    <div class="message-container"></div>
    <form class="form-validate">
		<?php if ($formTitle): ?>
            <div class="form-title"><?= $formTitle; ?></div>
		<?php endif; ?>
		<?php foreach ($formFields as $formField): ?>
			<?php $name = "form_fields".$key++;?>
            <div class="field-wrap">
				<?php if ($formField['label']): ?>
                <label>
                    <span class="fieldName">
                        <?= $formField['label']; ?>
	                    <?php if ($formField['required']): ?><span class="required">*</span><?php endif;?>
                    </span>
					<?php endif; ?>
	                <?php switch ($formField['type']):
		                case "textarea":
			                ?>
                            <textarea
                                    maxlength="<?= $formField['maxlength']; ?>"
								<?php if ($formField['validation']): ?>
                                    class="validate"
                                    data-validate="<?= $formField['type']; ?>"
									<?php if ($formField['pattern']): ?>
                                        pattern="<?= $formField['pattern'] ?>"
									<?php endif; ?>
									<?php if ($formField['tooltip']): ?>
                                        data-tooltip="<?= $formField['tooltip'] ?>"
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
					                <?php if ($formField['pattern']): ?>
                                        pattern="<?= $formField['pattern'] ?>"
					                <?php endif; ?>
					                <?php if ($formField['tooltip']): ?>
                                        data-tooltip="<?= $formField['tooltip'] ?>"
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
        <svg class="svg" viewBox="0 0 100 100">
            <path id="line-path" stroke="#ffdb00" fill="none"
                  d="m8 19 L 82 19 C 90 19, 90 38, 82 38 L 8 38 C 0 38, 0 56, 8 56 L 82 56 C 90 56, 90 88, 82 88 L8 88 C 0 88, 0 56, 8 56"
                  stroke-linecap="round" stroke-dasharray="70 500" stroke-dashoffset="0"></path>
        </svg>
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
    </form>
</div>
<?php
if ($phoneMask)
{
	$script = 'jQuery(function($) {
	          
	        $("#'.$form_id.'").find("[data-validate = \'phone\']").mask("' . $phoneMask . '");
	    });';

	$document->addScript($module_assets . '/js/jquery.maskedinput-1.2.2.min.js');
	$document->addScriptDeclaration($script);
}