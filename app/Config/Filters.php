<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'      => CSRF::class,
        'toolbar'   => DebugToolbar::class,
        'honeypot'  => Honeypot::class,
        'adminauth' => \App\Filters\AdminAuthFilter::class,
    ];

    public array $globals = [
        'before' => [],
        'after'  => ['toolbar'],
    ];

    public array $methods = [];
    public array $filters = [];
}