<?php
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    echo "<h1>Please install via composer.json</h1>";
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
    exit;
}

//Routes are defined in the config file.
$configFile = '.app_config.json';

//initiate config
$config = \Core\Config::init($configFile);
\Helpers\Hooks::get();
\Helpers\Hooks::addHook('headers','addNotice');
//create alias for Router
use \Core\Router;

//Initialize Router
Router::init($config);

//if no route found
//Router::error('Core\Error@index');

//To route with the url/Controller/Method/args schema uncomment this.
Router::$fallback = true;

//execute matched routes
Router::dispatch();

function addNotice() {
  \Core\View::addHeader('X-Uses: HMC-soft MVC');
}
