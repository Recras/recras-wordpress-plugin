<?php
$recras_instance = \Recras\Settings::getInstance();
if (!$recras_instance) {
    \Recras\Settings::errorNoRecrasName();
    return;
}

$recras_packages_model = new \Recras\Arrangement();
$recras_packages = $recras_packages_model->getPackages($recras_instance);
?>
<style id="arrangement_style">
    .programme-only { display: none; }
</style>

<dl>
    <dt><label for="arrangement_id"><?php esc_html_e('Package', 'recras'); ?></label>
        <dd><?php if (is_string($recras_packages)) { ?>
            <input type="number" id="arrangement_id" min="0" required>
            <?= esc_html($recras_packages); ?>
        <?php } elseif (is_array($recras_packages)) { ?>
            <select id="arrangement_id" required>
            <?php foreach ($recras_packages as $recras_package_id => $recras_package) { ?>
                <option value="<?= esc_html($recras_package_id); ?>"><?= esc_html($recras_package->arrangement); ?>
            <?php } ?>
            </select>
            <p><?php esc_html_e('If you are not seeing certain packages, make sure in Recras "May be presented on a website (via API)" is enabled on the tab "Extra settings" of the package.', 'recras'); ?></p>
        <?php } ?>
    <dt><label for="show_what"><?php esc_html_e('Show what?', 'recras'); ?></label>
        <dd><select id="show_what" required>
            <option value="title"><?php esc_html_e('Title', 'recras'); ?>
            <option value="description"><?php esc_html_e('Description', 'recras'); ?>
            <option value="duration"><?php esc_html_e('Duration', 'recras'); ?>
            <option value="location"><?php esc_html_e('Starting location', 'recras'); ?>
            <option value="persons"><?php esc_html_e('Minimum number of persons', 'recras'); ?>
            <option value="price_pp_excl_vat"><?php esc_html_e('Price p.p. excl. VAT', 'recras'); ?>
            <option value="price_pp_incl_vat"><?php esc_html_e('Price p.p. incl. VAT', 'recras'); ?>
            <option value="price_total_excl_vat"><?php esc_html_e('Total price excl. VAT', 'recras'); ?>
            <option value="price_total_incl_vat"><?php esc_html_e('Total price incl. VAT', 'recras'); ?>
            <option value="programme"><?php esc_html_e('Programme', 'recras'); ?>
            <option value="image_tag"><?php esc_html_e('Image tag', 'recras'); ?>
            <option value="image_url"><?php esc_html_e('Relative image URL', 'recras'); ?>
        </select>
    <dt class="programme-only"><label for="starttime"><?php esc_html_e('Start time', 'recras'); ?></label>
        <dd class="programme-only"><input type="text" id="starttime" pattern="[01][0-9]:[0-5][1-9]" placeholder="<?php esc_html_e('hh:mm', 'recras'); ?>" value="00:00">
    <dt class="programme-only"><?php esc_html_e('Show header?', 'recras'); ?>
        <dd class="programme-only">
            <input type="radio" name="header" value="yes" id="header_yes" checked><label for="header_yes"><?php esc_html_e('Yes', 'recras'); ?></label><br>
            <input type="radio" name="header" value="no" id="header_no"><label for="header_no"><?php esc_html_e('No', 'recras'); ?></label>
</dl>
<button class="button button-primary" id="arrangement_submit"><?php esc_html_e('Insert shortcode', 'recras'); ?></button>

<script>
    document.getElementById('show_what').addEventListener('change', function(){
        document.getElementById('arrangement_style').innerHTML = (document.getElementById('show_what').value === 'programme' ? '' : '.programme-only { display: none; }');
    });

    document.getElementById('arrangement_submit').addEventListener('click', function(){
        let shortcode = '[<?= esc_js(\Recras\Arrangement::SHORTCODE); ?> id="' + document.getElementById('arrangement_id').value + '" show="' + document.getElementById('show_what').value + '"';
        if (document.getElementById('show_what').value === 'programme') {
            if (document.getElementById('starttime').value !== '00:00') {
                shortcode += ' starttime="' + document.getElementById('starttime').value + '"';
            }
            if (document.getElementById('header_no').checked) {
                shortcode += ' showheader="no"';
            }
        }
        shortcode += ']';

        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
        tb_remove();
    });
</script>
