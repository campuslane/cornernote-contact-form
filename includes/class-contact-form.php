<?php namespace CampusLane;


/**
 * Frank Plugin Main Class
 * 
 */
class Contact_Form {

	public $plugin_path;
	protected $validation;

	/**
	 * Constructor
	 * Set things up
	 */
	public function __construct( $plugin_path )
	{
		$this->plugin_path = $plugin_path;
		$this->set_validation();
		$this->set_shortcodes();
		$this->set_actions();
	}

	protected function set_validation()
	{
		require_once( $this->plugin_path . 'includes/class-validation.php' );
		$this->validation = new Validation( $this->plugin_path );
	}

	protected function set_shortcodes()
	{
		add_shortcode( 'show_contact_form', [$this, 'contact_form_shortcode'] );
	}

	protected function set_actions()
	{
		add_action( 'init', [$this, 'contact_form_submitted'] );
	}

	/**
	 * Contact Form Shortcode Handler
	 * Triggered by the 'show_shortcode' shortcode.  
	 * @return string
	 */
	public function contact_form_shortcode()
	{
	    	global $contact_form_error;
		global $cornernote_validation;

		$cornernote_validation = $this->validation;

		$output = '';

		ob_start();
			require $this->plugin_path . 'templates/template-contact-form.php';
			$output .= ob_get_contents();
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
				global $cornernote_validation;
				$cornernote_validation->set_form_error("Oops we had a problem sending your email.");
			}
		}
		
	}


	

	/**
	 * Contact Form Submitted
	 * Triggered by the 'template_redirect' action set in run method.  Checks if 
	 * the contact form was submitted.  If it was, it will process the submitted 
	 * info, and send the email.   
	 * 
	 * @return redirect
	 */
	public function contact_form_submitted()
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

			global $cornernote_validation;
			$cornernote_validation = $this->validation;

			if ($this->validation->passed() ) {
				$this->deliver_mail();
			}
			
		}
	}

}