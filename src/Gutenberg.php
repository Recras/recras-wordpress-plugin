<?php
namespace Recras;

class Gutenberg
{
    const ENDPOINT_NAMESPACE = 'recras';
    const GUTENBERG_SCRIPT_VERSION = '2.3.4';


    public static function addBlocks()
    {
        $globalScriptName = 'gutenberg-recras-global';
        $globalStyleName = 'gutenberg-recras';
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
            wp_set_script_translations($globalScriptName, Plugin::TEXT_DOMAIN);
        }

        wp_register_style(
            $globalStyleName,
            plugins_url('css/gutenberg.css', __DIR__),
            ['wp-edit-blocks'],
            filemtime(plugin_dir_path(__FILE__) . '../css/gutenberg.css')
        );

        $gutenbergBlocks = [
            'availability' => [
                'callback' => ['Recras\Availability', 'renderAvailability'],
                'version' => '2.3.4',
            ],
            'contactform' => [
                'callback' => ['Recras\ContactForm', 'renderContactForm'],
                'version' => '2.3.4',
            ],
            'onlinebooking' => [
                'callback' => ['Recras\OnlineBooking', 'renderOnlineBooking'],
                'version' => '2.3.4',
            ],
            'package' => [
                'callback' => ['Recras\Arrangement', 'renderPackage'],
                'version' => '2.3.4',
            ],
            'product' => [
                'callback' => ['Recras\Products', 'renderProduct'],
                'version' => '2.2.1',
            ],
            'voucher' => [
                'callback' => ['Recras\Vouchers', 'renderVouchers'],
                'version' => '2.2.1',
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
            if (function_exists('wp_set_script_translations')) {
                wp_set_script_translations($handle, Plugin::TEXT_DOMAIN); //TODO: this generates an empty JS object
            }

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
