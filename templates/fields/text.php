<?php global $acma_errors; ?>
<div class="form-group <?php if(isset($acma_errors[$name])) echo 'has-error'; ?>">
  <label class="label-control" for="<?php echo $id; ?>"><?php echo $label; ?></label>
  <input type="text" class="form-control"
  name="<?php echo $name; ?>"
  id="<?php echo $id; ?>"
  value="<?php echo $value; ?>"
  placeholder="<?php echo $placeholder;  ?>"
  <?php if(count($attrs) > 0){ foreach($attrs as $attr_key => $attr_value){
    echo $attr_key . '="' . $attr_value.'" ';
   }  } ?>
  >
  <?php if(isset($acma_errors[$name])){ ?><span class="help-block"><?php echo $acma_errors[$name]; ?></span><?php } ?>
</div>
