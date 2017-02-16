<?php global $acma_errors; ?>
<div class="form-group <?php if(isset($acma_errors[$name])) echo 'has-error'; ?>">
  <label class="label-control" for="<?php echo $id; ?>"><?php echo $label ?></label>
  <select class="form-control"
  name="<?php echo $name; ?>"
  id="<?php echo $id; ?>"
  <?php if(count($attrs) > 0){ foreach($attrs as $attr_key => $attr_value){
    echo $attr_key . '="' . $attr_value.'" ';
   }  } ?>
  >
    <?php
    if(count($choices)>0):
      foreach($choices as $choice_id  => $choice_name): ?>
      <option value="<?php echo $choice_id; ?>"
        <?php
        if(is_array($value)){
          if(in_array($choice_id, $value)) echo 'selected="selected"';
        }
        else{
          if($value == $choice_id) echo 'selected="selected"';
        }
        ?>
        ><?php echo $choice_name; ?></option>
    <?php endforeach;
    endif; ?>
  </select>
  <?php if(isset($acma_errors[$name])){ ?><span class="help-block"><?php echo $acma_errors[$name]; ?></span><?php } ?>
</div>
