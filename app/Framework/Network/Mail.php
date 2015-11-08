<?php
namespace HMC\Network;

use HMC\Network\PhpMailer;

/*
 * Mail Helper
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 1.0
 * @date May 18 2015
 */
class Mail extends PhpMailer
{
    // Set default variables for all new objects
    public $From     = 'noreply@domain.com';
    public $FromName = SITETITLE;
    //public $Host     = 'smtp.gmail.com';
    //public $Mailer   = 'smtp';
    //public $SMTPAuth = true;
    //public $Username = 'email';
    //public $Password = 'password';
    //public $SMTPSecure = 'tls';
    public $WordWrap = 75;
    
    public function __construct( array $opts = null ) {
        if($opts) {
            if(isset($opts['From'])) {
                $this->From = $opts['From'];
            }
            if(isset($opts['FromName'])) {
                $this->FromName = $opts['FromName'];
            }
            if(isset($opts['Subject'])) {
                $this->Subject = $opts['Subject'];
            }
            if(isset($opts['To'])) {
                $this->To = $opts['To'];
            }
            if(isset($opts['Body'])) {
                $this->Body = $opts['Body'];
            }
            if(isset($opts['AltBody'])) {
                $this->AltBody = $opts['AltBody'];
            }
        }
    }

    public function subject($subject)
    {
        $this->Subject = $subject;
    }

    public function body($body)
    {
        $this->Body = $body;
    }

    public function send()
    {
        if($this->Body !== '' && $this->AltBody === '') {
            $this->AltBody = strip_tags(stripslashes($this->Body))."\n\n";
            $this->AltBody = str_replace("&nbsp;", "\n\n", $this->AltBody);
        }
        return parent::send();
    }
}
