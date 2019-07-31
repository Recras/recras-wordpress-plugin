<?php
namespace Recras;

class Gutenberg
{
    const ENDPOINT_NAMESPACE = 'recras';
    const GUTENBERG_SCRIPT_VERSION = '2.4.8';


    public static function addBlocks()
    {
        $globalScriptName = 'recras-gutenberg-global';
        $globalStyleName = 'recras-gutenberg';
        wp_register_script(
            $globalScriptName,
            plugins_url('js/gutenberg-global.js', __DIR__), [
            'wp-blocks',
            'wp-components',
            'wp-element',
            'wp-i18n',
        ],
            self::GUTENBERG_SCRIPT_VERSION,
            true
        );
        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations($globalScriptName, Plugin::TEXT_DOMAIN, plugin_dir_path(__DIR__) . 'lang');
        }
        wp_localize_script($globalScriptName, 'recrasOptions', [
            'settingsPage' => admin_url('admin.php?page=' . Settings::OPTION_PAGE),
            'subdomain' => get_option('recras_subdomain'),
        ]);

        wp_register_style(
            $globalStyleName,
            plugins_url('css/gutenberg.css', __DIR__),
            ['wp-edit-blocks'],
            '2.4.2'
        );

        $gutenbergBlocks = [
            'availability' => [
                'callback' => ['Recras\Availability', 'renderAvailability'],
                'version' => '2.4.8',
            ],
            'contactform' => [
                'callback' => ['Recras\ContactForm', 'renderContactForm'],
                'version' => '2.4.8',
            ],
            'onlinebooking' => [
                'callback' => ['Recras\OnlineBooking', 'renderOnlineBooking'],
                'version' => '2.4.8',
            ],
            'package' => [
                'callback' => ['Recras\Arrangement', 'renderPackage'],
                'version' => '2.4.8',
            ],
            'product' => [
                'callback' => ['Recras\Products', 'renderProduct'],
                'version' => '2.4.8',
            ],
            'voucher-info' => [
                'callback' => ['Recras\Vouchers', 'renderVoucherInfo'],
                'version' => '2.4.8',
            ],
            'voucher-sales' => [
                'callback' => ['Recras\Vouchers', 'renderVoucherSales'],
                'version' => '2.4.8',
            ],
        ];
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

    public static function addCategory($categories)
    {
        $categories[] = [
            'slug' => 'recras',
            'title' => 'Recras',
        ];
        return $categories;
    }

    public static function addEndpoints()
    {
        $routes = [
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
                    'callback' => ['Recras\Gutenberg', $callback],
                    'permission_callback' => function () {
                        return current_user_can('edit_posts');
                    },
                ]
            );
        }
    }

    public static function getContactForms()
    {
        $model = new ContactForm;
        return $model->getForms(get_option('recras_subdomain'));
    }

    public static function getPackages()
    {
        $model = new Arrangement;
        return $model->getArrangements(get_option('recras_subdomain'));
    }

    public static function getProducts()
    {
        $model = new Products;
        return $model->getProducts(get_option('recras_subdomain'));
    }

    public static function getVouchers()
    {
        $model = new Vouchers;
        return $model->getTemplates(get_option('recras_subdomain'));
    }
}
