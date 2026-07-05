<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class DashboardCache
{
    public const KEY = 'dashboard.stats';

    public const TTL = 300;

    public static function forget(): void
    {
        Cache::forget(self::KEY);
    }
}
