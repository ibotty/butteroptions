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

use \ButterLog as Log;

abstract class Page {
}

class SettingsPage extends Page {
  static $slugs = array();

  public $title;
  public $menu_title;
  public $capability = 'manage_options';
  protected $icon = 'options-general';
  protected $styles = "";
  protected $parent_page;
  protected $intros = array();
  protected $page_slug;
  protected $latest_section;

  function __construct($options, $title, $parent_page='options-general.php') {
    $this->options = $options;
    $this->title = $title;
    $this->parent_page = $parent_page;

    $normalized_title = sanitize_title_with_dashes($title);
    if (! isset(self::$slugs[$normalized_title]))
      self::$slugs[$normalized_title] = 0;
    else {
      self::$slugs[$normalized_title] = 0;
      $normalized_title.= "_".self::$slugs[$normalized_title];
    }

    $this->page_slug = $normalized_title;

    add_action('admin_menu', array($this, 'add_admin_page'));
  }

  function slug() {
    return $this->page_slug;
  }
  /**
   * set the pages icon (up, left of the title).
   * the icon will be set using a div with class icon32 and id icon-$slug.
   * the predefined icons (by wp) are:
   *   'edit', 'upload', 'link-manager', 'edit-pages', 'edit-comments',
   *   'themes', 'plugins', 'users', 'tools', 'options-general'
   *
   * @param string $icon the slug of the icon to use.
   * @param string|false $url the url of the icon, if no predefined chosen.
   * @param string $position additional css background property
   *
   * credits go to http://www.onextrapixel.com/2009/07/01/how-to-design-and-style-your-wordpress-plugin-admin-panel/ for a list of icons.
   */
  function set_icon($icon, $url=false, $position="") {
    if ($url) {
      add_action('admin_print_styles', array($this, 'admin_print_styles'));
      $this->styles.= "#icon-$icon {
          background: url('$url') no-repeat scroll $position transparent;
        }\n";
    }
    $this->icon = $icon;
  }

  function admin_print_styles() {
    echo $this->styles;
  }

  function add_section($section) {
    $this->latest_section = $section;
    $section->set_options($this->options);
    add_settings_section($section->slug(), $section->title,
      array($section, 'echo_section'), $this->page_slug);
  }

  function add_field($field, $section=null) {
    if (! $section) {
      if (! isset($this->latest_section))
        $this->add_section(new SimpleSession($this->title));
      $section = $this->latest_section;
    }

    // the following is to not have to pass the options on constructor time to the field
    $field->set_options($this->options);
    add_settings_field($field->slug(), $field->title, array($field, 'echo_field'),
      $this->page_slug, $section->slug(), $field->additional_args);
  }

  function add_intro($intro, $escaped=true) {
    $this->intros[] = $escaped? $intro: "<p>".esc_html($intro)."</p>";
  }

  function add_admin_page() {
    # default to title for menu title
    $menu_title = $this->menu_title? $this->menu_title: $this->title;

    add_submenu_page($this->parent_page, $this->title, $menu_title,
      $this->capability, $this->page_slug, array($this, 'echo_page'));
  }

  function echo_page() {
    echo "<div class='wrap'>
      <div id='icon-$this->icon' class='icon32'><br /></div>
      <h2>$this->title</h2>";
    echo join($this->intros, '\n');

    echo "<form action='options.php' method='post'>";

    settings_fields($this->options->slug());
    do_settings_sections($this->page_slug);

    echo "<p class='submit'>
      <input id='submit' name='submit' type='submit' class='button-primary' value=".__("Save Changes")." />
      </p>
      </form>
      </div>";

  }
}

?>
