<?php
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    echo "<h1>Please install via composer.json</h1>";
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
    exit;
}

$configFile = '.app_config.json';

$config = null;
$apcEnabled = (bool)ini_get('apc.enabled');
if($apcEnabled && apc_exists('hmcsoftmvc-config')) {
  if(file_exists($configFile)) {
    $lastModTime = filemtime($configFile);
    $lastConfTime = 0;
    if(apc_exists('hmcsoftmvc-config-updated')) {
      $lastConfTime = apc_fetch('hmcsoftmvc-config-updated');
    }
    if($lastConfTime < $lastModTime) {
      $config = json_decode(file_get_contents($configFile));
      apc_store('hmcsoftmvc-config',$config);
      apc_store('hmcsoftmvc-config-updated',$lastModTime);
    } else {
      $config = apc_fetch('hmcsoftmvc-config');
    }
  }
} else {
  if(is_readable($configFile)) {
    $config = json_decode(file_get_contents($configFile));
  }
}
//initiate config
\Core\Config::init($config);

//create alias for Router
use \Core\Router;
use \Helpers\Hooks;
$hooks = Hooks::get();

if(isset($config['ROUTES'])){ //Routes defined in the config file.
  Router::parseConfig($config['ROUTES']);
}

if(isset($config['HOOKS']) && isset($config['HOOKS']['ROUTES'])) {
  //These call a function on the controller to setup the routes.
  //This is the preferred method for projects with a large number of routes.
  foreach($config['HOOKS']['ROUTES'] as $route) {
    Hooks::addHook('routes',$route);
  }
}

$hooks->run('routes');

//if no route found
Router::error('Core\Error@index');

//turn on old style routing
Router::$fallback = false;

//execute matched routes
Router::dispatch();
