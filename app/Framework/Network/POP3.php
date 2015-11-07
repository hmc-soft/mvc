<?php

Namespace HMC\Network;

class POP3 {

  private $conn;

  public function login($host,$port,$user,$pass,$folder="INBOX",$ssl=false)
  {
      $ssl=($ssl==false)?"/novalidate-cert":"";
      $this->conn = imap_open("{"."$host:$port/pop3$ssl"."}$folder",$user,$pass);
      return ($this->conn == false ? false : true);
  }
  public function stat()
  {
      $check = imap_mailboxmsginfo($this->conn);
      return ((array)$check);
  }
  public function getMessageList($message="")
  {
      if ($message)
      {
          $range=$message;
      } else {
          $MC = imap_check($this->conn);
          $range = "1:".$MC->Nmsgs;
      }
      $response = imap_fetch_overview($this->conn,$range);
      foreach ($response as $msg) $result[$msg->msgno]=(array)$msg;
          return $result;
  }
  public function getMessage($message)
  {
      return(imap_fetchheader($this->conn,$message,FT_PREFETCHTEXT));
  }
  public function deleteMessage($message)
  {
      return(imap_delete($this->conn,$message));
  }
  public function parseHeaders($headers)
  {
      $headers=preg_replace('/\r\n\s+/m', '',$headers);
      preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)?\r\n/m', $headers, $matches);
      foreach ($matches[1] as $key =>$value) $result[$value]=$matches[2][$key];
      return($result);
  }
  public function mimeToArray($imap,$mid,$parse_headers=false)
  {
      $mail = imap_fetchstructure($this->conn,$mid);
      $mail = $this->getParts($this->conn,$mid,$mail,0);
      if ($parse_headers) $mail[0]["parsed"]=$this->parseHeaders($mail[0]["data"]);
      return($mail);
  }
  public function getParts($mid,$part,$prefix)
  {
      $attachments=array();
      $attachments[$prefix]=$this->decodePart($mid,$part,$prefix);
      if (isset($part->parts)) // multipart
      {
          $prefix = ($prefix == "0")?"":"$prefix.";
          foreach ($part->parts as $number=>$subpart)
              $attachments=array_merge($attachments, $this->getParts($mid,$subpart,$prefix.($number+1)));
      }
      return $attachments;
  }
  public function decodePart($message_number,$part,$prefix)
  {
      $connection = $this->conn;
      $attachment = array();

      if($part->ifdparameters) {
          foreach($part->dparameters as $object) {
              $attachment[strtolower($object->attribute)]=$object->value;
              if(strtolower($object->attribute) == 'filename') {
                  $attachment['is_attachment'] = true;
                  $attachment['filename'] = $object->value;
              }
          }
      }

      if($part->ifparameters) {
          foreach($part->parameters as $object) {
              $attachment[strtolower($object->attribute)]=$object->value;
              if(strtolower($object->attribute) == 'name') {
                  $attachment['is_attachment'] = true;
                  $attachment['name'] = $object->value;
              }
          }
      }

      $attachment['data'] = imap_fetchbody($connection, $message_number, $prefix);
      if($part->encoding == 3) { // 3 = BASE64
          $attachment['data'] = base64_decode($attachment['data']);
      }
      elseif($part->encoding == 4) { // 4 = QUOTED-PRINTABLE
          $attachment['data'] = quoted_printable_decode($attachment['data']);
      }
      return($attachment);
  }
}
