<?php

namespace Recras\Elementor;

use Recras\Arrangement;
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
            'bp_id',
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
                    'bp_id' => array_map(function ($id) {
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

    private function adminRender(array $settings): string
    {
        if (!$settings['bp_id']){
            return __('No book process has been chosen yet. Click on this text to select a book process.', Plugin::TEXT_DOMAIN);
        }
        $options = \Recras\Bookprocess::optionsForElementorWidget();
        $html = '';
        $html .= sprintf(
            __('Book process "%s" is integrated here.', Plugin::TEXT_DOMAIN),
            $options[$settings['bp_id']]
        );
        if ($settings['initial_widget_value']) {
            $packages = Arrangement::getPackages(get_option('recras_subdomain'));
            $html .= '<br>';
            if ($settings['hide_first_widget']) {
                $pckId = (int) $settings['initial_widget_value'];
                $pckName = isset($packages[$pckId]) ? $packages[$pckId]->arrangement : $pckId;
                $html .= sprintf(
                    __('The first widget is hidden for the booker, and has an initial value of "%s".', Plugin::TEXT_DOMAIN),
                    $pckName
                );
            } else {
                $html .= sprintf(
                    __('It has an initial value for the first widget of "%s".', Plugin::TEXT_DOMAIN),
                    $settings['initial_widget_value']
                );
            }
        }
        return $html;
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        if (is_admin()) {
            echo $this->adminRender($settings);
            return;
        }

        $shortcode  = '[' . \Recras\Plugin::SHORTCODE_BOOK_PROCESS;
        $shortcode .= ' id="' . $settings['bp_id'] . '"';
        $shortcode .= ' initial_widget_value="' . $settings['initial_widget_value'] . '"';
        $shortcode .= ' hide_first_widget="' . $settings['hide_first_widget'] . '"';
        $shortcode .= ']';
        echo do_shortcode($shortcode);
    }
}
