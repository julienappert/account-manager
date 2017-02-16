<?php global $acma_notif; ?>

<?php if(!is_null($acma_notif)): ?>
	<div class="alert alert-<?php echo $acma_notif['type']; ?>" role="alert"><?php echo $acma_notif['message']; ?></div>
<?php endif; ?>

<form id="acma-login" class="acma-form" action="<?php the_permalink(); ?>" method="post">

	<?php
	acma_field('email', 'email', acma_trad('login.your_email'), $email);
	acma_field('password', 'password', acma_trad('login.your_password'), $password, array('test_strength' => false));
	 ?>

	<p class="submit">
		<input class="btn btn-default" name="acma-login" value="<?php echo acma_trad('login.submit'); ?>" type="submit"/>
		<div class="forget"><a href="<?php echo wp_lostpassword_url(); ?>"><?php echo acma_trad('login.forgot_password'); ?></a></div>
		<input type="hidden" name="redirect_to" value="">
    <input type="hidden" name="login_nonce" value="<?php echo wp_create_nonce('acmalogin-nonce'); ?>"/>
	</p>
</form>
