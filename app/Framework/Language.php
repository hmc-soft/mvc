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
     * Load language function
     * @param  string $name
     * @param  string $code
     */
    public static function load($name, $icode = null)
    {

        if($icode == null) {
          self::$code = (\HMC\Config::SITE_LANGUAGE() !== '' ? \HMC\Config::SITE_LANGUAGE() : 'en');
        } else {
            self::$code = $icode;
        }
        // lang file
        $file = "app/Language/".self::$code."/$name.php";

        // check if is readable
        if (is_readable($file)) {
            // require file
            if(!isset(self::$array[$name]))
                self::$array[$name] = array();
            
            if(empty(self::$array[$name][self::$code]))
                self::$array[$name][self::$code] = include($file);
            
            self::$lastLoaded = $name;
            return true;
        } else {
            // display error
            //echo Error::display("Could not load language file '".self::$code."/$name.php'");
            //die;
            return false;
        }
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
            return self::$array[$name][self::$code][$value];
        } else {
            return $def ? $def : $value;
        }
        
    }
}
