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

        $bps = \Recras\Bookprocess::getProcesses(get_option('recras_subdomain'));
        $bpsWithAcceptedFirstWidget = array_filter($bps, function ($bp) {
            return in_array($bp->firstWidget, ['package']);
        });

        $this->add_control(
            'initial_widget_value',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label' => __('Prefill value for first widget? (optional)', Plugin::TEXT_DOMAIN),
                'condition' => [
                    'id' => array_map(function ($id) {
                        return (string) $id;
                    }, array_keys($bpsWithAcceptedFirstWidget)),
                ],
            ]
        );

        $this->add_control(
            'hide_first_widget',
            [
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label' => __('Hide first widget?', Plugin::TEXT_DOMAIN),
                'condition' => [
                    'initial_widget_value!' => '',
                ],
            ]
        );

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
        Book process #{{ settings.id }} is integrated here
        <?php
    }
}
