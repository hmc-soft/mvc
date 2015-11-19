<?php
namespace HMC;

use HMC\Error;

/*
 * Language - simple language handler
 *
 * @author Bartek Kuśmierczuk - contact@qsma.pl - http://qsma.pl
 * @version 2.2
 * @date November 18, 2014
 * @date updated May 18 2015
 */
class Language
{
    /**
     * Variable holds array with language
     * @var array
     */
    private static $array;
    private static $code;
    private static $lastLoaded = null;

    /**
     * pLoad language function
     * @param  string $name - the name of the language file to load
     * @param  string $code - (optional, defaults to SITE_LANGUAGE) the language code to load, i.e. en = english, fr = french
     * @param  string $fileTemplate - (optional, defaults to app/Language dir) for plugins to load their lang files
     */
    public static function pload($name, $icode = null, $fileTemplate = 'app/Language/[code]/[name].php') {
      if($icode == null) {
        self::$code = \HMC\Config::SITE_LANGUAGE();
      } else {
          self::$code = $icode;
      }
      // lang file
      $file = str_replace('[code]',self::$code,$fileTemplate);
      $file = str_replace('[name]',$name,$file);

      // check if is readable
      if (is_readable($file)) {
          // require file
          if(!isset(self::$array[$name]))
              self::$array[$name] = array();

          if(empty(self::$array[$name][self::$code]))
              self::$array[$name][self::$code] = include($file);

          return true;
      } else {
          return false;
      }
    }

    /**
     * Load language function
     * @param  string $name - the name of the language file to load
     * @param  string $code - (optional, defaults to SITE_LANGUAGE) the language code to load, i.e. en = english, fr = french
     */
    public static function load($name, $icode = null)
    {
      if(self::pload($name,$icode)) {
        self::$lastLoaded = $name;
        return true;
      }
      return false;
    }

    /**
     * Deprecated
     * Get element from language array by key
     * @param  string $value
     * @return string
     */
    /*public static function get($value)
    {
        if (self::$lastLoaded != null && !empty(self::$array[self::$lastLoaded][self::$code][$value])) {
            return self::$array[self::$lastLoaded][self::$code][$value];
        } else {
            return $value;
        }
    }*/

    /**
     * Get lang for views
     * @param  string $value this is "word" value from language file
     * @param  string $name  name of file with language
     * @param  string $default optional, used as default if $value not in lang file.
     * @return string
     */
    public static function tr($value, $name = null, $def = null)
    {
        if($name == null && self::$lastLoaded != null)
            $name = self::$lastLoaded;

        self::load($name);

        if (!empty(self::$array[$name][self::$code] && isset(self::$array[$name][self::$code][$value]))) {
            return self::expand_macros(self::$array[$name][self::$code][$value]);
        } else {
            return self::expand_macros($def ? $def : $value);
        }

    }

    private static function expand_macros($value) {
      $out = $value;
      $matches = array();
      preg_match_all("/(\{\w+_\w+\})/",$value,$matches);
      if(count($matches) > 0) {
        foreach($matches[0] as $match) {
          $rm = rtrim($match,'}');
          $rm = ltrim($rm,'{');
          $out = str_replace($match,Config::$rm(),$out);
        }
      }
      return $out;
    }
}
