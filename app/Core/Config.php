<?php
namespace Core;

use Helpers\Session;

/*
 * config - an example for setting up system settings
 * When you are done editing, rename this file to 'config.php'
 *
 * @author David Carr - dave@simplemvcframework.com
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */
class Config
{
    private static $options;

    private static function get_defaults() {
      return array(
        'SITE' => array(
          'URL' => '',
          'PATH' => '',
          'ENVIRONMENT' => 'development',
          'TITLE' => 'v1.0',
          'EMAIL' => '',
          'TEMPLATE' => 'default',
          'LANGUAGE' => 'en',
          'TIMEZONE' => 'America/Chicago'
        ),
        'DATABASE' => array(
          'TYPE' => 'mysql',
          'HOST' => 'localhost',
          'NAME' => '',
          'USER' => '',
          'PASS' => '',
          'PREFIX' => '',
          'CONNECTION' => null
        ),
        'SESSION' => array(
          'NAME' => '',
          'PREFIX' => 'hmc_',
          'ENCRYPT' => false,
          'ALGO' => MCRYPT_RIJNDAEL_128,
          'MODE' => MCRYPT_MODE_CBC,
          'KEY' => '',
          'IV' => ''
        ),
        'LOG' => array(
          'EXCEPTIONS' => 'Core\Logger::ExceptionHandler',
          'ERRORS' => 'Core\Logger::ErrorHandler',
          'DIR' => 'app/logs',
          'EMAIL' => false,
          'LEVEL' => \Psr\Log\LogLevel::ERROR
        )

      );
    }

    public static function init(array $opts = null)
    {
        $defaults = self::get_defaults();
        if(is_array($opts)){
          self::$options = array_merge($defaults,$opts);
        }


        switch (self::$options['SITE']['ENVIRONMENT']) {
            case 'development':
                error_reporting(E_ALL);
                break;
            default:
                error_reporting(0);
        }


        //turn on output buffering
        ob_start();

        //turn on custom error handling
        \Core\Logger::init(self::$options['LOG']);
        set_exception_handler(self::$options['LOG']['EXCEPTIONS']);
        set_error_handler(self::$options['LOG']['ERRORS']);

        //set timezone
        date_default_timezone_set(self::$options['SITE']['TIMEZONE']);

        //start sessions
        Session::init(self::$options['SESSION']);
    }

    public static function __toString() {
      return json_encode(self::$options);
    }

    public static function __callstatic($opt,$params = null) {
      $oarr = explode('_',$opt);
      if(is_array($oarr)) {
        if(count($oarr) > 1) {
          if(is_array(self::$options[$oarr[0]])){
            if(isset(self::$options[$oarr[0]]) && isset(self::$options[$oarr[0]][$oarr[1]])){
              return self::$options[$oarr[0]][$oarr[1]];
            } else {
              throw new InvalidArgumentException('No option set in: ' . $opt);
            }
          } else {
            throw new InvalidArgumentException('Invalid option requested: ' . $opt . ', ' . $oarr[0] . ' is not an array.');
          }
        } else {
          if(isset(self::$options[$oarr[0]])){
            return self::$options[$oarr[0]];
          } else {
            throw new InvalidArgumentException('No option set in: ' . $opt);
          }
        }
      } else {
        throw new InvalidArgumentException('Unknown option error: ' . $opt);
      }
    }
}
