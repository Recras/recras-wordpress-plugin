<?php
$instance = \Recras\Settings::getInstance();
if (!$instance) {
    \Recras\Settings::errorNoRecrasName();
    return;
}

$model = new \Recras\Products();
$products = $model->getProducts($instance);
?>

<dl>
    <dt><label for="product_id"><?php esc_html_e('Product', 'recras'); ?></label>
    <dd><?php if (is_string($products)) { ?>
            <input type="number" id="product_id" min="0" required>
            <?= $products; ?>
        <?php } elseif(is_array($products)) { ?>
            <select id="product_id" required>
            <?php foreach ($products as $ID => $product) { ?>
                <option value="<?= $ID; ?>"><?= $product->weergavenaam ? $product->weergavenaam : $product->naam; ?>
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
