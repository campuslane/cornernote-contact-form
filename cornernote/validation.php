<?php namespace Cornernote;


class Validation {

	private static $instance = null;

	private function __construct()
	{
	

	}


	public static function get_instance()
	{
		if ( null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	public function check()
	{
		return true;
	}

	public function passed()
	{
		return true;
	}

}