<?php global $acma_notif; ?>

<?php if(!is_null($acma_notif)): ?>
	<div class="alert alert-<?php echo $acma_notif['type']; ?>" role="alert"><?php echo $acma_notif['message']; ?></div>
<?php endif; ?>

<form class="acma-form" id="acma-changepassword" action="<?php the_permalink(); ?>" method="post">

	<?php acma_field('password', 'password', acma_trad('changepassword.new_password'), $password); ?>

	<p class="submit">
		<input class="btn btn-default" name="acma-changepassword" value="<?php echo acma_trad('changepassword.submit'); ?>" type="submit"/>
    <input type="hidden" name="changepassword_nonce" value="<?php echo wp_create_nonce('acmachangepassword-nonce'); ?>"/>
    <input type="hidden" name="email" value="<?php if(isset($_GET['email'])) echo $_GET['email'] ?>"/>
	</p>
</form>
