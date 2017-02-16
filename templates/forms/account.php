<?php global $acma_errors, $acma_notif; ?>

<?php if(!is_null($acma_notif)): ?>
	<div class="alert alert-<?php echo $acma_notif['type']; ?>" role="alert"><?php echo $acma_notif['message']; ?></div>
<?php endif; ?>

<form class="acma-form" id="acma-account" action="" method="post">
	<?php
	do_action('acma_account_before');

	acma_field('email', 'email', acma_trad('account.your_email'), $email);
	acma_field('password', 'oldpassword', acma_trad('account.your_oldpassword'), $oldpassword, array('test_strength' => false));
	acma_field('password', 'password', acma_trad('account.your_password'), $password);

	do_action('acma_account_after');

	 ?>
	<p class="submit">
		<input class="btn btn-default" name="acma-account" value="<?php echo acma_trad('account.submit'); ?>" type="submit"/>
    <input type="hidden" name="account_nonce" value="<?php echo wp_create_nonce('acmaaccount-nonce'); ?>"/>
	</p>
</form>
