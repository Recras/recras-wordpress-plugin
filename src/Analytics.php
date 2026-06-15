<?php
namespace Recras;

class Analytics
{
    public static function useAnalytics(): bool
    {
        return get_option('recras_enable_analytics');
    }
}
