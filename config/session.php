<?php
return [
    "driver" => env("SESSION_DRIVER", "database"),
    "lifetime" => env("SESSION_LIFETIME", 120),
    "expire_on_close" => false,
    "encrypt" => false,
    "files" => env("SESSION_FILES", true),
    "path" => realpath(storage_path("framework/sessions")),
    "store" => null,
    "lottery" => [2, 100],
    "cookie" => "laravel_session",
    "cookie_http_only" => true,
    "cookie_same_site" => "lax",
    "secure" => env("APP_ENV") === "production",
    "same_site" => "lax",
    "domain" => null,
    "table" => "sessions",
    "connection" => null,
];
