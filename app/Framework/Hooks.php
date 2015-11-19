<?php
namespace HMC;

/*
 * Hooks controller
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 2.2
 * @date updated May 18 2015
 */

class Hooks
{

    private static $plugins = array();
    private static $hooks = array();
    private static $instances = array();

    /**
     * initial hooks
     * @param  integer $id
     * @return $instance
     */
    public static function get($id = 0)
    {
        // return if instance exists
        if (isset(self::$instances[$id])) {
            return self::$instances[$id];
        }

        //define hooks
        self::setHooks(array(
            'init',
            'pre-config',
            'config',
            'config-ready',
            'get-database',
            'pre-headers',
            'headers',
            'meta',
            'css',
            'end-head',
            'beforeBody',
            'afterBody',
            'footer',
            'js',
            'routes',
            'pre-dispatch'
        ));

        //load modules
        self::loadPlugins('app/Framework/Modules/');
        $instance = new self();
        self::$instances[$id] = $instance;
        return $instance;

    }

    //adds hook to hook list
    public static function setHook($where)
    {
        if(!isset(self::$hooks[$where])) self::$hooks[$where] = array();
    }

    //add multiple hooks
    public static function setHooks($where)
    {
        foreach ($where as $where) {
            self::setHook($where);
        }
    }

    public static function loadPlugins($fromFolder)
    {
        if ($handle = opendir($fromFolder)) {
            while ($file = readdir($handle)) {
                if (is_file($fromFolder.$file)) {
                    require_once $fromFolder . $file;
                    self::$plugins [$file] ['file'] = $file;
                } elseif ((is_dir($fromFolder.$file)) && ($file != '.') && ($file != '..')) {
                  if(is_readable($fromfolder.$file.'/'.$file.'.php')) {
                    require_once $fromFolder . $file . '/' . $file . '.php';
                    self::$plugins [$file] ['file'] = $file;
                  } else {
                    self::loadPlugins($fromFolder.$file.'/');
                  }
                }
            }
            closedir($handle);
        }
    }

    //attach custom function to hook
    public static function addHook($where, $function, $priority = 10)
    {
        self::get();
        if (!isset(self::$hooks[$where])) {
            die("There is no such place ($where) for hooks.");
        } else {
          if(!is_int($priority)) $priority = 10;
          if($priority < 0) $priority = 0;
          if($priority > 99) $priority = 99;
          self::$hooks[$where][] = array( 'func' => $function, 'pri' => $priority);
        }
    }

    private static function sortHook($where) {
      $hsort = function($a, $b) {
        if($a['pri'] == $b['pri']) return 0;
        return ($a['pri'] < $b['pri'] ? -1 : 1);
      };
      usort(self::$hooks[$where],$hsort);
    }

    public static function run($where, $args = '')
    {
      self::get();
      if (isset(self::$hooks[$where])) {
          self::sortHook($where);
          $result = $args;

          foreach (self::$hooks[$where] as $hook) {
              if (preg_match("/@/i", $hook['func'])) {
                  //grab all parts based on a / separator
                  $parts = explode('\\', $hook['func']);

                  //collect the last index of the array
                  $last = end($parts);

                  //grab the controller name and method call
                  $segments = explode('@', $last);

                  $classname = new $segments[0]();
                  $result = call_user_func(array($classname, $segments[1]), $result);

              } else {
                  if (function_exists($hook['func'])) {
                      $result = call_user_func($hook['func'], $result);
                  } else {
                    if(is_callable($hook['func'])) {
                      $result = $hook['func']($result);
                    }
                  }
              }
          }

          return $result;
      } else {
          throw new \InvalidArgumentException("There is no such place ($where) for hooks.");
      }
    }

    public static function collectHook($where, $args = null)
    {
        self::get();
        ob_start();
        echo self::run($where, $args);
        return ob_get_clean();
    }
}
