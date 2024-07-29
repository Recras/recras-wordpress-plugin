<?php
namespace Recras;

class Bookprocess
{
    /**
     * Clear book process cache (transients)
     */
    public static function clearCache(): int
    {
        global $recrasPlugin;

        $subdomain = get_option('recras_subdomain');
        $status = $recrasPlugin->transients->delete($subdomain . '_bookprocesses');
        $status += $recrasPlugin->transients->delete($subdomain . '_bookprocesses_v2');
        return $status;
    }

    public static function enqueueScripts(string $subdomain): void
    {
        wp_enqueue_script(
            'recrasbookprocesses',
            'https://' . $subdomain . '.recras.nl/bookprocess/bookprocess.mjs',
            [], false, true
        );

        wp_enqueue_style(
            'recrasreactdatepicker',
            'https://' . $subdomain . '.recras.nl/bookprocess/node_modules/react-datepicker/dist/react-datepicker.css'
        );
    }

    /**
     * Get book processes for a Recras instance
     *
     * @return array|string
     */
    public static function getProcesses(string $subdomain)
    {
        global $recrasPlugin;

        $json = $recrasPlugin->transients->get($subdomain . '_bookprocesses_v2');
        if ($json === false) {
            try {
                $json = Http::get($subdomain, 'bookprocesses/book');
            } catch (\Exception $e) {
                return $e->getMessage();
            }
            $recrasPlugin->transients->set($subdomain . '_bookprocesses_v2', $json);
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

    public static function optionsForElementorWidget()
    {
        $fmt = [];
        $processes = self::getProcesses(get_option('recras_subdomain'));
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

        $subdomain = Settings::getSubdomain($attributes);
        if (!$subdomain) {
            return Plugin::getNoSubdomainError();
        }

        if (empty($attributes['id'])) {
            return __('Error: no ID set', Plugin::TEXT_DOMAIN);
        }

        if (!ctype_digit($attributes['id']) && !is_int($attributes['id'])) {
            return __('Error: ID is not a number', Plugin::TEXT_DOMAIN);
        }

        $processes = self::getProcesses($subdomain);
        if (is_string($processes)) {
            // Not a form, but an error
            return sprintf(__('Error: %s', Plugin::TEXT_DOMAIN), $processes);
        }

        if (!isset($processes[$attributes['id']])) {
            return __('Error: book process does not exist', Plugin::TEXT_DOMAIN);
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
                data-url="https://' . $subdomain . '.recras.nl"
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
