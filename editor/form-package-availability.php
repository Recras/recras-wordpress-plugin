<?php
$recras_instance = \Recras\Settings::getInstance();
if (!$recras_instance) {
    \Recras\Settings::errorNoRecrasName();
    return;
}

$recras_package_model = new \Recras\Arrangement();
$recras_packages = $recras_package_model->getPackages($recras_instance);
?>

<dl>
    <dt><label for="arrangement_id"><?php esc_html_e('Package', 'recras'); ?></label>
    <dd><?php if (is_string($recras_packages)) { ?>
            <input type="number" id="arrangement_id" min="0" required>
            <?= $recras_packages; ?>
        <?php } elseif(is_array($recras_packages)) { ?>
            <select id="arrangement_id" required>
            <?php
                foreach ($recras_packages as $recras_package_id => $recras_package) {
                    if (!$recras_package->mag_beschikbaarheidskalender_api) {
                        continue;
                    }
                ?>
                <option value="<?= $recras_package_id; ?>"><?= $recras_package->arrangement; ?>
                <?php
                }
                ?>
            </select>
        <?php } ?>
    <dt><label for="auto_resize"><?php esc_html_e('Automatic resize?', 'recras'); ?></label>
        <dd><input type="checkbox" id="auto_resize" checked>
</dl>
<button class="button button-primary" id="arrangement_submit"><?php esc_html_e('Insert shortcode', 'recras'); ?></button>

<script>
    document.getElementById('arrangement_submit').addEventListener('click', function(){
        let shortcode = '[<?= \Recras\Availability::SHORTCODE; ?> id="' + document.getElementById('arrangement_id').value + '"';
        if (!document.getElementById('auto_resize').checked) {
            shortcode += ' autoresize=0';
        }
        shortcode += ']';

        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
        tb_remove();
    });
</script>
