<?php
namespace Recras;

class Transient
{
    private const BASE = 'recras_';

    /**
     * Delete a transient. Returns 0 for success/not existing, 1 for error for easy error counting
     */
    public static function delete(string $name): int
    {
        if (!self::get($name)) {
            return 0;
        }
        return (delete_transient(self::BASE . $name) ? 0 : 1);
    }

    /**
     * @return mixed
     */
    public static function get(string $name)
    {
        return get_transient(self::BASE . $name);
    }

    // HOUR_IN_SECONDS is defined by WordPress
    public static function set(string $name, $value, int $expiration = HOUR_IN_SECONDS): bool
    {
        return set_transient(self::BASE . $name, $value, $expiration);
    }
}
