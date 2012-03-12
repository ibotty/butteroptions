<?php
use ButterLog as Log;

class ButterOptions {
  /**
   * a hash of the form $key => $default
   * $key is the same for $options->$key
   *
   * nb.: array_keys($defaults) are the defined keys.
   */
  protected $defaults = array();

  /**
   * a hash of the form $key => $validation
   * $validation can be either a callback or a regex (i.e a string "/regex/").
   */
  protected $validations = array();

  /**
   * a hash of the form $key => $sanitize_cb
   */
  protected $sanitizes = array();

  /**
   * a hash of the form $key => $value
   */
  protected $values;

  /**
   * the settings slug
   */
  protected $slug;

  function __construct($slug, $defaults=array(), $validations=array(), $sanitizes=array()) {
    $this->slug = $slug;
    $this->defaults = $defaults;
    $this->validations = $validations;
    $this->sanitizes = $sanitizes;

    register_setting($this->slug, $this->slug, array($this, 'sanitize'));
  }

  function default_value($key) {
    return $this->defaults[$key];
  }

  function current_value($key) {
    $this->get_options();
    if (! isset($this->values[$key]))
      return null;
    else
      return $this->values[$key];
  }

  function __set($key, $val) {
    Log::debug("__set called with $key=> $val");
    if (isset($fields[$key])) {
      $this->get_options();

      if ($this->values[$key] === $val)
        return;

      $this->values[$key] = $val;
      update_option($this->serialize($this->values));
    }
  }

  /**
   * return the set value or the default, if not set
   */
  function __get($key) {
    if (! array_key_exists($key, $this->defaults)) {
      $trace = debug_backtrace();
      trigger_error("Undefined property via __get: ".__CLASS__."::$key ".
        "in {$trace[0]['file']} on line {$trace[0]['line']}.",
        E_USER_NOTICE);
    }
    $this->get_options();
    if (! isset($this->values[$key]))
      return $this->defaults[$key];
    else
      return $this->values[$key];
  }

  function __isset($key) {
    return array_key_exists($key, $this->defaults);
  }

  function slug() {
    return $this->slug;
  }

  function sanitize($options) {
    $problems = false;

    if (empty($this->values))
      $this->get_options();
    $new_options = $this->values;

    foreach ($options as $option=>$value) {
      // unset options are ok
      if ($value === "") {
        Log::debug("User unset option $option");
        unset($new_options[$option]);
        unset($options[$option]);
      } elseif (array_key_exists($option, $this->validations)) {
        $validation = $this->validations[$option];
        if (is_callable($validation)) {
          if (! call_user_func($validation, $value)) {
            Log::info("User set option $option to invalid value $value.");
            add_settings_error($this->slug,
              "$this->slug-problem-$option",
              __("Option $option cannot be set to $value."), 'error');
            $problems = true;
            unset($options[$option]);
          }
        } elseif (substr($validation,0, 1) == '/') {
          if (! preg_match($validation, $value)) {
            Log::info("User set option $option to invalid value $value.");
            add_settings_error($this->slug,
              "$this->slug-problem-$option",
              __("Option $option cannot be set to $value."), 'error');
            $problems = true;
            unset($options[$option]);
          }
        } else
          Log::warn("\$validations['$option'] is not supported");
      } else if (! array_key_exists($option, $this->defaults)) {
        // do not accept not defined options; silently ignore them.
        Log::warn("User set not existing option $option.");
        unset($options[$option]);
      }
    }
    if (! $problems)
      add_settings_error($this->slug,
        "$this->slug-saved",
        __("Settings saved."), 'updated');

    foreach ($options as $option=>&$value) {
      if (isset($this->sanitizes[$option]))
        $value = call_user_func($this->sanitizes[$option], $value);
    }

    // merge new and old options hash.
    return wp_parse_args($options, $new_options);
  }

  protected function get_options() {
    if (! isset($this->values))
      $this->values = $this->deserialize(get_option($this->slug));
  }
  /**
   * XXX: unimplemented
   */
  protected function deserialize($options) {
    return $options;
  }
  /**
   * XXX: unimplemented
   */
  protected function serialize($options) {
    return $options;
  }
}

?>
