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
    public static function get(string $subdomain, string $uri)
    {
        $ch = curl_init('https://' . $subdomain . '.recras.nl/api2/' . $uri);
        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, 1);
        $json = curl_exec($ch);

        if ($json === false) {
            $errorMsg = curl_error($ch);
            throw new Exception\UrlException(sprintf(__('Error: could not retrieve data from Recras. The error message received was: %s', Plugin::TEXT_DOMAIN), $errorMsg));
        }
        try {
            $json = json_decode($json, null, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            throw new Exception\JsonParseException(sprintf(__('Error: could not parse data from Recras. The error message was: %s', Plugin::TEXT_DOMAIN), $e->getMessage()));
        }

        curl_close($ch);
        return $json;
    }
}
