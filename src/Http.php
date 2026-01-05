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
        $ch = curl_init('https://' . $instance . '/api2/' . $uri);
        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, 1);
        $json = curl_exec($ch);

        if ($json === false) {
            $errorMsg = curl_error($ch);
            /* translators: Error message */
            throw new Exception\UrlException(sprintf(__('Error: could not retrieve data from Recras. The error message received was: %s', 'recras'), $errorMsg));
        }
        try {
            $json = json_decode($json, null, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            /* translators: Error message */
            throw new Exception\JsonParseException(sprintf(__('Error: could not parse data from Recras. The error message was: %s', 'recras'), $e->getMessage()));
        }

        curl_close($ch);
        return $json;
    }
}
