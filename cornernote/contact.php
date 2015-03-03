<?php  namespace Cornernote;

/**
 * Contact
 * This is the contact form singleton class.  We made it a singleton so that you 
 * can easily get the instance of the plugin and use its public methods anywhere, 
 * by simply getting the current instance:  $cn = Cornernote\Contact::get_instance();
 * Now you can do something like $cn->some_method_name() from anywhere.  
 */


class Contact {

	private static $instance = null;
	private $plugin_path;
	private $validation;


	/**
	 * Constructor
	 * This constructor is private because we're using this class 
	 * as a singleton.  It sets up the plugin.
	 */
	private function __construct()
	{

		$this->set_plugin_path();
		$this->set_validation();
		$this->set_shortcodes();
		$this->set_actions();

	}

	/**
	 * Get Instance
	 * This is how the single instance of the class is retrieved.  If there is 
	 * no existing instance, the class is instantiated, otherwise we use 
	 * the existing instantiation
	 * @return class instance
	 */
	public static function get_instance()
	{
		if ( null == Contact::$instance) {
			Contact::$instance = new Contact;
		}

		return Contact::$instance;
	}


	/**
	 * Set Validation
	 * Get an instance of the validation class which is also a singleton.
	 */
	protected function set_validation()
	{
		$this->validation = Validation::get_instance();
	}


	/**
	 * Set the Plugin Path
	 * It's the parent of the current path.
	 */
	public function set_plugin_path()
	{
		$this->plugin_path =  plugin_dir_path(__FILE__) . '../';
	}


	/**
	 * Get the Plugin Path
	 * @return string - the server path for the base plugin directory
	 */
	public function get_plugin_path()
	{
		return $this->plugin_path;
	}

	
	/**
	 * Set up the WP Short Codes
	 */
	protected function set_shortcodes()
	{
		// The show_contact_form shortcode will display the contact form
		add_shortcode( 'show_contact_form', [$this, 'show_contact_form_handler'] );
	}


	/**
	 * Set up the WP Actions
	 */
	protected function set_actions()
	{
		// init action (before headers are sent), we check
		// to see if our contact form was submitted
		add_action( 'init', [$this, 'check_contact_form_submission'] );
	}


	/**
	 * Show Contact Form Handler
	 * Triggered by the 'show_shortcode_form' shortcode.  
	 * @return string
	 */
	public function show_contact_form_handler()
	{
		return $this->get_template( $this->plugin_path . 'templates/template-contact-form.php' );
	}


	/**
	 * Get Template
	 * Provide the server path, and you'll get back the php parsed 
	 * template content as a string
	 * @param  string $path - server path
	 * @return string - php parsed template
	 */
	public function get_template($path)
	{
		
		ob_start();
			require($path);
			$output = ob_get_contents();
		ob_end_clean();	

	    	return $output;
	}


	/**
	 * Code to send the mail on form submission
	 * @return ?
	 */
	protected function deliver_mail()
	{

		// if the submit button is clicked, send the email
		if ( isset( $_POST['cf-submitted'] ) and $_POST['cf-submitted'] == 1 ) {

			// sanitize form values
			$name    = sanitize_text_field( $_POST["cf-name"] );
			$email   = sanitize_email( $_POST["cf-email"] );
			$subject = sanitize_text_field( $_POST["cf-subject"] );
			$message = esc_textarea( $_POST["cf-message"] );

			// get the blog administrator's email address
			$to = get_option( 'admin_email' );

			$headers = "From: $name <$email>" . "\r\n";

			// If email has been process for sending, display a success message
			if ( wp_mail( $to, $subject, $message, $headers ) ) {

			wp_redirect( home_url());

			} else {
				$this->set_form_error("Oops we had a problem sending your email.");
			}
		}
		
	}

	/**
	 * Check if Contact Form was Submitted
	 * If it was, it will process the submitted info, and send the email.   
	 * @return redirect
	 */
	public function check_contact_form_submission()
	{

		if (isset($_POST['cf-submitted']) &&  $_POST['cf-submitted'] == 1) {


			$rules = [
				'cf-name' => 'required', 
				'cf-email' => 'required|email', 
				'cf-subject' => 'required', 
				'cf-message' => 'required', 
			];

			$messages = [

				'cf-name' => [
					'required' => 'The Name Field is Required...', 
				], 

				'cf-email' => [
					'required' => 'The Email Address is Required...', 
					'email' => 'Oops, it looks like the email address isn\'t formatted properly', 
				], 

				'cf-subject' => [
					'required' => 'The Subject Field is Required...', 
				], 

				'cf-message' => [
					'required' => 'The Message Field is Required...', 
				], 

			];

			$this->validation->check($_POST, $rules, $messages);


			if ($this->validation->passed() ) {
				$this->deliver_mail();
			}
			
		}
	}

}