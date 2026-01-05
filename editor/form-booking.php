<?php
$recras_instance = \Recras\Settings::getInstance();
if (!$recras_instance) {
    \Recras\Settings::errorNoRecrasName();
    return;
}

$recras_packages_model = new \Recras\Arrangement();
$recras_packages = $recras_packages_model->getPackages($recras_instance, true);
?>
<dl>
    <dt><label><?php esc_html_e('Integration method', 'recras'); ?></label>
        <dd>
            <label>
                <input type="radio" id="use_new_library_yes" name="integration_method" value="jslibrary" checked>
                <?php esc_html_e('Seamless (recommended)', 'recras'); ?>
            </label>
            <br>
            <label>
                <input type="radio" id="use_new_library_no" name="integration_method" value="iframe">
                <?php esc_html_e('iframe (uses setting in your Recras)', 'recras'); ?>
            </label>
        <p class="recras-notice">
            <?php
            esc_html_e('Seamless integration uses the styling of your website. At Recras → Settings in the menu on the left, you can set an optional theme.', 'recras');
            ?>
            <br>
            <?php
            esc_html_e('iframe integration uses the styling set in your Recras. You can change the styling in Recras via Settings → Other settings → Custom CSS.', 'recras');
            ?>
        </p>

    <dt id="pack_sel_label">
        <label for="package_selection"><?php esc_html_e('Package selection', 'recras'); ?></label>
    <dd id="pack_sel_input">
        <?php unset($recras_packages[0]); ?>
        <select multiple id="package_selection">
            <?php foreach ($recras_packages as $recras_package_id => $recras_package) { ?>
            <option value="<?= esc_html($recras_package_id); ?>"><?= esc_html($recras_package->arrangement); ?>
            <?php } ?>
        </select>
        <p class="recras-notice">
            <?php
            esc_html_e('To (de)select multiple packages, hold Ctrl and click (Cmd on Mac)', 'recras');
            ?>
        </p>
    <dt id="pack_one_label" style="display: none;">
        <label for="arrangement_id"><?php esc_html_e('Package', 'recras'); ?></label>
    <dd id="pack_one_input" style="display: none;">
        <?php if (is_string($recras_packages)) { ?>
            <input type="number" id="arrangement_id" min="0">
            <?= esc_html($recras_packages); ?>
        <?php } elseif(is_array($recras_packages)) { ?>
            <?php unset($recras_packages[0]); ?>
            <select id="arrangement_id" required>
                <option value="0"><?php esc_html_e('No pre-filled package', 'recras'); ?>
                <?php foreach ($recras_packages as $recras_package_id => $recras_package) { ?>
                <option value="<?= esc_html($recras_package_id); ?>"><?= esc_html($recras_package->arrangement); ?>
                <?php } ?>
            </select>
        <?php } ?>

    <dt><label for="show_times"><?php esc_html_e('Preview times in programme', 'recras'); ?></label>
        <dd><input type="checkbox" id="show_times">
    <dt><label><?php esc_html_e('Pre-fill amounts (requires pre-filled package)', 'recras'); ?></label>
        <dd><strong><?php esc_html_e('Sorry, this is only available using the Gutenberg editor.', 'recras'); ?></strong>
    <dt><label for="prefill_date"><?php esc_html_e('Pre-fill date (requires exactly 1 package selected)','recras' ); ?></label>
        <dd><input
            type="date"
            id="prefill_date"
            min="<?= esc_html(date('Y-m-d')); ?>"
            pattern="<?= esc_html(\Recras\ContactForm::PATTERN_DATE); ?>"
            placeholder="<?= esc_html(__('yyyy-mm-dd', 'recras')); ?>"
            disabled
        >
    <dt><label for="prefill_time"><?php esc_html_e('Pre-fill time (requires exactly 1 package selected)','recras' ); ?></label>
        <dd><input
            type="time"
            id="prefill_time"
            pattern="<?= esc_html(\Recras\ContactForm::PATTERN_TIME); ?>"
            step="300"
            placeholder="<?= esc_html(__('hh:mm', 'recras')); ?>"
            disabled
        >
    <dt><label for="redirect_page"><?php esc_html_e('Thank-you page', 'recras'); ?></label>
        <dd><select id="redirect_page">
            <option value=""><?php esc_html_e("Don't redirect", 'recras'); ?>
            <optgroup label="<?php esc_html_e('Pages', 'recras'); ?>">
                <?php foreach (get_pages() as $page) { ?>
                <option value="<?= esc_html(get_permalink($page->ID)); ?>"><?= esc_html($page->post_title); ?>
                <?php } ?>
            </optgroup>
            <optgroup label="<?php esc_html_e('Posts', 'recras'); ?>">
                <?php foreach (get_posts() as $post) { ?>
                <option value="<?= esc_html(get_permalink($post->ID)); ?>"><?= esc_html($post->post_title); ?>
                <?php } ?>
            </optgroup>
        </select>
    <dt><label for="show_discounts"><?php esc_html_e('Show discount fields', 'recras'); ?></label>
        <dd><input type="checkbox" id="show_discounts" checked>
    <dt><label for="auto_resize"><?php esc_html_e('Automatic resize?', 'recras'); ?></label>
        <dd><input type="checkbox" id="auto_resize" disabled>

</dl>
<button class="button button-primary" id="booking_submit"><?php esc_html_e('Insert shortcode', 'recras'); ?></button>

<script>
    [...document.querySelectorAll('[name="integration_method"]')].forEach(function(el) {
        el.addEventListener('change', function(){
            const useLibrary = document.getElementById('use_new_library_yes').checked;
            document.getElementById('auto_resize').disabled = useLibrary;
            document.getElementById('redirect_page').disabled = !useLibrary;
            document.getElementById('show_times').disabled = !useLibrary;
            document.getElementById('show_discounts').disabled = !useLibrary;

            document.getElementById('pack_sel_label').style.display = useLibrary ? 'block' : 'none';
            document.getElementById('pack_sel_input').style.display = useLibrary ? 'block' : 'none';
            document.getElementById('pack_one_label').style.display = useLibrary ? 'none' : 'block';
            document.getElementById('pack_one_input').style.display = useLibrary ? 'none' : 'block';
        });
    });
    document.getElementById('arrangement_id').addEventListener('change', function() {
        const hasPackage = this.value > 0;
        document.getElementById('prefill_date').disabled = !hasPackage;
        document.getElementById('prefill_time').disabled = !hasPackage;
    });
    document.getElementById('package_selection').addEventListener('change', function() {
        const selectedPackages = document.querySelectorAll('#package_selection option:checked');
        const hasPackage = selectedPackages.length === 1;
        document.getElementById('prefill_date').disabled = !hasPackage;
        document.getElementById('prefill_time').disabled = !hasPackage;
    });

    document.getElementById('booking_submit').addEventListener('click', function() {
        const useNewLibrary = document.getElementById('use_new_library_yes').checked;

        let arrangementID;
        let packageIDsMultiple = [];
        const selectedPackages = document.querySelectorAll('#package_selection option:checked');
        if (selectedPackages.length === 1) {
            arrangementID = selectedPackages[0].value;
        } else {
            packageIDsMultiple = [...selectedPackages].map(el => el.value);
        }
        let shortcode = '[<?= esc_js(\Recras\OnlineBooking::SHORTCODE); ?>';
        if (packageIDsMultiple.length > 0 && useNewLibrary) {
            shortcode += ' package_list="' + packageIDsMultiple.join(',') + '"';
        } else if (arrangementID) {
            shortcode += ' id="' + arrangementID + '"';
        }

        if (useNewLibrary) {
            shortcode += ' use_new_library=1';
            if (document.getElementById('redirect_page').value !== '') {
                shortcode += ' redirect="' + document.getElementById('redirect_page').value + '"';
            }
            if (document.getElementById('show_times').checked) {
                shortcode += ' show_times=1';
            }
            if (!document.getElementById('show_discounts').checked) {
                shortcode += ' showdiscount=0';
            }
        } else {
            if (!document.getElementById('auto_resize').checked) {
                shortcode += ' autoresize=0';
            }
        }

        if (arrangementID) {
            if (document.getElementById('prefill_date').value) {
                shortcode += ' prefill_date="' + document.getElementById('prefill_date').value + '"';
            }
            if (document.getElementById('prefill_time').value) {
                shortcode += ' prefill_time="' + document.getElementById('prefill_time').value + '"';
            }
        }
        shortcode += ']';

        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
        tb_remove();
    });
</script>
