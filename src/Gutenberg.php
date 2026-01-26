<?php
namespace Recras;

class Gutenberg
{
    private const ENDPOINT_NAMESPACE = 'recras';

    public static function addBlocks(): void
    {
        $globalScriptName = 'recras-gutenberg-global';
        $globalStyleName = 'recras-gutenberg';
        wp_register_script(
            $globalScriptName,
            plugins_url('js/gutenberg-global.js', __DIR__), [
                'wp-blocks',
                'wp-components',
                'wp-date',
                'wp-element',
                'wp-i18n',
            ],
            '6.5.1',
            true
        );
        wp_set_script_translations($globalScriptName, 'recras', plugin_dir_path(__DIR__) . 'lang');
        wp_localize_script($globalScriptName, 'recrasOptions', [
            'settingsPage' => admin_url('admin.php?page=' . Settings::OPTION_PAGE),
            'instance' => Settings::getInstance(),
        ]);

        wp_register_style(
            $globalStyleName,
            plugins_url('css/gutenberg.css', __DIR__),
            ['wp-edit-blocks'],
            '3.6.3'
        );

        $gutenbergBlocks = [
            'availability' => [
                'callback' => [Availability::class, 'renderAvailability'],
                'version' => '6.4.0',
            ],
            'bookprocess' => [
                'callback' => [Bookprocess::class, 'renderBookprocess'],
                'version' => '6.5.1',
            ],
            'contactform' => [
                'callback' => [ContactForm::class, 'renderContactForm'],
                'version' => '6.5.1',
            ],
            'onlinebooking' => [
                'callback' => [OnlineBooking::class, 'renderOnlineBooking'],
                'version' => '6.4.0',
            ],
            'package' => [
                'callback' => [Arrangement::class, 'renderPackage'],
                'version' => '6.5.1',
            ],
            'product' => [
                'callback' => [Products::class, 'renderProduct'],
                'version' => '6.5.1',
            ],
            'voucher-info' => [
                'callback' => [Vouchers::class, 'renderVoucherInfo'],
                'version' => '6.5.1',
            ],
            'voucher-sales' => [
                'callback' => [Vouchers::class, 'renderVoucherSales'],
                'version' => '6.5.1',
            ],
        ];

        if (!Settings::allowOnlinePackageBooking()) {
            unset($gutenbergBlocks['availability']);
            unset($gutenbergBlocks['onlinebooking']);
        }
        if (!Settings::allowOldVoucherSales()) {
            unset($gutenbergBlocks['voucher-sales']);
        }

        foreach ($gutenbergBlocks as $key => $block) {
            $handle = 'recras-gutenberg-' . $key;
            wp_register_script(
                $handle,
                plugins_url('js/gutenberg-' . $key . '.js', __DIR__),
                [$globalScriptName],
                $block['version'],
                true
            );
            // Translations for these scripts are already handled by the global script

            \register_block_type('recras/' . $key, [
                'editor_script' => 'recras-gutenberg-' . $key,
                'editor_style' => $globalStyleName,
                'render_callback' => $block['callback'],
            ]);
        }
    }

    public static function addCategory(array $categories): array
    {
        $categories[] = [
            'slug' => 'recras',
            'title' => 'Recras',
        ];
        return $categories;
    }

    public static function addEndpoints(): void
    {
        $routes = [
            'bookprocesses' => 'getBookprocesses',
            'contactforms' => 'getContactForms',
            'packages' => 'getPackages',
            'products' => 'getProducts',
            'vouchers' => 'getVouchers',
        ];
        foreach ($routes as $uri => $callback) {
            register_rest_route(
                self::ENDPOINT_NAMESPACE,
                '/' . $uri,
                [
                    'methods' => 'GET',
                    'callback' => [__CLASS__, $callback],
                    'permission_callback' => function () {
                        return current_user_can('edit_posts');
                    },
                ]
            );
        }
    }

    public static function getBookprocesses()
    {
        $instance = \Recras\Settings::getInstance();
        $model = new Bookprocess();
        return $model->getProcesses($instance);
    }

    public static function getContactForms()
    {
        $instance = \Recras\Settings::getInstance();
        $model = new ContactForm();
        return $model->getForms($instance);
    }

    public static function getPackages()
    {
        $instance = \Recras\Settings::getInstance();
        $model = new Arrangement();
        return $model->getPackages($instance, false, false);
    }

    public static function getProducts()
    {
        $instance = \Recras\Settings::getInstance();
        $model = new Products();
        return $model->getProducts($instance);
    }

    public static function getVouchers()
    {
        $instance = \Recras\Settings::getInstance();
        $model = new Vouchers();
        return $model->getTemplates($instance);
    }
}
