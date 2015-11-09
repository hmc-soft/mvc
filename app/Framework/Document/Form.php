<?php

namespace HMC\Document;

/**
* The Form class contains static helpers for creating forms.
*/
class Form {

  /**
  * Easily output a properly formed select tag.
  *
  * Example:
  *
  * ``use HMC\Document\Form;``
  *
  * ``echo Form::select(array(``
  *
  * ``  'name' => 'myselect',``
  *
  * ``  'wrapper-class' => 'form-item dropdown',``
  *
  * ``  'label-text' => 'Choose an option...',``
  *
  * ``  'value' => 1``
  *
  * ``), array(``
  *
  * ``  array(``
  *
  * ``    'value' => 0,``
  *
  * ``    'text' => 'None'``
  *
  * ``  ),``
  *
  * ``  array(``
  *
  * ``    'value' => 1,``
  *
  * ``    'text' => 'First'``
  *
  * ``  )``
  *
  * ``))``
  *
  * ``
  * Outputs:
  * ``
  * <div class="form-item dropdown">
  *   <label for="myselect">Choose an option...</label>
  *   <select id="myselect" name="myselect">
  *     <option value="0">None</option>
  *     <option value="1" selected="selected">First</option>
  *   </select>
  * </div>
  * ``
  * @param $attribs - attributes to assigns
  * @param $options - the options the select will contain.
  * @return the HTML of the select box.
  */
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
    return $p;
  }

  /**
  * Easily output a proper input field.
  *
  * Example:
  * ``
  * use \HMC\Document\Form;
  * echo Form::input(array(
  *   'wrapper-class' => 'form-item',
  *   'name' => 'username',
  *   'label-text' => 'User Name:',
  *   'type' => 'text',
  * ));
  * ``
  * Returns:
  * ``
  * <div class="form-item">
  *   <label for="username">User Name:</label>
  *   <input name="username" id="username" type="text" />
  * </div>
  * ``
  */
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
    return $p;
  }

}
