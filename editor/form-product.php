<?php
$recras_instance = \Recras\Settings::getInstance();
if (!$recras_instance) {
    \Recras\Settings::errorNoRecrasName();
    return;
}

$recras_products_model = new \Recras\Products();
$recras_products = $recras_products_model->getProducts($recras_instance);
?>

<dl>
    <dt><label for="product_id"><?php esc_html_e('Product', 'recras'); ?></label>
    <dd><?php if (is_string($recras_products)) { ?>
            <input type="number" id="product_id" min="0" required>
            <?= $recras_products; ?>
        <?php } elseif(is_array($recras_products)) { ?>
            <select id="product_id" required>
            <?php foreach ($recras_products as $recras_product_id => $recras_product) { ?>
                <option value="<?= $recras_product_id; ?>"><?= $recras_product->weergavenaam ?: $recras_product->naam; ?>
            <?php } ?>
            </select>
        <?php } ?>
    <dt><label for="show_what"><?php esc_html_e('Show what?', 'recras'); ?></label>
    <dd><select id="show_what" required>
            <option value="title"><?php esc_html_e('Title', 'recras'); ?>
            <option value="description"><?php esc_html_e('Description (short)', 'recras'); ?>
            <option value="description_long"><?php esc_html_e('Description (long)', 'recras'); ?>
            <option value="duration"><?php esc_html_e('Duration', 'recras'); ?>
            <option value="image_tag"><?php esc_html_e('Image tag', 'recras'); ?>
            <option value="image_url"><?php esc_html_e('Image URL', 'recras'); ?>
            <option value="minimum_amount"><?php esc_html_e('Minimum amount', 'recras'); ?>
            <option value="price_incl_vat"><?php esc_html_e('Price (incl. VAT)', 'recras'); ?>
        </select>
</dl>
<button class="button button-primary" id="product_submit"><?php esc_html_e('Insert shortcode', 'recras'); ?></button>

<script>
    document.getElementById('product_submit').addEventListener('click', function(){
        const shortcode = '[<?= \Recras\Products::SHORTCODE; ?> id="' +
            document.getElementById('product_id').value + '" show="' +
            document.getElementById('show_what').value + '"]';

        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
        tb_remove();
    });
</script>
