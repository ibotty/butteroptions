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

abstract class Section {
  protected $slug;
  protected $option;
  public $title;

  function echo_section() {
  }

  function slug() {
    if (! isset($this->slug))
      $this->slug = uniqid('section_');
    return $this->slug;
  }
  function set_options($options) {
    $this->options = $options;
  }
}

class SimpleSection extends Section {
  protected $fields = array();
  public $intro;
  public $escape_intro = true;

  function __construct($title, $intro="", $escape_intro=true) {
    $this->title = $title;
    $this->intro = $intro;
    $this->escape_intro = $escape_intro;
  }

  function echo_section() {
    if ($this->escape_intro)
      echo "<p>".esc_html($this->intro)."</p>";
    else
      echo $this->intro;
  }
}
?>
