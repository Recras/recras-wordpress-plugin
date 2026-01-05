<?php
namespace Recras;

class Bookprocess
{
    public const SHORTCODE = 'recras-bookprocess';

    /**
     * Clear book process cache (transients)
     */
    public static function clearCache(): int
    {
        $instance = Settings::getInstance();
        return Transient::delete($instance . '_bookprocesses_v2');
    }

    public static function enqueueScripts(string $instance): void
    {
        wp_enqueue_script_module(
            'recrasbookprocesses',
            'https://' . $instance . '/bookprocess/dist/index.js',
            [],
            date('Ymd') // Hint at caching for 1 day
        );

        wp_enqueue_style(
            'recrasreactdatepicker',
            'https://' . $instance . '/bookprocess/node_modules/react-datepicker/dist/react-datepicker.css'
        );
    }

    /**
     * Get book processes for a Recras instance
     *
     * @return array|string
     */
    public static function getProcesses(string $instance)
    {
        $json = Transient::get($instance . '_bookprocesses_v2');
        if ($json === false) {
            try {
                $json = Http::get($instance, 'bookprocesses/book');
            } catch (\Exception $e) {
                return $e->getMessage();
            }
            Transient::set($instance . '_bookprocesses_v2', $json);
        }

        $processes = [];
        foreach ($json->_embedded->bookprocess as $process) {
            $processes[$process->id] = (object) [
                'name' => $process->name,
                'firstWidget' => $process->_extra->first_widget_type ?? null,
            ];
        }
        return $processes;
    }

    public static function optionsForElementorWidget(): array
    {
        $instance = Settings::getInstance();
        $fmt = [];
        $processes = self::getProcesses($instance);
        foreach ($processes as $id => $process) {
            $fmt[$id] = $process->name;
        }
        return $fmt;
    }

    /**
     * Add the [recras-bookprocess] shortcode
     *
     * @param array|string $attributes
     */
    public static function renderBookprocess($attributes): string
    {
        if (is_string($attributes)) {
            $attributes = [];
        }

        $instance = Settings::getInstance($attributes);
        if (!$instance) {
            return Plugin::noInstanceError();
        }

        if (empty($attributes['id'])) {
            return __('Error: no ID set', 'recras');
        }

        if (!ctype_digit($attributes['id']) && !is_int($attributes['id'])) {
            return __('Error: ID is not a number', 'recras');
        }

        $processes = self::getProcesses($instance);
        if (is_string($processes)) {
            // Not a form, but an error
            /* translators: Error message */
            return sprintf(__('Error: %s', 'recras'), $processes);
        }

        if (!isset($processes[$attributes['id']])) {
            return __('Error: book process does not exist', 'recras');
        }

        $initialWidgetValueHtml = '';
        $extraCSS = '';
        if (isset($attributes['initial_widget_value'])) {
            $initialWidgetValueHtml = ' data-first-widget-value="' . $attributes['initial_widget_value'] . '"';
            if (isset($attributes['hide_first_widget']) && Settings::parseBoolean($attributes['hide_first_widget'])) {
                $extraCSS = '<style>.bookprocess[data-id="' . $attributes['id'] . '"] .has-initial-value { display: none; }</style>';
            }
        }
        return '
            <section
                class="bookprocess" 
                data-id="' . $attributes['id'] . '" 
                data-url="https://' . $instance . '"
                ' . $initialWidgetValueHtml . '
            ></section>
        ' . $extraCSS;
    }

    /**
     * Show the TinyMCE shortcode generator contact form
     */
    public static function showForm(): void
    {
        require_once(__DIR__ . '/../editor/form-bookprocess.php');
    }
}
