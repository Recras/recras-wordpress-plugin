<?php
namespace Recras;

class Settings
{
    public static function addInputCurrency($args)
    {
        $field = $args['field'];
        $value = get_option($field);
        if (!$value) {
            $value = '€';
        }

        printf('<input type="text" name="%s" id="%s" value="%s">', $field, $field, $value);
    }

    public static function addInputSubdomain($args)
    {
        $field = $args['field'];
        $value = get_option($field);
        if (!$value) {
            $value = 'demo';
        }

        printf('<input type="text" name="%s" id="%s" value="%s">', $field, $field, $value);
    }

    public static function editSettings()
    {
        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        require_once('admin/settings.php');
    }

    public static function registerSettings()
    {
        add_settings_section(
            'recras',
            'Recras Settings',
            ['Recras\Settings', 'settingsHelp'],
            'recras'
        );

        register_setting('recras', 'recras_subdomain', ['Recras\Settings', 'sanitizeSubdomain']);
        register_setting('recras', 'recras_currency', '');

        add_settings_field('recras_subdomain', __('Subdomain', Plugin::TEXT_DOMAIN), ['Recras\Settings', 'addInputSubdomain'], 'recras', 'recras', ['field' => 'recras_subdomain']);
        add_settings_field('recras_currency', __('Currency symbol', Plugin::TEXT_DOMAIN), ['Recras\Settings', 'addInputCurrency'], 'recras', 'recras', ['field' => 'recras_currency']);
    }

    public function sanitizeSubdomain($subdomain)
    {
        // RFC 1034 section 3.5 - http://tools.ietf.org/html/rfc1034#section-3.5
        if (strlen($subdomain) > 63) {
            return false;
        }
        if (! preg_match('/^[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?$/', $subdomain)) {
            return false;
        }
        return $subdomain;
    }

    public static function settingsHelp()
    {
        _e('Enter your Recras details here', Plugin::TEXT_DOMAIN);
    }
}