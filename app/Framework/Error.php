<?php
namespace HMC;

use HMC\Controller;
use HMC\View;

/*
 * error class - calls a 404 page
 *
 * @author David Carr - dave@simplemvcframework.HMC
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */
class Error extends Controller
{
    /**
     * $error holder
     * @var string
     */
    private $error = null;

    /**
     * save error to $this->_error
     * @param string $error
     */
    public function __construct($error)
    {
        parent::__construct();
        $this->error = $error;
    }

    public static function error404() {
      View::addHeader("HTTP/1.0 404 Not Found");

      $data['title'] = 'Page Not Found';
      $data['error'] = 'Page Not Found';

      View::renderTemplate('header', $data);
      View::render('error/404', $data);
      View::renderTemplate('footer', $data);
    }

    public static function showError($errNum, $info = null) {

      ob_get_clean();
      $defError = "An internal server error has occured.";
      switch($errNum) {
        case 404:
          Error::error404();
          return;
          break;

        case 500:
        default:
          break;
      }
        if($info != null)
            Logger::error('['.$errNum.'] ' . $info);
        
      $data['title'] = "Internal Server Error";
      $data['error'] = $info != null ? (Config::SITE_ENVIRONMENT() == 'development' ? $defError . '<br/>'. $info : $defError) : $defError;

      View::addHeader("HTTP/1.0 500 Internal Server Error");
      View::renderTemplate('header', $data);
      View::render('error/500', $data);
      View::renderTemplate('footer', $data);
      die();
    }

    /**
     * load a 404 page with the error message
     */
    public function index()
    {
      Error::showError(404);
    }

    /**
     * display errors
     * @param  array  $error an error of errors
     * @param  string $class name of class to apply to div
     * @return string        return the errors inside divs
     */
    public static function display($error, $class = 'alert alert-danger')
    {
        if (is_array($error)) {
            foreach ($error as $error) {
                $row.= "<div class='$class'>$error</div>";
            }
            return $row;
        } else {
            if (isset($error)) {
                return "<div class='$class'>$error</div>";
            }
        }
    }
}