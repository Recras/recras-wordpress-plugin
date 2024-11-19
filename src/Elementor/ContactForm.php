<?php

namespace Recras\Elementor;

use Recras\Arrangement;
use Recras\Plugin;

class ContactForm extends \Elementor\Widget_Base
{
    public function get_name(): string
    {
        return 'contactform';
    }

    public function get_title(): string
    {
        return __('Contact form', Plugin::TEXT_DOMAIN);
    }

    public function get_icon(): string
    {
        return 'eicon-envelope';
    }

    public function get_categories(): array
    {
        return ['recras'];
    }

    private function getValidElements(): array
    {
        return [
            'dl' => __('Definition list', Plugin::TEXT_DOMAIN),
            'ol' => __('Ordered list', Plugin::TEXT_DOMAIN),
            'table' => __('Table', Plugin::TEXT_DOMAIN),
        ];
    }

    private function getValidChoiceElements(): array
    {
        return [
            'select' => __('Drop-down list (Select)', Plugin::TEXT_DOMAIN),
            'radio' => __('Radio buttons', Plugin::TEXT_DOMAIN),
        ];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section(
            'content',
            [
                'label' => __('Contact form', Plugin::TEXT_DOMAIN),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ],
        );

        $options = \Recras\ContactForm::optionsForElementorWidget();
        $this->add_control(
            'cf_id',
            [
                'type' => \Elementor\Controls_Manager::SELECT2,
                'label' => __('Contact form', Plugin::TEXT_DOMAIN),
                'options' => $options,
                'default' => count($options) === 1 ? reset($options) : null,
            ]
        );
        $this->add_control(
            'showtitle',
            [
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label' => __('Show title?', Plugin::TEXT_DOMAIN),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        $this->add_control(
            'showlabels',
            [
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label' => __('Show labels?', Plugin::TEXT_DOMAIN),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'showplaceholders',
            [
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label' => __('Show placeholders?', Plugin::TEXT_DOMAIN),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $pcks = Arrangement::getPackagesForElementor();
        $this->add_control(
            'package',
            [
                'type' => \Elementor\Controls_Manager::SELECT2,
                'label' => __('Package', Plugin::TEXT_DOMAIN),
                'options' => $pcks,
            ]
        );

        $validElements = $this->getValidElements();
        $this->add_control(
            'container_element',
            [
                'type' => \Elementor\Controls_Manager::SELECT2,
                'label' => __('HTML element', Plugin::TEXT_DOMAIN),
                'options' => $validElements,
                'default' => 'dl',
            ]
        );

        $validChoiceElements = $this->getValidChoiceElements();
        $this->add_control(
            'single_choice_element',
            [
                'type' => \Elementor\Controls_Manager::SELECT2,
                'label' => __('Element for single choices', Plugin::TEXT_DOMAIN),
                'options' => $validChoiceElements,
                'default' => 'select',
            ]
        );
        $this->add_control(
            'submittext',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label' => __('Submit button text', Plugin::TEXT_DOMAIN),
                'default' => __('Send', Plugin::TEXT_DOMAIN),
            ]
        );
        $this->add_control(
            'redirect',
            [
                'type' => \Elementor\Controls_Manager::URL,
                'label' => __('Thank-you page (optional, leave empty to not redirect)', Plugin::TEXT_DOMAIN),
                'options' => false,
            ]
        );

        $this->end_controls_section();
    }

    private function isValidRedirect($settings): bool
    {
        if (isset($settings['redirect'], $settings['redirect']['url']) && !empty($settings['redirect']['url'])) {
            return filter_var($settings['redirect']['url'], FILTER_VALIDATE_URL);
        }
        return false;
    }

    private function adminRender(array $settings): string
    {
        if (!$settings['cf_id']){
            return __('No contact form has been chosen yet. Click on this text to select a contact form.', Plugin::TEXT_DOMAIN);
        }
        $options = \Recras\ContactForm::optionsForElementorWidget();
        $html = '';
        $html .= sprintf(
            __('Contact form "%s" is integrated here.', Plugin::TEXT_DOMAIN),
            $options[$settings['cf_id']]
        );

        if ($this->isValidRedirect($settings)) {
            $html .= '<br>';
            $html .= sprintf(
                __('After submitting, the user will be redirected to "%s".', Plugin::TEXT_DOMAIN),
                $settings['redirect']['url']
            );
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

        $shortcode  = '[' . \Recras\Plugin::SHORTCODE_CONTACT_FORM;
        $shortcode .= ' id="' . $settings['cf_id'] . '"';

        if (isset($settings['showtitle']) && $settings['showtitle'] === 'yes') {
            $shortcode .= ' showtitle="yes"';
        }

        foreach (['showtitle', 'showlabels', 'showplaceholders'] as $option) {
            if (empty($settings[$option])) {
                $val = 0;
            } else {
                $val = ($settings[$option] === 'yes') ? 1 : 0;
            }
            $shortcode .= ' ' . $option . '="' . $val . '"';
        }

        if (isset($settings['package']) && $settings['package'] > 0) {
            $shortcode .= ' arrangement="' .$settings['package'] . '"';
        }

        if (isset($settings['container_element']) && in_array($settings['container_element'], array_keys($this->getValidElements()))) {
            $shortcode .= ' element="' .$settings['container_element'] . '"';
        }

        if (isset($settings['single_choice_element']) && in_array($settings['single_choice_element'], array_keys($this->getValidChoiceElements()))) {
            $shortcode .= ' single_choice_element="' .$settings['single_choice_element'] . '"';
        }

        if (isset($settings['submittext'])) {
            $shortcode .= ' submittext="' . $settings['submittext'] . '"';
        } else {
            echo'huh';
        }

        if ($this->isValidRedirect($settings)) {
            $shortcode .= ' redirect="' . $settings['redirect']['url'] . '"';
        }

        $shortcode .= ']';
        echo do_shortcode($shortcode);
    }
}
