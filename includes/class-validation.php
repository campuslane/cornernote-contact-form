<?php namespace CampusLane;

class Validation {

	public $plugin_path;
	protected $errors;

	public function __construct( $plugin_path )
	{
		$this->plugin_path = $plugin_path;
	}


	public function check($input, $rules, $messages = [])
	{
		$this->errors = [];

		$fields = $this->explode_rules($rules);

		foreach($fields as $field => $rules) {

			foreach($rules as $rule) {
				if ( ! call_user_func_array( [$this, $rule], [$field] ) ) {
					$this->errors[$field][] = $messages[$field][$rule];
				}
			}
		}	
	}

	public function get_validation_errors()
	{
		return $this->errors;
	}

	public function passed()
	{
		if (isset($this->errors['form_error'])) {
			unset($this->errors['form_error']);
		}
		return count($this->errors) == 0;
	}

	public function failed()
	{
		return count($this->errors) > 0;
	}

	public function field_error($field)
	{
		return isset($this->errors[$field][0]) ?  '<div class="alert alert-danger">' . $this->errors[$field][0] . '</div>' : '';
	}

	public function field_value($field)
	{
		return ( isset( $_POST[$field]) ? esc_attr( $_POST[$field] ) : '' );
	}

	public function set_form_error($error)
	{
		$this->errors['form_error'] = $error;
	}

	public function get_form_error()
	{
		return isset($this->errors['form_error']) ? $this->errors['form_error'] : '';
	}

	public function get_errors()
	{
		return $this->errors;
	}

	/**
	 * Explode the rules into an array of rules.
	 *
	 * @param  string|array  $rules
	 * @return array
	 */
	protected function explode_rules($rules)
	{
		foreach ($rules as $key => &$rule)
		{
			$rule = (is_string($rule)) ? explode('|', $rule) : $rule;
		}

		return $rules;
	}


	public function email ($field) {
		return true;
	}

	public function required($field) {

		$value = isset($_POST[$field]) ? $_POST[$field] : '';

		if (is_null($value))
		{
			return false;
		}

		elseif (is_string($value) and trim($value) === '')
		{
			return false;
		}

		return true;
	}

}