<h1><?php esc_html_e('Recras settings', 'recras'); ?></h1>

<form action="options.php" method="POST">
<?php
    settings_fields('recras');
    do_settings_sections('recras');
    submit_button(__('Save', 'recras'));
?>
</form>
