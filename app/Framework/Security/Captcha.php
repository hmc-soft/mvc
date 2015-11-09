<?php

namespace HMC\Security;

use HMC\Session;

/**
* Easily manage Captcha creation / verification.
*/
class Captcha extends \Gregwar\Captcha\CaptchaBuilder {

  /**
  * Generate a new Captcha.
  * The phrase contained in the Captcha is automatically placed
  * in the user's session for later verification.
  *
  * @param $width int The width of the generated captcha image.
  * @param $height int The height of the generated captcha image.
  * @return Captcha object for further options and output.
  */
  public static function generate( $width = 150, $height = 40) {
    $ret = new Captcha();
    $ret->build($width,$height);
    Session::set("CAPTCHA_VALUE",$ret->getPhrase());
    return $ret;
  }

  /**
  * Verify a previously created Captcha phrase.
  * @param @passedValue string containing the phrase recieved from the user.
  * @return bool True if the value matches the captcha phrase, false otherwise.
  */
  public static function verify($passedValue) {
    $realv = Session::pull("CAPTCHA_VALUE");
    if(strtolower($passedValue) === strtolower($realv)) {
      return true;
    }
    return false;
  }
}
