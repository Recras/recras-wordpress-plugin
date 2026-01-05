<?php
$instance = \Recras\Settings::getInstance();
if (!$instance) {
    \Recras\Settings::errorNoRecrasName();
    return;
}

$model = new \Recras\Vouchers();
$templates = $model->getTemplates($instance);
?>

<dl>
    <dt><label for="template_id"><?php esc_html_e('Template', 'recras'); ?></label>
    <dd><?php if (is_string($templates)) { ?>
            <input type="number" id="template_id" min="0">
            <?= $templates; ?>
        <?php } elseif(is_array($templates)) { ?>
            <select id="template_id" required>
                <option value="0"><?php esc_html_e('No pre-filled template', 'recras'); ?>
                <?php foreach ($templates as $ID => $template) { ?>
                <option value="<?= $ID; ?>"><?= $template->name; ?>
                <?php } ?>
            </select>
        <?php } ?>
    <dt><label for="redirect_page"><?php esc_html_e('Thank-you page', 'recras'); ?></label>
    <dd><select id="redirect_page">
            <option value=""><?php esc_html_e("Don't redirect", 'recras'); ?>
            <optgroup label="<?php esc_html_e('Pages', 'recras'); ?>">
                <?php foreach (get_pages() as $page) { ?>
                <option value="<?= get_permalink($page->ID); ?>"><?= htmlspecialchars($page->post_title); ?>
                <?php } ?>
            </optgroup>
            <optgroup label="<?php esc_html_e('Posts', 'recras'); ?>">
                <?php foreach (get_posts() as $post) { ?>
                <option value="<?= get_permalink($post->ID); ?>"><?= htmlspecialchars($post->post_title); ?>
                <?php } ?>
            </optgroup>
        </select>
    <dt><label for="show_quantity"><?php esc_html_e('Show quantity input (will be set to 1 if not shown)', 'recras'); ?></label>
    <dd><input type="checkbox" id="show_quantity" checked>
</dl>
<button class="button button-primary" id="voucher_submit"><?php esc_html_e('Insert shortcode', 'recras'); ?></button>

<script>
    document.getElementById('voucher_submit').addEventListener('click', function(){
        const templateID = document.getElementById('template_id').value;
        let shortcode = '[<?= \Recras\Vouchers::SHORTCODE_SALES; ?>';

        if (templateID !== '0') {
            shortcode += ' id="' + templateID + '"';
        }

        if (document.getElementById('redirect_page').value !== '') {
            shortcode += ' redirect="' + document.getElementById('redirect_page').value + '"';
        }

        if (!document.getElementById('show_quantity').checked) {
            shortcode += ' showquantity=0';
        }

        shortcode += ']';

        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
        tb_remove();
    });
</script>
