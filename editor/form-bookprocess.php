<?php
$recras_instance = \Recras\Settings::getInstance();
if (!$recras_instance) {
    \Recras\Settings::errorNoRecrasName();
    return;
}

$recras_bp_model = new \Recras\Bookprocess();
$recras_bps = $recras_bp_model->getProcesses($recras_instance);
?>
<style id="bookprocess_style">
    .recras-hidden-input { display: none; }
</style>

<dl>
    <dt><label for="bookprocess_id"><?php esc_html_e('Book process', 'recras'); ?></label>
        <dd><?php if (is_string($recras_bps)) { ?>
            <input type="number" id="bookprocess_id" min="1" required>
            <?= esc_html($recras_bps); ?>
        <?php } elseif (is_array($recras_bps)) { ?>
            <select id="bookprocess_id" required>
                <?php foreach ($recras_bps as $recras_bp_id => $recras_bp) { ?>
                <option value="<?= esc_html($recras_bp_id); ?>"><?= esc_html($recras_bp->name); ?>
                <?php } ?>
            </select>
        <?php } ?>
    <dt class="first-widget-only recras-hidden-input">
        <label><?php esc_html_e('Initial value for first widget', 'recras'); ?></label>
        <dd class="first-widget-only recras-hidden-input">
            <input id="first_widget_value_package" type="number" min="1" step="1">
            <p class="recras-notice">
                <?php esc_html_e('Please note that no validation on this value is performed. Invalid values may be ignored or may stop the book process from working properly.', 'recras'); ?>
            </p>
    <dt class="first-widget-only recras-hidden-input">
        <label for="hide_first_widget"><?php esc_html_e('Hide first widget?', 'recras'); ?></label>
        <dd class="first-widget-only recras-hidden-input">
            <input type="checkbox" id="hide_first_widget">
</dl>
<button class="button button-primary" id="bp_submit"><?php esc_html_e('Insert shortcode', 'recras'); ?></button>

<script>
    function bpIdChange () {
        const elPackage = document.getElementById('first_widget_value_package');
        const elId = document.getElementById('bookprocess_id');
        <?php
        if (is_array($recras_bps)) {
        ?>
        const bookprocesses = <?= json_encode($recras_bps); ?>;
        <?php
        }
        ?>
        const toggleEls = [...document.querySelectorAll('.first-widget-only')];
        const hideToggleEls = function () {
            for (let el of toggleEls) {
                el.classList.add('recras-hidden-input');
            }
        };
        const showToggleEls = function () {
            for (let el of toggleEls) {
                el.classList.remove('recras-hidden-input');
            }
        };

        if (bookprocesses && bookprocesses[elId.value]) {
            switch (bookprocesses[elId.value].firstWidget) {
                case 'package':
                    showToggleEls();
                    elPackage.style.display = 'inline-block';
                    break;
                default:
                    hideToggleEls();
            }
        } else {
            hideToggleEls();
        }
    }

    document.getElementById('bookprocess_id').addEventListener('change', bpIdChange);
    bpIdChange();

    document.getElementById('bp_submit').addEventListener('click', function() {
        const elPackage = document.getElementById('first_widget_value_package');

        let shortcode = '[<?= esc_js(\Recras\Bookprocess::SHORTCODE); ?> id="' + document.getElementById('bookprocess_id').value + '"';

        let initialValue;
        if (elPackage && elPackage.value) {
            initialValue = elPackage.value;
        }
        if (initialValue) {
            shortcode += ' initial_widget_value="' + initialValue + '"';
            if (document.getElementById('hide_first_widget').checked) {
                shortcode += ' hide_first_widget="yes"';
            }
        }

        shortcode += ']';

        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
        tb_remove();
    });
</script>
