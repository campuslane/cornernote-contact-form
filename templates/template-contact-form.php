<form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="post">

	<?php $v = Cornernote\Validation::get_instance(); ?>

	
	

	<div class="form-group">
	<label for="cf-name">Your Name</label>

	
	<input class="form-control" type="text" name="cf-name"  value="" />
	</div>

	<div class="form-group">
	<label for="cf-email">Your Email</label>
	
	<input class="form-control" type="text" name="cf-email" value=""  />
	</div>
	
	<div class="form-group">
	<label for="cf-subject">Your Subject</label>
	
	<input class="form-control" type="text" name="cf-subject"  value=""  />
	</div>
	
	<div class="form-group">
	<label for="cf-message">Your Message</label>
	
	<textarea class="form-control"  rows="8" name="cf-message"></textarea>
	</div>

	<input type="hidden" name="cf-submitted" value="1">
	
	<div class="form-group">
	<input type="submit" class="btn btn-primary"  value="Send">
	</div>
</form>


	