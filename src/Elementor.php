<?php
namespace Recras;

class Elementor
{
    public static function addCategory($elements_manager): void
    {
        $elements_manager->add_category(
            'recras',
            [
                'title' => 'Recras',
                'icon' => 'fa fa-plug', // Where is this used?
            ]
        );
    }

    public static function addWidgets($widgets_manager): void
    {
        $widgets_manager->register(new Elementor\Bookprocess());
    }
}
