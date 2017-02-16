<?php global $acma_errors, $acma_notif; ?>

<?php if(!is_null($acma_notif)): ?>
	<div class="alert alert-<?php echo $acma_notif['type']; ?>" role="alert"><?php echo $acma_notif['message']; ?></div>
<?php endif; ?>

<form class="acma-form" id="acma-register" action="<?php the_permalink(); ?>" method="post">

	<?php

	do_action('acma_register_before');

	acma_field('email', 'email', acma_trad('account.your_email'), $email);
	acma_field('password', 'password', acma_trad('register.your_password'), $password);

	do_action('acma_register_after');
	?>

	<p class="submit">
		<input class="btn btn-default" name="acma-register" value="<?php echo acma_trad('register.submit'); ?>" type="submit"/>
    <input type="hidden" name="redirect_to" value="">
    <input type="hidden" name="register_nonce" value="<?php echo wp_create_nonce('acmaregister-nonce'); ?>"/>
	</p>
</form>
