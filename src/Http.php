<?php
namespace Recras;

class Http
{
    /**
     * @return array|object|string
     *
     * @throws Exception\JsonParseException
     * @throws Exception\UrlException
     */
    public static function get(string $instance, string $uri)
    {
        $recras_response = wp_remote_get('https://' . $instance . '/api2/' . $uri);
        if ($recras_response instanceof \WP_Error) {
            throw new Exception\UrlException(
                sprintf(
                    esc_html(
                        /* translators: Error message */
                        __('Error: could not retrieve data from Recras. The error message received was: %s', 'recras')
                    ), esc_html($recras_response->get_error_message())
                )
            );
        }
        $json = $recras_response['body'];

        try {
            $json = json_decode($json, null, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            throw new Exception\JsonParseException(
                sprintf(
                    esc_html(
                        /* translators: Error message */
                        __('Error: could not parse data from Recras. The error message was: %s', 'recras'), $e->getMessage()
                    )
                )
            );
        }

        return $json;
    }
}
