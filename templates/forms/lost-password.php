<?php global $acma_notif; ?>

<?php if( !is_null($acma_notif) ): ?>
	<div class="alert alert-<?php echo $acma_notif['type']; ?>" role="alert"><?php echo $acma_notif['message']; ?></div>
<?php endif; ?>

<form class="acma-form form-inline" id="acma-lostpassword" action="<?php the_permalink(); ?>" method="post">

	<?php acma_field('email', 'email', acma_trad('lostpassword.your_email'), $email); ?>

	<input class="btn btn-default" name="acma-lostpassword" value="<?php echo acma_trad('lostpassword.submit'); ?>" type="submit"/>
  <input type="hidden" name="lostpassword_nonce" value="<?php echo wp_create_nonce('acmalostpassword-nonce'); ?>"/>
</form>
