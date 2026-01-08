<?php
namespace Recras;

class Plugin
{
    private const LIBRARY_VERSION = '2.0.6';
    public const TEXT_DOMAIN = 'recras';

    public string $baseUrl;
    public Transient $transients;

    /**
     * Init all the things!
     */
    public function __construct()
    {
        $this->setBaseUrl();

        // Needs to run before: Gutenberg::addBlocks, self::loadAdminScripts, self::loadScripts
        $this->checkOldSettings();

        // Init Localisation
        load_default_textdomain();
        load_plugin_textdomain('recras', false, dirname(plugin_basename(__DIR__)) . '/lang');

        // Add admin menu pages
        add_action('admin_menu', [&$this, 'addMenuItems']);

        // Settings & page
        add_action('init', [Settings::class, 'registerSettings']);
        add_action('admin_init', [Settings::class, 'registerSettingsPage']);
        add_action('admin_init', [Editor::class, 'addButtons']);
        add_action('plugins_loaded', [Settings::class, 'maybeUpdateSettings']);

        // Gutenberg
        add_action('init', [Gutenberg::class, 'addBlocks']);
        add_action('rest_api_init', [Gutenberg::class, 'addEndpoints']);
        add_filter('block_categories_all', [Gutenberg::class, 'addCategory']);

        // Elementor
        add_action('elementor/elements/categories_registered', [Elementor::Class, 'addCategory']);
        add_action('elementor/widgets/register', [Elementor::Class, 'addWidgets']);

        // Load scripts
        add_action('admin_enqueue_scripts', [$this, 'loadAdminScripts']);
        add_action('wp_enqueue_scripts', [$this, 'loadScripts']);
        // Editing a page with Elementor makes WP think it's not in admin mode
        add_action('elementor/editor/before_enqueue_scripts', [$this, 'loadAdminScripts']);

        // Clear caches
        add_action('admin_post_clear_recras_cache', [$this, 'clearCache']);

        $this->addShortcodes();

        register_uninstall_hook(__FILE__, [__CLASS__, 'uninstall']);
    }

    private function addClassicEditorSubmenuPage(string $title, string $slug, callable $callable): void
    {
        add_submenu_page(
            '',
            $title,
            '',
            'publish_posts',
            $slug,
            $callable
        );
    }

    /**
     * Add the menu items for our plugin
     */
    public function addMenuItems(): void
    {
        $mainPage = current_user_can('manage_options') ? 'recras' : Settings::PAGE_CACHE;
        add_menu_page('Recras', 'Recras', 'edit_pages', $mainPage, '', plugin_dir_url(__DIR__) . 'logo.svg', 58);

        if (current_user_can('manage_options')) {
            add_submenu_page(
                'recras',
                __('Settings', 'recras'),
                __('Settings', 'recras'),
                'manage_options',
                'recras',
                ['\Recras\Settings', 'editSettings']
            );
        }

        add_submenu_page(
            'recras',
            __('Cache', 'recras'),
            __('Cache', 'recras'),
            'edit_pages',
            Settings::PAGE_CACHE,
            ['\Recras\Settings', 'clearCachePage']
        );
        add_submenu_page(
            'recras',
            __('Documentation', 'recras'),
            __('Documentation', 'recras'),
            'edit_pages',
            Settings::PAGE_DOCS,
            ['\Recras\Settings', 'documentation']
        );
        add_submenu_page(
            'recras',
            __('Shortcodes', 'recras'),
            __('Shortcodes', 'recras'),
            'edit_pages',
            Settings::PAGE_SHORTCODES,
            ['\Recras\Settings', 'shortcodes']
        );

        $this->addClassicEditorSubmenuPage(__('Package', 'recras'), 'form-arrangement', [Arrangement::class, 'showForm']);
        $this->addClassicEditorSubmenuPage(__('Book process', 'recras'), 'form-bookprocess', [Bookprocess::class, 'showForm']);
        $this->addClassicEditorSubmenuPage(__('Contact form', 'recras'), 'form-contact', [ContactForm::class, 'showForm']);
        $this->addClassicEditorSubmenuPage(__('Online booking of packages', 'recras'), 'form-booking', [OnlineBooking::class, 'showForm']);
        $this->addClassicEditorSubmenuPage(__('Product', 'recras'), 'form-product', [Products::class, 'showForm']);
        $this->addClassicEditorSubmenuPage(__('Voucher sales', 'recras'), 'form-voucher-sales', [Vouchers::class, 'showSalesForm']);
        $this->addClassicEditorSubmenuPage(__('Voucher info', 'recras'), 'form-voucher-info', [Vouchers::class, 'showInfoForm']);
    }


    /**
     * Register our shortcodes
     */
    private function addShortcodes(): void
    {
        add_shortcode(Availability::SHORTCODE, [Availability::class, 'renderAvailability']);
        add_shortcode(OnlineBooking::SHORTCODE, [OnlineBooking::class, 'renderOnlineBooking']);
        add_shortcode(Bookprocess::SHORTCODE, [Bookprocess::class, 'renderBookprocess']);
        add_shortcode(ContactForm::SHORTCODE, [ContactForm::class, 'renderContactForm']);
        add_shortcode(Arrangement::SHORTCODE, [Arrangement::class, 'renderPackage']);
        add_shortcode(Products::SHORTCODE, [Products::class, 'renderProduct']);
        add_shortcode(Vouchers::SHORTCODE_SALES, [Vouchers::class, 'renderVoucherSales']);
        add_shortcode(Vouchers::SHORTCODE_INFO, [Vouchers::class, 'renderVoucherInfo']);
    }

    private function checkOldSettings(): void
    {
        $instance = \Recras\Settings::getInstance();
        if (!$instance) {
            return;
        }

        $setting = Transient::get($instance . '_show_old_online_booking');
        if ($setting === false) {
            try {
                $setting = Http::get($instance, 'instellingen/allow_online_package_booking');
            } catch (\Exception $e) {
                return;
            }
            if (is_object($setting) && property_exists($setting, 'waarde')) {
                Transient::set($instance . '_show_old_online_booking', $setting->waarde, DAY_IN_SECONDS);
            }
        }

        $setting = Transient::get($instance . '_show_old_voucher_sales');
        if ($setting === false) {
            try {
                $setting = Http::get($instance, 'instellingen/allow_old_vouchers_sales');
            } catch (\Exception $e) {
                return;
            }
            if (is_object($setting) && property_exists($setting, 'waarde')) {
                Transient::set($instance . '_show_old_voucher_sales', $setting->waarde, DAY_IN_SECONDS);
            }
        }
    }


    //TODO: change to :never when we support only PHP 8.1+
    public static function clearCache(): void
    {
        $errors = 0;
        $errors += Arrangement::clearCache();
        $errors += Bookprocess::clearCache();
        $errors += ContactForm::clearCache();
        $errors += Products::clearCache();
        $errors += Settings::clearCache();
        $errors += Vouchers::clearCache();

        $pageUrl = 'admin.php?page=' . Settings::PAGE_CACHE . '&msg=' . Plugin::getStatusMessage($errors);
        header('Location: ' . admin_url($pageUrl));
        exit;
    }


    /**
     * Get error message if no domain has been entered yet
     */
    public static function noInstanceError(): string
    {
        if (current_user_can('manage_options')) {
            return __('Error: you have not set your Recras domain yet', 'recras');
        }
        return __('Error: your Recras domain has not been set yet, but you do not have the permission to set this. Please ask your site administrator to do this for you.', 'recras');
    }

    public static function getStatusMessage(int $errors): string
    {
        return ($errors === 0 ? 'success' : 'error');
    }


    /**
     * Load scripts for use in the WP admin
     */
    public function loadAdminScripts(): void
    {
        $l10n = [
            'contact_form' => __('Contact form', 'recras'),
            'no_connection' => __('Could not connect to your Recras', 'recras'),
            'online_booking' => __('Online booking of packages', 'recras'),
            'bookprocess' => __('Book process', 'recras'),
            'package' => __('Package', 'recras'),
            'package_availability' => __('Package availability', 'recras'),
            'product' => __('Product', 'recras'),
            'showOnlineBooking' => 'yes',
            'showVoucherSales' => 'yes',
            'voucherInfo' => __('Voucher info', 'recras'),
            'voucherSales' => __('Voucher sales', 'recras'),
        ];

        if (!Settings::allowOnlinePackageBooking()) {
            $l10n['showOnlineBooking'] = 'no';
        }
        if (!Settings::allowOldVoucherSales()) {
            $l10n['showVoucherSales'] = 'no';
        }

        wp_register_script('recras-admin', $this->baseUrl . '/js/admin.js', [], '6.4.0', true);
        wp_localize_script('recras-admin', 'recras_l10n', $l10n);
        wp_enqueue_script('recras-admin');
        wp_enqueue_style('recras-admin-style', $this->baseUrl . '/css/admin-style.css', [], '6.0.7');
        wp_enqueue_script('wp-api');
    }

    /**
     * Load the general script and localisation
     */
    public function loadScripts(): void
    {
        $localisation = [
            'checkboxRequired' => __('At least one choice is required', 'recras'),
            'loading' => __('Loading...', 'recras'),
            'sent_success' => __('Your message was sent successfully', 'recras'),
            'sent_error' => __('There was an error sending your message', 'recras'),
        ];

        // Add Pikaday scripts and Pikaday localisation if the site has "Use calendar widget" enabled
        if (get_option('recras_datetimepicker')) {
            wp_enqueue_script('pikaday', 'https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.2/pikaday.min.js', [], '1.8.2', true);
            wp_enqueue_style('pikaday', 'https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.2/css/pikaday.min.css', [], '1.8.2');

            $localisation['pikaday'] = [
                'previousMonth' => __('Previous month', 'recras'),
                'nextMonth' => __('Next month', 'recras'),
                'months' => [
                    __('January', 'recras'),
                    __('February', 'recras'),
                    __('March', 'recras'),
                    __('April', 'recras'),
                    __('May', 'recras'),
                    __('June', 'recras'),
                    __('July', 'recras'),
                    __('August', 'recras'),
                    __('September', 'recras'),
                    __('October', 'recras'),
                    __('November', 'recras'),
                    __('December', 'recras'),
                ],
                'weekdays' => [
                    __('Sunday', 'recras'),
                    __('Monday', 'recras'),
                    __('Tuesday', 'recras'),
                    __('Wednesday', 'recras'),
                    __('Thursday', 'recras'),
                    __('Friday', 'recras'),
                    __('Saturday', 'recras'),
                ],
                'weekdaysShort' => [
                    __('Sun', 'recras'),
                    __('Mon', 'recras'),
                    __('Tue', 'recras'),
                    __('Wed', 'recras'),
                    __('Thu', 'recras'),
                    __('Fri', 'recras'),
                    __('Sat', 'recras'),
                ],
            ];
        }

        // Fix BP date picker for sites that set HTML { font-size: 10px }
        if (get_option('recras_fix_react_datepicker')) {
            // This version number is the react-datepicker version
            wp_enqueue_style('fixreactdatepicker', $this->baseUrl . '/css/fixreactdatepicker.css', [], '8.9.0');
        }

        if (Settings::allowOnlinePackageBooking() || Settings::allowOldVoucherSales()) {
            wp_enqueue_script('recrasjslibrary', $this->baseUrl . '/js/onlinebooking.min.js', [], $this::LIBRARY_VERSION, ['strategy' => 'defer']);
        }

        // Book process
        // We should load the `_base` stylesheet before the `_styling` stylesheet, so the styling gets priority over the base
        $instance = Settings::getInstance();
        wp_enqueue_style(
            'recras_bookprocesses_base',
            'https://' . $instance . '/bookprocess/bookprocess_base.css'
        );
        Bookprocess::enqueueScripts($instance);

        // Integration theme
        $theme = get_option('recras_theme');
        if ($theme) {
            $allowedThemes = Settings::getThemes();
            if ($theme !== 'none' && array_key_exists($theme, $allowedThemes)) {
                wp_enqueue_style(
                    'recras_bookprocesses_styling',
                    'https://' . $instance . '/bookprocess/bookprocess_styling.css'
                );

                wp_enqueue_style('recras_theme_base', $this->baseUrl . '/css/themes/base.css', [], '6.1.1');
                wp_enqueue_style('recras_theme_' . $theme, $this->baseUrl . '/css/themes/' . $theme . '.css', [], $allowedThemes[$theme]['version']);
            }
        }

        // Generic functionality & localisation script
        $scriptName = 'recras-frontend';
        wp_register_script($scriptName, $this->baseUrl . '/js/recras.js', ['jquery'], '6.5.0', true);
        wp_localize_script($scriptName, 'recras_l10n', $localisation);
        wp_enqueue_script($scriptName);
    }


    /**
     * Set plugin base dir
     */
    public function setBaseUrl(): void
    {
        $this->baseUrl = rtrim(plugins_url('', __DIR__), '/');
    }

    public static function uninstall(): void
    {
        delete_option('recras_currency');
        delete_option('recras_datetimepicker');
        delete_option('recras_fix_react_datepicker');
        delete_option('recras_decimal');
        delete_option('recras_enable_analytics');
        delete_option('recras_subdomain'); // Legacy since 2025-09
        delete_option('recras_domain');
        delete_option('recras_theme');
    }
}
