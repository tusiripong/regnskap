<div class="jumbotron" text-align="center">
	<h1>Welcome to Financial</h1>

	<?php if (isset($current_user->email)) : ?>
		<a href="<?php echo site_url(SITE_AREA).'/content?without_unlock=1'; ?>" class="btn btn-large btn-success">Go to the Regnskap Admin</a>
	<?php else :?>
		<a href="<?php echo site_url(LOGIN_URL); ?>" class="btn btn-large btn-primary"><?php echo lang('bf_action_login'); ?></a>
	<?php endif;?>
	
</div>

