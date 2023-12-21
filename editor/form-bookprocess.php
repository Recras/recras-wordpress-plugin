<?php
$subdomain = get_option('recras_subdomain');
if (!$subdomain) {
    \Recras\Settings::errorNoRecrasName();
    return;
}

$model = new \Recras\Bookprocess();
$processes = $model->getProcesses($subdomain);
?>
<style id="bookprocess_style">
    .recras-hidden-input { display: none; }
</style>

<dl>
    <dt><label for="bookprocess_id"><?php _e('Book process', \Recras\Plugin::TEXT_DOMAIN); ?></label>
        <dd><?php if (is_string($processes)) { ?>
            <input type="number" id="bookprocess_id" min="1" required>
            <?= $processes; ?>
        <?php } elseif (is_array($processes)) { ?>
            <select id="bookprocess_id" required>
                <?php foreach ($processes as $ID => $process) { ?>
                <option value="<?= $ID; ?>"><?= $process->name; ?>
                <?php } ?>
            </select>
        <?php } ?>
    <dt class="first-widget-only recras-hidden-input">
        <label><?php _e('Initial value for first widget', \Recras\Plugin::TEXT_DOMAIN); ?></label>
        <dd class="first-widget-only recras-hidden-input">
            <input id="first_widget_value_package" type="number" min="1" step="1">
            <p class="recras-notice">
                <?php _e('Please note that no validation on this value is performed. Invalid values may be ignored or may stop the book process from working properly.', \Recras\Plugin::TEXT_DOMAIN); ?>
            </p>
    <dt class="first-widget-only recras-hidden-input">
        <label for="hide_first_widget"><?php _e('Hide first widget?', \Recras\Plugin::TEXT_DOMAIN); ?></label>
        <dd class="first-widget-only recras-hidden-input">
            <input type="checkbox" id="hide_first_widget">
</dl>
<button class="button button-primary" id="bp_submit"><?php _e('Insert shortcode', \Recras\Plugin::TEXT_DOMAIN); ?></button>

<script>
    function bpIdChange () {
        const elPackage = document.getElementById('first_widget_value_package');
        const elId = document.getElementById('bookprocess_id');
        <?php
        if (is_array($processes)) {
        ?>
        const bookprocesses = <?= json_encode($processes); ?>;
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
                    elDate.style.display = 'none';
                    elDate.value = '';
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

        let shortcode = '[<?= \Recras\Plugin::SHORTCODE_BOOK_PROCESS; ?> id="' + document.getElementById('bookprocess_id').value + '"';

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
