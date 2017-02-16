<?php global $acma_errors; ?>
<div class="form-group <?php if(isset($acma_errors[$name])) echo 'has-error'; ?>">
  <label class="control-label" for="<?php echo $id; ?>"><?php echo $label; ?></label>

  <div class="password-container">
    <div class="input-group">
      <input type="password"
      name="<?php echo $name; ?>"
      id="<?php echo $id; ?>"
      class="form-control"
      value="<?php echo $value; ?>"
      placeholder="<?php echo $placeholder;  ?>"
      <?php if(count($attrs) > 0){ foreach($attrs as $attr_key => $attr_value){
        echo $attr_key . '="' . $attr_value.'" ';
       }  } ?>
      >
      <div class="input-group-addon showpassword" title="<?php echo acma_trad('show_password'); ?>"><i class="fa fa-eye"></i></div>
    </div>
    <?php if($test_strength): ?>
      <div class="password-score"></div>
    <?php endif; ?>
  </div>
  <?php if(isset($acma_errors[$name])): ?><span class="help-block"><?php echo $acma_errors[$name]; ?></span><?php endif; ?>
</div>
