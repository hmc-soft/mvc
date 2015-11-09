<?php
namespace HMC;

use HMC\Controller;
use HMC\View;
use HMC\Language;

/*
 * error class - displays and potentially logs an error.
 *
 * @author Ebben Feagan - ebben@hmc-soft.com
 * @author David Carr - dave@simplemvcframework.com
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

    private static function error404() {
      Language::load('Errors');
      View::addHeader("HTTP/1.0 404 Not Found");

      $data['title'] = Language::tr('404_title');
      $data['error'] = Language::tr('404_title');

      View::renderTemplate('header', $data);
      View::render('error/404', $data);
      View::renderTemplate('footer', $data);
    }

    /**
    * Show an error message to the user and log the error if needed.
    * Current 404 errors are not logged. This function ends execution.
    * @param $errNum int the HTTP error code to return, typically 404 for missing page or 500.
    * @param $info string (optional) with the message to log, not displayed in production.
    */
    public static function showError($errNum, $info = null) {

      Language::load('Errors');

      ob_get_clean();
      $defError = Language::tr('500_default_error');
      switch($errNum) {
        case 404:
          Error::error404();
          die;
          break;

        case 500:
        default:
          break;
      }
        if($info != null)
            Logger::error('['.$errNum.'] ' . strip_tags($info));

      $data['title'] = Language::tr('500_title');
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
