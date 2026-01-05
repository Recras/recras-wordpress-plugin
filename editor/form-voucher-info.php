<?php
$recras_instance = \Recras\Settings::getInstance();
if (!$recras_instance) {
    \Recras\Settings::errorNoRecrasName();
    return;
}

$recras_vouchers_model = new \Recras\Vouchers();
$recras_voucher_templates = $recras_vouchers_model->getTemplates($recras_instance);
?>

<dl>
    <dt><label for="template_id"><?php esc_html_e('Template', 'recras'); ?></label>
    <dd><?php if (is_string($recras_voucher_templates)) { ?>
            <input type="number" id="template_id" min="0" required>
            <?= esc_html($recras_voucher_templates); ?>
        <?php } elseif (is_array($recras_voucher_templates)) { ?>
            <select id="template_id" required>
                <?php foreach ($recras_voucher_templates as $recras_vt_id => $recras_voucher_template) { ?>
                <option value="<?= esc_html($recras_vt_id); ?>"><?= esc_html($recras_voucher_template->name); ?>
                <?php } ?>
            </select>
        <?php } ?>
    <dt><label for="show_what"><?php esc_html_e('Show what?', 'recras'); ?></label>
    <dd><select id="show_what" required>
            <option value="name"><?php esc_html_e('Name', 'recras'); ?>
            <option value="price"><?php esc_html_e('Price', 'recras'); ?>
            <option value="validity"><?php esc_html_e('Validity', 'recras'); ?>
        </select>
</dl>
<button class="button button-primary" id="voucher_submit"><?php esc_html_e('Insert shortcode', 'recras'); ?></button>

<script>
    document.getElementById('voucher_submit').addEventListener('click', function(){
        let shortcode = '[<?= esc_js(\Recras\Vouchers::SHORTCODE_INFO); ?>';
        shortcode += ' id="' + document.getElementById('template_id').value + '"';
        shortcode += ' show="' + document.getElementById('show_what').value + '"';
        shortcode += ']';

        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
        tb_remove();
    });
</script>
