<?php
/*
Plugin Name: Cornernote Contact Form
Description: A contact form for wordpress
Version: 1.0
Author: Frank
License: GPLv2 or later
Text Domain: cornernote-contact-form
*/


// small update


// we're using Composer's PSR-4 Autoloader
require('vendor/autoload.php');

// instantiate the main plugin class (a singleton)
Cornernote\Contact::get_instance();









