<?php
namespace HMC;

/*
 * View - load template pages
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */
class View
{
    /**
     * @var array Array of HTTP headers
     */
    private static $headers = array();

    private static function init() {
      if(!headers_sent()){
        \HMC\Hooks::run('headers');
      }
    }

    /**
     * include template file
     * @param  string  $path  path to file from views folder
     * @param  array $data  array of data
     * @param  array $error array of errors
     */
    public static function render($path, $data = false, $error = false)
    {
      self::init();
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }
        $file = "app/Views/$path.php";
        if(file_exists($file)) {
            require $file;
        } else {
            //Logger::error();
            Error::showError(500, 'File (' . $file . ') was not found.');
            die();
        }
    }

    /**
     * include template file
     * @param  string  $path  path to file from Modules folder
     * @param  array $data  array of data
     * @param  array $error array of errors
     */
    public static function renderModule($path, $data = false, $error = false)
    {
        self::init();
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }
        require "app/Framework/Modules/$path.php";
    }

    /**
     * return absolute path to selected template directory
     * @param  string  $path  path to file from views folder
     * @param  array   $data  array of data
     * @param  string  $custom path to template folder
     */
    public static function renderTemplate($path, $data = false, $custom = false)
    {
      self::init();
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }
        $file = "app/Templates/".\HMC\Config::SITE_TEMPLATE()."/$path.php";
        if ($custom == false) {
            if(file_exists($file)){
              require $file;
            } else {
                Logger::error('File (' . $file . ') was not found.');
              Error::showError(500);
              die();
            }
        } else {
            require "app/Templates/$custom/$path.php";
        }
    }

    /**
     * add HTTP header to headers array
     * @param  string  $header HTTP header text
     */
    public static function addHeader($header)
    {
        self::$headers[] = $header;
    }

    /**
    * Add an array with headers to the view.
    * @param array $headers
    */
    public static function addHeaders($headers = array())
    {
        foreach ($headers as $header) {
            self::addHeader($header);
        }
    }
}
