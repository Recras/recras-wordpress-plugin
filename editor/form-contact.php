<?php
$recras_instance = \Recras\Settings::getInstance();
if (!$recras_instance) {
    \Recras\Settings::errorNoRecrasName();
    return;
}

$recras_packages_model = new \Recras\Arrangement();
$recras_packages = $recras_packages_model->getPackages($recras_instance);

$recras_forms_model = new \Recras\ContactForm();
$recras_forms = $recras_forms_model->getForms($recras_instance);
?>
<dl>
    <dt><label for="contactform_id"><?php esc_html_e('Contact form', 'recras'); ?></label>
        <dd><?php if (is_string($recras_forms)) { ?>
            <input type="number" id="contactform_id" min="0" required>
            <?= esc_html($recras_forms); ?>
        <?php } elseif (is_array($recras_forms)) { ?>
            <select id="contactform_id" required>
                <?php foreach ($recras_forms as $recras_form_id => $recras_form) { ?>
                <option value="<?= esc_html($recras_form_id); ?>"><?= esc_html($recras_form->naam); ?>
                <?php } ?>
            </select>
        <?php } ?>
    <dt><label for="showtitle"><?php esc_html_e('Show title?', 'recras'); ?></label>
        <dd><input type="checkbox" id="showtitle" checked>
    <dt><label for="showlabels"><?php esc_html_e('Show labels?', 'recras'); ?></label>
        <dd><input type="checkbox" id="showlabels" checked>
    <dt><label for="showplaceholders"><?php esc_html_e('Show placeholders?', 'recras'); ?></label>
        <dd><input type="checkbox" id="showplaceholders" checked>
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
        <?php } ?>
        <p class="recras-notice">
            <?php esc_html_e('Some packages may not be available for all contact forms. You can change this by editing your contact forms in Recras.', 'recras'); ?><br>
            <?php esc_html_e('If you are still missing packages, make sure in Recras "May be presented on a website (via API)" is enabled on the tab "Extra settings" of the package.', 'recras'); ?>
        </p>
    <dt><label for="container_element"><?php esc_html_e('HTML element', 'recras'); ?></label>
        <dd><select id="container_element">
                <option value="dl" selected><?php esc_html_e('Definition list', 'recras'); ?> (&lt;dl&gt;)
                <option value="ol"><?php esc_html_e('Ordered list', 'recras'); ?> (&lt;ol&gt;)
                <option value="table"><?php esc_html_e('Table', 'recras'); ?> (&lt;table&gt;)
            </select>
    <dt><label for="single_choice_element"><?php esc_html_e('Element for single choices', 'recras'); ?></label>
        <dd><select id="single_choice_element">
                <option value="select" selected><?php esc_html_e('Drop-down list (Select)', 'recras'); ?>
                <option value="radio"><?php esc_html_e('Radio buttons', 'recras'); ?>
            </select>
        <p class="recras-notice">
            <?php esc_html_e('This relates to: customer type, package selection, gender, and single choice', 'recras'); ?><br>
        </p>
    <dt><label for="submit_text"><?php esc_html_e('Submit button text', 'recras'); ?></label>
        <dd><input type="text" id="submit_text" value="<?php esc_html_e('Send', 'recras'); ?>">
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
</dl>
<button class="button button-primary" id="contact_submit"><?php esc_html_e('Insert shortcode', 'recras'); ?></button>

<script>
    const DEFAULT_ELEMENT = 'dl';
    const DEFAULT_SINGLE_CHOICE_ELEMENT = 'select';

    // Check which arrangements are available
    getContactFormArrangements(document.getElementById('contactform_id').value, '<?= esc_js($recras_instance); ?>');
    document.getElementById('contactform_id').addEventListener('change', function(){
        getContactFormArrangements(document.getElementById('contactform_id').value, '<?= esc_js($recras_instance); ?>');
    });

    document.getElementById('contact_submit').addEventListener('click', function(){
        let shortcode = '[<?= esc_js(\Recras\ContactForm::SHORTCODE); ?> id="' + document.getElementById('contactform_id').value + '"';

        const options = ['showtitle', 'showlabels', 'showplaceholders'];
        for (let opt of options) {
            if (!document.getElementById(opt).checked) {
                shortcode += ' ' + opt + '="no"';
            }
        }

        if (document.getElementById('arrangement_id').value > 0) {
            shortcode += ' arrangement="' + document.getElementById('arrangement_id').value + '"';
        }
        if (document.getElementById('container_element').value !== DEFAULT_ELEMENT) {
            shortcode += ' element="' + document.getElementById('container_element').value + '"';
        }
        if (document.getElementById('single_choice_element').value !== DEFAULT_SINGLE_CHOICE_ELEMENT) {
            shortcode += ' single_choice_element="' + document.getElementById('single_choice_element').value + '"';
        }
        if (document.getElementById('submit_text').value !== '<?php esc_html_e('Send', 'recras'); ?>') {
            shortcode += ' submittext="' + document.getElementById('submit_text').value + '"';
        }
        if (document.getElementById('redirect_page').value !== '') {
            shortcode += ' redirect="' + document.getElementById('redirect_page').value + '"';
        }

        shortcode += ']';

        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
        tb_remove();
    });
</script>
