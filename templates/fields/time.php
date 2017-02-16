<?php global $acma_errors; ?>
<div class="form-group <?php if(isset($acma_errors[$name])) echo 'has-error'; ?>">
  <label class="label-control" for="<?php echo $id; ?>"><?php echo $label; ?></label>
  <div class="input-group">
  <input type="text" class="form-time form-control"
  name="<?php echo $name; ?>"
  id="<?php echo $id; ?>"
  value="<?php echo $value; ?>"
  placeholder="<?php echo $placeholder;  ?>"
  <?php if(count($attrs) > 0){ foreach($attrs as $attr_key => $attr_value){
    echo $attr_key . '="' . $attr_value.'" ';
   }  } ?>
  >
  <div class="input-group-addon"><i class="fa fa-clock-o" aria-hidden="true"></i></div>
</div>
  <?php if(isset($acma_errors[$name])){ ?><span class="help-block"><?php echo $acma_errors[$name]; ?></span><?php } ?>
</div>
