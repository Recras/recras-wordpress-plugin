<?php
namespace Recras;

class Settings
{
    public const OPTION_PAGE = 'recras';
    private const OPTION_SECTION = 'recras';
    public const PAGE_CACHE = 'recras-clear-cache';
    public const PAGE_DOCS = 'recras-documentation';
    public const PAGE_SHORTCODES = 'recras-shortcodes';


    public static function addInputAnalytics(array $args): void
    {
        self::addInputCheckbox($args);
        self::infoText(
            __('This option is <strong>not needed when using book processes</strong>. GA is integrated automatically for them.', Plugin::TEXT_DOMAIN)
        );
    }


    /**
     * Add a currency input field
     */
    public static function addInputCurrency(array $args): void
    {
        $field = $args['field'];
        $value = get_option($field);
        if (!$value) {
            $value = '€';
        }

        printf('<input type="text" name="%s" id="%s" value="%s">', $field, $field, $value);
    }


    /**
     * Add a checkbox option
     */
    public static function addInputCheckbox(array $args): void
    {
        $field = $args['field'];
        $value = get_option($field);

        printf('<input type="checkbox" name="%s" id="%s" value="1"%s>', $field, $field, ($value ? ' checked' : ''));
    }

    public static function addInputDatepicker(array $args): void
    {
        self::addInputCheckbox($args);
        self::infoText(__('Use this setting if you want to be able to style the date picker in contact forms.', Plugin::TEXT_DOMAIN));
    }

    public static function addInputFixDatepicker(array $args): void
    {
        self::addInputCheckbox($args);
        self::infoText(__('On some websites, the date picker in a book process has a tiny font. Enable this to fix this.', Plugin::TEXT_DOMAIN));
    }


    /**
     * Add a decimal separator input field
     */
    public static function addInputDecimal(array $args): void
    {
        $field = $args['field'];
        $value = get_option($field);
        if (!$value) {
            $value = '.';
        }

        printf('<input type="text" name="%s" id="%s" value="%s" size="2" maxlength="1">', $field, $field, $value);
        self::infoText(__('Used in prices, such as 100,00.', Plugin::TEXT_DOMAIN));
    }


    /**
     * Add a subdomain input field
     */
    public static function addInputDomain(array $args): void
    {
        $field = $args['field'];
        $value = get_option($field);
        if (!$value) {
            $value = 'demo';
        }

        printf('<input type="text" name="%s" id="%s" value="%s" placeholder="demo.recras.nl">', $field, $field, $value);
        $arr = wp_remote_get('https://' . $value . '/');
        if ($arr instanceof \WP_Error) {
            self::infoText(__('Instance not found!', Plugin::TEXT_DOMAIN), 'recrasAdminError');
        } elseif (is_array($arr) && isset($arr['http_response']) && $arr['http_response'] instanceof \WP_HTTP_Requests_Response) {
            if ($arr['http_response']->get_status() === 404) {
                self::infoText(__('Error fetching instance!', Plugin::TEXT_DOMAIN), 'recrasAdminError');
            }
        }
    }


    public static function addInputTheme(array $args): void
    {
        $themes = self::getThemes();

        $field = $args['field'];
        $value = get_option($field);
        if (!$value) {
            $value = 'none';
        }

        $html = '<select name="' . $field . '" id="' . $field . '">';
        foreach ($themes as $key => $theme) {
            $selText = '';
            if ($value === $key) {
                $selText = ' selected';
            }
            $html .= '<option value="' . $key . '"' . $selText . '>' . $theme['name'];
        }
        $html .= '</select>';
        echo $html;
    }


    public static function allowOnlinePackageBooking(): bool
    {
        $subdomain = get_option('recras_subdomain');
        if (!$subdomain) {
            return true;
        }
        $setting = Transient::get($subdomain . '_show_old_online_booking');
        // if getting the transient fails, we want to show the button to be sure, so comparing with 'no' is safest
        return ($setting === 'no') ? false : true;
    }


    public static function allowOldVoucherSales(): bool
    {
        $subdomain = get_option('recras_subdomain');
        if (!$subdomain) {
            return true;
        }
        $setting = Transient::get($subdomain . '_show_old_voucher_sales');
        // if getting the transient fails, we want to show the button to be sure, so comparing with 'no' is safest
        return ($setting === 'no') ? false : true;
    }

    /**
     * Clear voucher template cache (transients)
     */
    public static function clearCache(): int
    {
        $subdomain = get_option('recras_subdomain');
        $errors = 0;
        if (Transient::get($subdomain . '_show_old_online_booking')) {
            $errors = Transient::delete($subdomain . '_show_old_online_booking');
        }
        if (Transient::get($subdomain . '_show_old_voucher_sales')) {
            $errors = Transient::delete($subdomain . '_show_old_voucher_sales');
        }

        return $errors;
    }

    /**
     * Admin page to clear the cache
     */
    public static function clearCachePage(): void
    {
        if (!current_user_can('edit_pages')) {
            wp_die(__('You do not have sufficient permissions to access this page.'), '', 401);
        }
        require_once(__DIR__ . '/admin/cache.php');
    }


    public static function documentation(): void
    {
        if (!current_user_can('edit_pages')) {
            wp_die(__('You do not have sufficient permissions to access this page.'), '', 401);
        }
        require_once(__DIR__ . '/admin/documentation.php');
    }


    public static function shortcodes(): void
    {
        if (!current_user_can('edit_pages')) {
            wp_die(__('You do not have sufficient permissions to access this page.'), '', 401);
        }
        require_once(__DIR__ . '/admin/shortcodes.php');
    }


    /**
     * Load the admin options page
     */
    public static function editSettings(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'), '', 401);
        }
        require_once(__DIR__ . '/admin/settings.php');
    }


    public static function errorNoRecrasName(): void
    {
        echo '<p class="recrasInfoText">';
        $settingsLink = admin_url('admin.php?page=' . self::OPTION_PAGE);
        printf(
            __('Please enter your Recras name in the %s before adding widgets.', Plugin::TEXT_DOMAIN),
            '<a href="' . $settingsLink . '" target="_blank">' . __('Recras → Settings menu', Plugin::TEXT_DOMAIN) . '</a>'
        );
        echo '</p>';
    }

    /**
     * This returns a valid locale, based on the locale set for WordPress, to use in "external" Recras scripts
     */
    public static function externalLocale(): string
    {
        $localeShort = substr(get_locale(), 0, 2);
        switch ($localeShort) {
            case 'de':
                return 'de_DE';
            case 'fy':
            case 'nl':
                return 'nl_NL';
            default:
                return 'en_GB';
        }
    }


    /**
     * Get the Recras instance, which can be set in the shortcode attributes or as global setting
     */
    public static function getInstance(array $attributes): string
    {
        if (isset($attributes['recrasname'])) {
            $name = $attributes['recrasname'];
            if (strpos($name, '.recras.') === false) {
                $name .= '.recras.nl';
            }
            return $name;
        }
        $domain = get_option('recras_domain');
        if ($domain) {
            return $domain;
        }
        $subdomain = get_option('recras_subdomain');
        if ($subdomain) {
            $domain = $subdomain . '.recras.nl';
            update_option('recras_domain', $domain);
            return $domain;
        }
        return '';
    }


    public static function getThemes(): array
    {
        return [
            'none' => [
                'name' => __('No theme', Plugin::TEXT_DOMAIN),
                'version' => null,
            ],
            'basic' => [
                'name' => __('Basic theme', Plugin::TEXT_DOMAIN),
                'version' => '5.5.0',
            ],
            'bpgreen' => [
                'name' => __('BP Green', Plugin::TEXT_DOMAIN),
                'version' => '5.5.0',
            ],
            'reasonablyred' => [
                'name' => __('Reasonably Red', Plugin::TEXT_DOMAIN),
                'version' => '5.5.0',
            ],
            'recrasblue' => [
                'name' => __('Recras Blue', Plugin::TEXT_DOMAIN),
                'version' => '5.5.0',
            ],
        ];
    }


    private static function infoText(string $text, string $extraClass = ''): void
    {
        echo '<p class="description' . ($extraClass ? ' ' . $extraClass : '') . '">' . $text . '</p>';
    }


    /**
     * Parse a boolean value
     * @param bool|string $value
     */
    public static function parseBoolean($value): bool
    {
        $falseValues = [false, 'false', 0, '0', 'no'];
        if (isset($value) && in_array($value, $falseValues, true)) {
            // Without strict=true, in_array(true, $falseValues) is true!
            return false;
        }
        return true;
    }

    private static function registerSetting(string $name, $default, string $type = 'string', callable $sanitizeCallback = null): void
    {
        $options = [
            'default' => $default,
            'type' => $type,
        ];
        if ($sanitizeCallback) {
            $options['sanitize_callback'] = $sanitizeCallback;
        }
        register_setting('recras', $name, $options);
    }

    private static function addField(string $name, string $title, callable $inputFn): void
    {
        add_settings_field($name, $title, $inputFn, self::OPTION_PAGE, self::OPTION_SECTION, [
            'field' => $name,
            'label_for' => $name,
        ]);
    }

    public static function maybeUpdateSettings(): void
    {
        $domain = get_option('recras_domain');
        if ($domain) {
            return;
        }

        $subdomain = get_option('recras_subdomain');
        if ($subdomain) {
            update_option('recras_domain', $subdomain . '.recras.nl');
            delete_option('recras_subdomain');
        }
    }

    /**
     * Register plugin settings
     */
    public static function registerSettings(): void
    {
        self::registerSetting('recras_subdomain', 'demo'); // Legacy since 2025-09
        self::registerSetting('recras_domain', 'demo.recras.nl', 'string', [__CLASS__, 'sanitizeDomain']);
        self::registerSetting('recras_currency', '€');
        self::registerSetting('recras_decimal', ',');
        self::registerSetting('recras_datetimepicker', false, 'boolean');
        self::registerSetting('recras_fix_react_datepicker', false, 'boolean');
        self::registerSetting('recras_theme', 'basic');
        self::registerSetting('recras_enable_analytics', false, 'boolean');
    }


    public static function registerSettingsPage(): void
    {
        \add_settings_section(
            self::OPTION_SECTION,
            __('Recras settings', Plugin::TEXT_DOMAIN),
            [__CLASS__, 'settingsHelp'],
            self::OPTION_PAGE
        );
        self::registerSettings();

        self::addField('recras_domain', __('Recras domain', Plugin::TEXT_DOMAIN), [__CLASS__, 'addInputDomain']);
        self::addField('recras_currency', __('Currency symbol', Plugin::TEXT_DOMAIN), [__CLASS__, 'addInputCurrency']);
        self::addField('recras_decimal', __('Decimal separator', Plugin::TEXT_DOMAIN), [__CLASS__, 'addInputDecimal']);
        self::addField('recras_datetimepicker', __('Use calendar widget for contact forms', Plugin::TEXT_DOMAIN), [__CLASS__, 'addInputDatepicker']);
        self::addField('recras_fix_react_datepicker', __('Fix book process datepicker styling', Plugin::TEXT_DOMAIN), [__CLASS__, 'addInputFixDatepicker']);
        self::addField('recras_theme', __('Theme for Recras integrations', Plugin::TEXT_DOMAIN), [__CLASS__, 'addInputTheme']);
        self::addField('recras_enable_analytics', __('Enable Google Analytics integration? (deprecated)', Plugin::TEXT_DOMAIN), [__CLASS__, 'addInputAnalytics']);
    }



    /**
     * Sanitize user inputted subdomain
     *
     * @return string|false
     */
    public static function sanitizeDomain(string $domain)
    {
        $subdomainRegex = '^[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?';
        $domainRegex = '\.recras\.(nl|com)$';
        if (!preg_match('/' . $subdomainRegex . $domainRegex . '/', $domain)) {
            return false;
        }
        return $domain;
    }


    /**
     * Echo settings helper text
     */
    public static function settingsHelp(): void
    {
        printf(
            __('For more information on these options, please see the %s page.', Plugin::TEXT_DOMAIN),
            '<a href="' . admin_url('admin.php?page=' . self::PAGE_DOCS) . '">' . __('Documentation', Plugin::TEXT_DOMAIN) . '</a>'
        );
    }
}
