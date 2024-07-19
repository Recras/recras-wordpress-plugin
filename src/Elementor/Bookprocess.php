<?php

namespace Recras\Elementor;

use Recras\Plugin;

class Bookprocess extends \Elementor\Widget_Base
{
    public function get_name(): string
    {
        return 'bookprocess';
    }

    public function get_title(): string
    {
        return __('Book process', Plugin::TEXT_DOMAIN);
    }

    public function get_icon(): string
    {
        return 'eicon-editor-list-ul';
    }

    public function get_categories(): array
    {
        return ['recras'];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section(
            'content',
            [
                'label' => __('Book process', Plugin::TEXT_DOMAIN),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ],
        );

        $options = \Recras\Bookprocess::optionsForElementorWidget();
        $this->add_control(
            'id',
            [
                'type' => \Elementor\Controls_Manager::SELECT2,
                'label' => __('Book process', Plugin::TEXT_DOMAIN),
                'options' => $options,
                'default' => count($options) === 1 ? reset($options) : null,
            ]
        );
        //TODO: prefill first widget
        //TODO: hide first widget

        $this->end_controls_section();
    }

    protected function render(): void
    {
        echo do_shortcode('[' . \Recras\Plugin::SHORTCODE_BOOK_PROCESS . ' id="' . $this->get_settings_for_display('id') . '"]');
    }

    protected function content_template()
    {
        $options = \Recras\Bookprocess::optionsForElementorWidget();
        ?>
        TODO: Book process #{{ settings.id }} is integrated here
        <?php
    }
}
