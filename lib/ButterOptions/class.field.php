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
