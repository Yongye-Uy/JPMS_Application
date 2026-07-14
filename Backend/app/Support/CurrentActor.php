<?php

namespace App\Support;

/**
 * Holds the resolved RemoteUser's id for the duration of the request, set
 * by the "remote-sanctum" guard closure in AppServiceProvider AFTER it
 * finishes resolving — NOT read via Auth::guard()->user() from inside
 * ApiClient, because that guard's own resolution closure calls ApiClient
 * (to introspect the token), which would recurse infinitely.
 */
class CurrentActor
{
    private static ?int $id = null;

    public static function set(?int $id): void
    {
        self::$id = $id;
    }

    public static function id(): ?int
    {
        return self::$id;
    }
}
