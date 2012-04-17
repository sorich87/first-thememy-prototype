<?php
/**
 * Template Name: Survey Page
 *
 * @package thememy
 * @since ThemeMY! 0.1
 */

$email = isset( $_GET['email'] ) ? stripslashes( $_GET['email'] ) : '';

if ( ! isset( $_GET['success'] ) && ! get_user_by( 'email', $user_email ) )
	wp_die( __( "You don't have the rights to access this resource." ) );

get_header(); ?>

	<div class="page-header">
		<h1><?php _e( 'New User Survey' ); ?></h1>
	</div>

	<?php if ( isset( $_GET['success'] ) ) : ?>

	<p><?php printf(
		__( 'Thanks for answering the survey, your account is all setup for you and <a href="%s">you can now sign in</a>.' ),
 		home_url( '/' )
	); ?></p>

	<?php else : ?>

	<p><?php _e( "Please help us improve ThemeMY! by answering the following three questions. Answering is not mandatory. If you don't want to answer, just skip some or all the questions." ); ?></p>

	<form action="" method="post">
		<input type="hidden" name="email" value="<?php echo $email; ?>" />

		<fieldset>
			<legend><?php _e( 'Please answer these three (3) simple questions.' ); ?></legend>

			<div class="control-group">
				<label class="control-label" for="question_1"><b><?php _e( 'Did you already sell your themes somewhere else in the past?' ); ?></b></label>
				<div class="controls">
					<label class="radio">
						<input type="radio" id="question_1_yes" name="question_1" value="yes" />
						<?php _e( 'Yes' ); ?>
					</label>
					<label class="radio">
						<input type="radio" id="question_1_no" name="question_1" value="no" />
						<?php _e( 'No' ); ?>
					</label>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="question_2"><b><?php _e( 'If you answered "yes" to the question above, where did you sell your themes at?' ); ?></b></label>
				<div class="controls">
					<label class="radio">
						<input type="radio" id="question_2_marketplace" name="question_2" value="marketplace" />
						<?php _e( 'A theme marketplace' ); ?>
					</label>
					<label class="radio">
						<input type="radio" id="question_2_store" name="question_2" value="store" />
						<?php _e( 'Your own theme store' ); ?>
					</label>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="question_3"><b><?php _e( 'What is the biggest problem you expect ThemeMY! to solve for you now or in a near future?' ); ?></b></label>
				<div class="controls">
					<textarea class="input-xlarge" id="question_3" name="question_3" rows="3"></textarea>
				</div>
			</div>

			<div class="form-actions">
				<button type="submit" class="btn btn-primary"><?php _e( 'Send answers' ); ?></button>
				<a class="btn" href="<?php echo home_url( '/' ); ?>"><?php _e( 'Skip survey and login' ); ?></a>
			</div>
		</fieldset>
	</form>

	<?php endif; ?>

<?php get_footer(); ?>
