<?php
namespace HMC;

use HMC\View;
use HMC\Hooks;

/*
 * Router - routing urls to closurs and controllers - modified from https://github.com/NoahBuscher/Macaw
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 2.2
 * @date Auguest 16th, 2014
 * @date updated May 18 2015
 */
class Router
{

    // Fallback for auto dispatching feature.
    public static $fallback = false;

    // If true - do not process other routes when match is found
    public static $halts = true;

    // Set routes, methods and etc.
    public static $static_routes = array();
    public static $routes = array();
    public static $methods = array();
    public static $callbacks = array();
    public static $errorCallback;

    // Set route patterns
    public static $patterns = array(
        ':any' => '[^/]+',
        ':num' => '[0-9]+',
        ':all' => '.*'
    );

    /**
     * Defines a route w/ callback and method
     *
     * @param   string $method
     * @param   array @params
     */
    public static function __callstatic($method, $params)
    {

        $uri = dirname($_SERVER['PHP_SELF']).'/'.$params[0];
        $callback = $params[1];

        array_push(self::$routes, $uri);
        array_push(self::$methods, strtoupper($method));
        array_push(self::$callbacks, $callback);
    }

    public static function init($config) {
      $hooks = Hooks::get();

      if(isset($config['STATIC'])) {
        foreach($config['STATIC'] as $stat_r) {
          self::$static_routes[] = $stat_r;
        }
      }

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
    }

    public static function parseConfig(array $routes) {
      foreach($routes as $route) {
        $method = strtolower($route[0]);
        $url = $route[1];
        $call = $route[2];

        call_user_func(array('\HMC\Router',$method),$url,$call);
      }
    }

    /**
     * Defines callback if route is not found
     * @param   string $callback
     */
    public static function error($callback)
    {
        self::$errorCallback = $callback;
    }

    /**
     * Don't load any further routes on match
     * @param  boolean $flag
     */
    public static function haltOnMatch($flag = true)
    {
        self::$halts = $flag;
    }

    /**
     * Call object and instantiate
     *
     * @param  object $callback
     * @param  array $matched  array of matched parameters
     * @param  string $msg
     */
    public static function invokeObject($callback, $matched = null, $msg = null)
    {

        //grab all parts based on a / separator and collect the last index of the array
        $last = explode('/', $callback);
        $last = end($last);

        //grab the controller name and method call
        $segments = explode('@', $last);

        //instanitate controller with optional msg (used for errorCallback)
        $controller = new $segments[0]($msg);

        if ($matched == null) {
            //call method
            $controller->$segments[1]();
        } else {
            //call method and pass in array keys as params
            call_user_func_array(array($controller, $segments[1]), $matched);
        }
    }

    /**
     * autoDispatch by Volter9
     * Ability to call controllers in their controller/model/param way
     */
    public static function autoDispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = trim($uri, ' /');
        $uri = ($amp = strpos($uri, '&')) !== false ? substr($uri, 0, $amp) : $uri;

        $parts = explode('/', $uri);
        if($parts[0] == 'index.php'
         || $parts[0] == str_replace('/','',Config::SITE_PATH())
        )
          unset($parts[0]);

        $controller = array_shift($parts);
        //$controller = $controller ? $controller : DEFAULT_CONTROLLER;

        $method = array_shift($parts);
        $method = $method ? $method : 'index';

        $args = !empty($parts) ? $parts : array();

        // Check for file
        if (!file_exists("app/Controllers/$controller.php")) {
            return false;
        }

        $controller = ucwords($controller);
        $controller = "\\Controllers\\$controller";
        $c = new $controller;

        if (method_exists($c, $method)) {
            $c->$method($args);
            //found method so stop
            return true;
        }

        return false;
    }

    /**
     * Runs the callback for the given request
     */
    public static function dispatch()
    {

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        self::$routes = str_replace('//', '/', self::$routes);

        $found_route = false;

        // parse query parameters

        $query = '';
        $q_arr = array();
        if (strpos($uri, '&') > 0) {
            $query = substr($uri, strpos($uri, '&') + 1);
            $uri = substr($uri, 0, strpos($uri, '&'));
            $q_arr = explode('&', $query);
            foreach ($q_arr as $q) {
                $qobj = explode('=', $q);
                $q_arr[] = array($qobj[0] => $qobj[1]);
                if (!isset($_GET[$qobj[0]])) {
                    $_GET[$qobj[0]] = $qobj[1];
                }
            }
        }

        // check if route is defined without regex
        if (in_array($uri, self::$routes)) {
            $route_pos = array_keys(self::$routes, $uri);

            // foreach route position
            foreach ($route_pos as $route) {
                if (self::$methods[$route] == $method || self::$methods[$route] == 'ANY') {
                    $found_route = true;

                    //if route is not an object
                    if (!is_object(self::$callbacks[$route])) {
                        //call object controller and method
                        self::invokeObject(self::$callbacks[$route]);
                        if (self::$halts) {
                            return;
                        }
                    } else {
                        //call closure
                        call_user_func(self::$callbacks[$route]);
                        if (self::$halts) {
                            return;
                        }
                    }
                }

            }
            // end foreach

        } else {
            // check if defined with regex
            $pos = 0;

            // foreach routes
            foreach (self::$routes as $route) {
                $route = str_replace('//', '/', $route);

                if (strpos($route, ':') !== false) {
                    $route = str_replace($searches, $replaces, $route);
                }

                if (preg_match('#^' . $route . '$#', $uri, $matched)) {
                    if (self::$methods[$pos] == $method || self::$methods[$pos] == 'ANY') {
                        $found_route = true;

                        //remove $matched[0] as [1] is the first parameter.
                        array_shift($matched);

                        if (!is_object(self::$callbacks[$pos])) {
                            //call object controller and method
                            self::invokeObject(self::$callbacks[$pos], $matched);
                            if (self::$halts) {
                                return;
                            }
                        } else {
                            //call closure
                            call_user_func_array(self::$callbacks[$pos], $matched);
                            if (self::$halts) {
                                return;
                            }
                        }
                    }
                }
                $pos++;
            }
            // end foreach
        }

        if (self::$fallback) {
            //call the auto dispatch method
            $found_route = self::autoDispatch();
        }

        // run the error callback if the route was not found
        if (!$found_route) {
          //first check if we have a satisfiable static route.
          if(count(self::$static_routes) > 0) {
            $test_sr = str_replace(Config::SITE_PATH().'/','',$uri);
            foreach(self::$static_routes as $sr) {
              if(file_exists($sr . $test_sr)) {
                $ext = \HMC\Document\Document::getExtension($test_sr);
                switch($ext) {
                  case 'html':
                  case 'htm':
                    require($sr . $test_sr);
                    break;

                  default:
                    header("Location: {$sr}{$test_sr}");
                }
                die;
              } else {
                //for security you cannot access php files directly so
                //if there's no extension we test for a php file with that name.
                if(file_exists($sr.$test_sr.'.php')) {
                  require($sr.$test_sr.'.php');
                  die;
                }
              }
            }
          }

          if (!self::$errorCallback) {
              self::$errorCallback = function () {
                  Error::showError(404);
              };
          }

          if (!is_object(self::$errorCallback)) {
              //call object controller and method
              self::invokeObject(self::$errorCallback, null, 'No routes found.');
              if (self::$halts) {
                  return;
              }
          } else {
              call_user_func(self::$errorCallback);
              if (self::$halts) {
                  return;
              }
          }
        }
    }
}
