<?php
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    echo "<h1>Please install via composer.json</h1>";
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
    exit;
}

$config = null;
if(is_readable('.app_config.json')) {
  $config = json_decode(file_get_contents('.app_config.json'));
}

//initiate config
\Core\Config::init($config);

//create alias for Router
use \Core\Router;
use \Helpers\Hooks;
$hooks = Hooks::get();

if(isset($config['ROUTES'])){
  Router::parseConfig($config['ROUTES']);
}

//Routes can be added here, but should be placed in the .app_config.json file.
//Router::get('','Controllers\Welcome@index');
//Router::get('sub-page','Controllers\Welcome@subPage');

$hooks->run('routes');

//if no route found
Router::error('Core\Error@index');

//turn on old style routing
Router::$fallback = false;

//execute matched routes
Router::dispatch();
