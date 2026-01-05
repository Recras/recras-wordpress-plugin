<?php
    if (isset($_GET['msg'])) {
        if ($_GET['msg'] === 'success') {
?>
<div class="updated notice">
    <p><?php esc_html_e('The cache was cleared.', 'recras'); ?></p>
</div>
<?php
        } elseif ($_GET['msg'] === 'error') {
            ?>
<div class="error notice">
    <p><?php esc_html_e('The selected cache could not be cleared. This could be an error, or there could be nothing to clear.', 'recras'); ?></p>
</div>
            <?php
        }
    }
?>

<h1><?php esc_html_e('Clear Recras cache', 'recras'); ?></h1>

<p><?php esc_html_e('Data coming from your Recras (contact forms, packages, products, voucher templates) is cached for up to 24 hours. If you make important changes (i.e. a price increase) it is recommended you clear the Recras cache.', 'recras'); ?></p>

<form action="<?= admin_url('admin-post.php?action=clear_recras_cache'); ?>" method="POST">
    <input type="submit" value="<?php esc_html_e('Clear Recras cache', 'recras'); ?>">
</form>
