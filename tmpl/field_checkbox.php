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
<label class="checkbox-label">
    <input type="checkbox"
           class="checkbox"
           name="<?= $name; ?>"
        <?php if ($formField['required']): ?>
            required=""
		<?php endif; ?>
           <?php if($formField['checkbox_selected']):?>checked<?php endif;?> />
    <?=$formField['label']?>
</label>
