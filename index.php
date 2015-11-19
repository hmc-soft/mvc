<?php
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    echo "<h1>Please install via composer.json</h1>";
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
    exit;
}

use HMC\Config;
use HMC\Hooks;
use HMC\Router;
use HMC\View;

//Routes are defined in the config file.
$configFile = '.app_config.json';

//initiate config
Hooks::run('init');
$configFile = Hooks::run('pre-config',$configFile);
$config = Config::init($configFile);
Hooks::run('config-ready');
Hooks::addHook('headers','addNotice');


//Initialize Router
Router::init($config);

//if no route found
//Router::error('Core\Error@index');

//To route with the url/Controller/Method/args schema uncomment this.
Router::$fallback = true;

Hooks::run('pre-dispatch');
//execute matched routes
Router::dispatch();

function addNotice() {
  View::addHeader('X-Uses: HMC-soft MVC');
}
