<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

if (empty($sitekey) || empty($secretkey))
{
	echo JText::_("MOD_SEOCLICK_FORMS_RECAPTCHA_KEY_ERROR");
}

switch ($joomlaRecapchaType){
	case 'invisible':
		?>
		<div class="g-recaptcha seoclick invisible-recaptcha"
		     data-badge="<?=$joomlaRecapchaPosition?>"
		     data-sitekey="<?=$sitekey?>"
		     data-callback="submitSeoclickForm"
		     data-size="invisible">
		</div>
		<?php
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