<?php

namespace HMC\Security;

/**
* Easily manage Captcha creation / verification.
*/
class Captcha extends \Gregwar\Captcha\CaptchaBuilder {

  /**
  * Generate a new Captcha
  */
  public static function generate( $width = 150, $height = 40) {
    $ret = new Captcha();
    $ret->build($width,$height);
    Session::set("CAPTCHA_VALUE",$ret->getPhrase());
    return $ret;
  }

  public static function verify($passed) {
    
  }
}
