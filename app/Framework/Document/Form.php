<?php

namespace HMC\Document;

class Form {

  public static function select($attribs,$options) {
    $id = (isset($attribs['id']) ? $attribs['id'] : $attribs['name']);
    $p = '<div class="'.$attribs['wrapper-class'].'">';
    $p .= '<label for="'.$id.'">'.$attribs['label-text'].'</label>';
    $p .= '<select id="'.$id.'" name="'.$attribs['name'].'" ';
    foreach($attribs as $attrib => $value) {
      switch($attrib) {
        case 'id':
        case 'name':
        case 'wrapper-class':
        case 'label-text':
          break;

        default:
          $p .= $attrib . '="'.$value.'" ';
      }
    }
    $p .= '>';
    foreach($options as $option) {
      $selected = '';
      if($attribs['value'] == $option['value']) {
        $selected = ' selected="selected" ';
      }
      $p .= '<option value="'.$option['value'].'"'.$selected.'>'.$option['text'].'</option>';
    }
    $p .= '</select></div>';
    echo $p;
  }

  public static function input($attribs) {
    $id = (isset($attribs['id']) ? $attribs['id'] : $attribs['name']);
    $p = '<div class="'.$attribs['wrapper-class'].'">';
    $p .= '<label for="'.$id.'">'.$attribs['label-text'].'</label>';
    $p .= '<input name="'.$attribs['name'].'" id='.$id.'" ';
    foreach($attribs as $attrib => $value) {
      switch($attrib) {
        case 'id':
        case 'name':
        case 'wrapper-class':
        case 'label-text':
          break;

        default:
          $p .= $attrib . '="'.$value.'" ';
      }
    }
    $p .= '/></div>';
    echo $p;
  }

}
