Options for Wordpress with just the right amount of butter
============

How to use
---------
Have a look at the following example.

```php
<?php
require_once("path/to/ButterOptions.php");

// to simply use class names w/o namespace
use ButterOptions\SettingsPage,
  ButterOptions\SimpleSection,
  ButterOptions\TextField;

// register options. the allowed keys will be the keys of $defaults
$defaults = array(
  "option1" => "default1",
  "option2" => "default2"
);
$validations = array(
  "option1" => function($value) {...},
  "option2" => "/regex to match/"
);
$sanitizations = array(
  "option1" => 'strtolower'
);
$myoptions = new ButterOptions("my_options_slug", $defaults, $validations, $sanitizations);

// add settings page
$page = new SettingsPage($options, "My Title"); 

$page->add_section(new SimpleSection("My Settings", "A short description about my settings.")); //optional

$page->add_field(new TextField("Option 1", "option1", "short description about this setting"));


// to get an option
$my_option1 = $myoptions->option1;

// to set an option
$myoptions->$option2 = "take that, option";
```

There are other things possible. Look into the code for now.


Dependencies
---------
Wordpress (say: 3.3), PHP 5.3
