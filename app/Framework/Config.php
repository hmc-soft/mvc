<?php
namespace HMC;

use HMC\Session;

/*
 * config - an example for setting up system settings
 * When you are done editing, rename this file to 'config.php'
 *
 * @author David Carr - dave@simplemvcframework.HMC
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */
class Config
{
    private static $options;
	
	private static function merge_defaults($defaults,$opts) {
		$ret = array();
		foreach ($defaults as $key => $value) {
			$ret[$key] = $value;
			if(isset($opts[$key])) {
				foreach($opts[$key] as $opt_key => $opt_val) {
					$ret[$key][$opt_key] = $opt_val;
				}
			}
		}
		return $ret;
	}

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
          'TYPE' => '',
          'HOST' => '',
          'NAME' => '',
          'USER' => '',
          'PASS' => '',
          'PREFIX' => '',
          'CONNECTION' => null
        ),
        'SESSION' => array(
          'NAME' => 'HMVC-DEFSESSION',
          'PREFIX' => 'hmc_',
          'ENCRYPT' => false,
          'ALGO' => MCRYPT_RIJNDAEL_128,
          'MODE' => MCRYPT_MODE_CBC,
          'KEY' => '',
          'IV' => ''
        ),
        'LOG' => array(
          'EMAIL' => false
        ),
		'ROUTES' => array(
			array('GET','','Controllers\\Welcome@index'),
			array('GET','subpage','Controllers\\Welcome@subPage')
		)

      );
    }

    public static function init($configFile)
    {
        $opts = null;
        $apcEnabled = (bool)ini_get('apc.enabled');
        if($apcEnabled && apc_exists('hmcsoftmvc-config')) {
          if(file_exists($configFile)) {
            $lastModTime = filemtime($configFile);
            $lastConfTime = 0;
            if(apc_exists('hmcsoftmvc-config-updated')) {
              $lastConfTime = apc_fetch('hmcsoftmvc-config-updated');
            }
            if($lastConfTime < $lastModTime) {
              $opts = json_decode(file_get_contents($configFile),true);
              apc_store('hmcsoftmvc-config',$opts);
              apc_store('hmcsoftmvc-config-updated',$lastModTime);
            } else {
              $opts = apc_fetch('hmcsoftmvc-config');
            }
          }
        } else {
          if(is_readable($configFile)) {
            $opts = json_decode(file_get_contents($configFile),true);
          }
        }
        $defaults = self::get_defaults();
        if($opts !== null){
          self::$options = (array) self::merge_defaults($defaults,(array)$opts);
        }

        if(isset(self::$options['SITE'])) {
          if(isset(self::$options['ENVIRONMENT'])) {
            if(self::$options['SITE']['ENVIRONMENT'] == 'development') {
              error_reporting(E_NONE);

            } else {
              error_reporting(E_NONE);
            }
          }
          //set timezone
          if(isset(self::$options['SITE']['TIMEZONE'])) {
            date_default_timezone_set(self::$options['SITE']['TIMEZONE']);
          }
        }

        //turn on output buffering
        ob_start();

        //turn on custom error handling
        if(isset(self::$options['LOG'])) {
          \HMC\Logger::init(self::$options['LOG']);
          if(isset(self::$options['LOG']['EXCEPTIONS'])){
            set_exception_handler(self::$options['LOG']['EXCEPTIONS']);
          } else {
            set_exception_handler('HMC\Logger::exceptionHandler');
          }
          if(isset(self::$options['LOG']['ERRORS'])){
            set_error_handler(self::$options['LOG']['ERRORS']);
          } else {
            set_error_handler('HMC\Logger::errorHandler');
          }
        }

        //start sessions
        if(isset(self::$options['SESSION'])){
          Session::init(self::$options['SESSION']);
        } else {
          Session::init();
        }

        return self::$options;
    }

    public function __toString() {
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
              throw new \InvalidArgumentException('No option set in: ' . $opt);
            }
          } else {
            throw new \InvalidArgumentException('Invalid option requested: ' . $opt . ', ' . $oarr[0] . ' is not an array.');
          }
        } else {
          if(isset(self::$options[$oarr[0]])){
            return self::$options[$oarr[0]];
          } else {
            throw new \InvalidArgumentException('No option set in: ' . $opt);
          }
        }
      } else {
        throw new \InvalidArgumentException('Unknown option error: ' . $opt);
      }
    }
}
