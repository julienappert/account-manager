<?php global $acma_errors; ?>
<div class="form-group <?php if(isset($acma_errors[$name])) echo 'has-error'; ?>">
<?php if(strlen($label) >0 ): ?>
<label class="label-control"><?php echo $label; ?></label>
<?php endif; ?>
<?php
if(count($choices)>0):
  foreach($choices as $choice_id  => $choice_name):
    if($inline):
      ?>
        <label class="checkbox-inline">
          <input type="checkbox" name="<?php echo $name; ?>" value="<?php echo $choice_id; ?>" <?php if($value == $choice_id) echo 'checked="checked"'; ?>>
          <?php echo $choice_name; ?>
        </label>
      <?php
    else:
      ?>
      <div class="checkbox">
        <label>
          <input type="checkbox" name="<?php echo $name; ?>" value="<?php echo $choice_id; ?>" <?php if($value == $choice_id) echo 'checked="checked"'; ?>>
          <?php echo $choice_name; ?>
        </label>
      </div>
      <?php
    endif;
  endforeach;
endif; ?>
  <?php if(isset($acma_errors[$name])){ ?><span class="help-block"><?php echo $acma_errors[$name]; ?></span><?php } ?>
</div>
