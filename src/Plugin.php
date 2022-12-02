<?php
/*
 * Copyright 2022 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace LoginAttempts;

use Cake\Core\BasePlugin;

/**
 * Plugin class for CakePHP.
 */
class Plugin extends BasePlugin
{
    /**
     * Do bootstrapping or not
     *
     * @var bool
     */
    protected $bootstrapEnabled = false;

    /**
     * Enable middleware
     *
     * @var bool
     */
    protected $middlewareEnabled = false;

    /**
     * Load routes or not
     *
     * @var bool
     */
    protected $routesEnabled = false;

    /**
     * Console middleware
     *
     * @var bool
     */
    protected $consoleEnabled = true;
}
