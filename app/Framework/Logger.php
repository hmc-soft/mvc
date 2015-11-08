<?php
namespace HMC;

use HMC\Network\Mail;

/*
 * logger class - Custom errors
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */
class Logger
{

    /**
    * determines if error should be displayed
    * @var boolean
    */
    private static $printError = false;

    /**
    * determines if error should be emailed to SITEEMAIL defined in the config
    * @var boolean
    */
    private static $emailError = false;

    /**
    * clear the errorlog
    * @var boolean
    */
    private static $clear = false;

    /**
    * path to error file
    * @var boolean
    */
    private static $errorFile = '.error.log';

    private static $logDir = 'app/Logs';

    private static $logger = null;

    public static function init($args = array()) {
      if(isset($args['DIR'])){
        self::$logDir = $args['DIR'];
      }
      if(isset($args['EMAIL'])){
        self::$emailError = $args['EMAIL'];
      }

      if(self::$logger == null) {
        self::$logger = new \Katzgrau\KLogger\Logger(self::$logDir,(isset($args['LEVEL'])? $args['LEVEL'] : \Psr\Log\LogLevel::ERROR));
      }

    }

    public static function info($e) {
      if(self::$logger != null) {
        self::$logger->info($e);
      }
    }

    public static function warn($e) {
      if(self::$logger != null) {
        self::$logger->warning($e);
      }
    }

    public static function debug($e) {
      if(self::$logger != null) {
        self::$logger->debug($e);
      }
    }

    public static function error($e) {
      if(self::$logger != null) {
        self::$logger->error($e);
      }
    }

    /**
    * in the event of an error show this message
    */
    public static function customErrorMsg()
    {
        echo "<p>An error occured, The error has been reported.</p>";
        exit;
    }

    /**
    * saved the exception and calls customer error function
    * @param  exeption $e
    */
    public static function exceptionHandler($e)
    {
      if(Config::SITE_ENVIRONMENT() == 'development') {
        Error::showError(500,self::buildExceptionMessage($e));
        die();
      }
      if(self::$logger != null) {
        self::error($e);
      } else {
        self::newMessage($e);
        self::customErrorMsg();
      }


    }

    /**
    * saves error message from exception
    * @param  numeric $number  error number
    * @param  string $message the error
    * @param  string $file    file originated from
    * @param  numeric $line   line number
    */
    public static function errorHandler($number, $message, $file, $line)
    {
      if(Config::SITE_ENVIRONMENT() == 'development') {
        Error::showError(500,'('.$number.') '.$file.':'.$line. ' => '.$message);
        die();
      }
      $msg = "$message in $file on line $line";
      if(self::$logger != null){
        self::error($number . $msg);
      } else {
        if (($number !== E_NOTICE) && ($number < 2048)) {
            self::errorMessage($msg);
            self::customErrorMsg();
        }
      }

      return 0;
    }

    public static function buildExceptionMessage(\Exception $exception) {
      $message = $exception->getMessage();
      $code = $exception->getCode();
      $file = $exception->getFile();
      $line = $exception->getLine();
      $trace = $exception->getTraceAsString();
      $date = date('M d, Y G:iA');

      $logMessage = "<h3>Exception information:</h3>\n
         <p><strong>Date:</strong> {$date}</p>\n
         <p><strong>Message:</strong> {$message}</p>\n
         <p><strong>Code:</strong> {$code}</p>\n
         <p><strong>File:</strong> {$file}</p>\n
         <p><strong>Line:</strong> {$line}</p>\n
         <h3>Stack trace:</h3>\n
         <pre>{$trace}</pre>\n
         <hr />\n";

      return $logMessage;
    }

    /**
    * new exception
    * @param  Exception $exception
    * @param  boolean   $printError show error or not
    * @param  boolean   $clear       clear the errorlog
    * @param  string    $errorFile  file to save to
    */
    public static function newMessage(\Exception $exception)
    {

        $logMessage = self::buildExceptionMessage($exception);

        if (is_file(self::$errorFile) === false) {
            file_put_contents(self::$errorFile, '');
        }

        if (self::$clear) {
            $f = fopen(self::$errorFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }

            $content = null;
        } else {
            $content = file_get_contents(self::$errorFile);
        }

        file_put_contents(self::$errorFile, $logMessage . $content);

        //send email
        self::sendEmail($logMessage);

        if (self::$printError == true) {
            echo $logMessage;
            exit;
        }
    }

    /**
    * custom error
    * @param  string  $error       the error
    * @param  boolean $printError display error
    * @param  string  $errorFile  file to save to
    */
    public static function errorMessage($error)
    {
        $date = date('M d, Y G:iA');
        $logMessage = "<p>Error on $date - $error</p>";

        if (is_file(self::$errorFile) === false) {
            file_put_contents(self::$errorFile, '');
        }

        if (self::$clear) {
            $f = fopen(self::$errorFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }

            $content = null;
        } else {
            $content = file_get_contents(self::$errorFile);
            file_put_contents(self::$errorFile, $logMessage . $content);
        }

        //send email
        self::sendEmail($logMessage);

        if (self::$printError == true) {
            echo $logMessage;
            exit;
        }
    }

    public static function sendEmail($message)
    {
        if (self::$emailError == true) {
            $mail = new Mail();
            $mail->setFrom(\HMC\Config::SITE_EMAIL());
            $mail->addAddress(\HMC\Config::SITE_EMAIL());
            $mail->subject('New error on '.\HMC\Config::SITE_TITLE());
            $mail->body($message);
            $mail->send();
        }
    }
}
