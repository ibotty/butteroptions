<?php
/*
Copyright (c) 2011, 2012 Tobias Florek.  All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

   1. Redistributions of source code must retain the above copyright notice,
      this list of conditions and the following disclaimer.

   2. Redistributions in binary form must reproduce the above copyright notice,
      this list of conditions and the following disclaimer in the documentation
      and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO
EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace ButterOptions;

use ButterLog as Log;

abstract class Field {
  protected $id;
  protected $options;
  public $additional_args = array();
  public $title;

  function __construct($title, $id) {
    $this->title = $title;
    $this->id = $id;
  }
  abstract function echo_field($additional_args = array());
  function slug() {
    return "{$this->options->slug()}[$this->id]";
  }
  function default_value() {
    return $this->options->default_value($this->id);
  }
  function current_value() {
    return $this->options->current_value($this->id);
  }
  function set_options($options) {
    $this->options = $options;
  }
}

class OptionField extends Field {
  protected $choices;
  protected $multiple;

  /**
   * @param string $title
   * @param string $id
   * @param array $choices possible values as hash "desc" => "value"
   * @param bool $allow_multiple weather to allow selecting multiple options
   */
  function __construct($title, $id, $choices, $allow_multiple=true) {
    $this->choices = $choices;
    $this->multiple = $allow_multiple;
    parent::__construct($title, $id);
  }
  function echo_field($additional_args = array()){
    if ($this->multiple)
      echo "<select name='{$this->slug()}[]' multiple='multiple'>\n";
    else
      echo "<select name='{$this->slug()}' $this->multiple>\n";

    $current = $this->current_value();
    foreach ($this->choices as $label => $value) {
      $selected = in_array($value, $current)? "selected='selected'": "";
      echo "<option value='$value' $selected/>$label</option>\n";
    }
  }
}
class RadioButton extends RadioOrCheckbox {
  protected $type = "radio";
}
class Checkbox extends RadioOrCheckbox {
  protected $type = "checkbox";
}
abstract class RadioOrCheckbox extends Field {
  protected $type;
  protected $choices;
  protected $displayopts;
  /**
   * @param string $title
   * @param string $id
   * @param array $choices possible values as hash "label" => "value"
   * @param bool $allow_multiple weather to allow selecting multiple options
   */
  function __construct($title, $id, $choices, $displayopts=array()) {
    $display_defaults = array('separate_str'=>"<br />", 'label_before'=>false);

    $this->displayopts = wp_parse_args($display_defaults);
    $this->choices = $choices;
    parent::__construct($title, $id);
  }

  function echo_field($additional_args = array()) {
    extract($this->displayopts);

    $current = $this->current_value();
    Log::debug("current value:", $current);
    foreach ($this->choices as $label => $value) {
      $c_id = esc_attr($label);
      if (is_array($current))
        $checked = in_array($value, $current)? "checked='checked'": "";
      else
        $checked = $current == $value? "checked='checked'": "";

      if ($label_before)
        echo "<label for='$c_id'>$label</label>";
      echo "<input id='$c_id' type='$this->type' name='{$this->slug()}' $checked value='$value'/>";
      if (! $label_before)
        echo "<label for='$c_id'>$label</label>";
      echo "$separate_str \n";
    }
  }
}
class TextField extends Field {
  function __construct($title, $id, $desc="", $placeholder="") {
    $this->placeholder = $placeholder;
    $this->desc = $desc;
    parent::__construct($title, $id);
  }

  function echo_field($additional_args = array()) {
    $placeholder = $this->placeholder? $this->placeholder: $this->default_value();
    echo "<input class='regular-text'
      type='text'
      placeholder='$placeholder'
      value='{$this->current_value()}'
      name='{$this->slug()}' />\n";
    if ($this->desc)
      echo "<span class='description'>$this->desc</span>";
  }
}

?>
