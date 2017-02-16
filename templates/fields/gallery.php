<?php $value = explode(',',$value); ?>

<div class="form-group form-gallery">
  <label><?php echo $label; ?></label>
  <div class="gallery_actions">
      <button type="button" class="browse btn btn-default">Parcourir...</button>
      <button type="button" class="start-upload btn btn-primary">Transférer les photos</button>
  </div>
  <input type="hidden" name="<?php echo $name; ?>" class="gallery_ids" id="<?php echo $id; ?>" value="<?php if(is_array($value)) echo implode(',',$value); ?>">
  <input type="hidden" class="gallery_postid" value="<?php if(isset($postid)) echo $postid; ?>">
  <ul class="filelist"></ul>
  <div class="console"></div>

  <input type="hidden" name="acma_ajax_nonce" class="acma_ajax_nonce" value="<?php echo wp_create_nonce('acma-ajax-nonce'); ?>">

  <div class="photos-thumbnail">
    <ul>
      <?php if(is_array($value)): ?>
        <?php if(count($value)>0): ?>
          <?php foreach($value as $photoid): $src = wp_get_attachment_image_src($photoid, 'medium'); ?>
          <li class="photo-thumbnail" id="<?php echo $photoid; ?>">
            <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-trash"></i></button>
            <div class="img" style="background-image:url(<?php echo $src[0]; ?>)"></div>
          </li>
          <?php endforeach; ?>
        <?php endif; ?>
      <?php endif; ?>
    </ul>
    <div class="clearfix"></div>
  </div>
</div>
