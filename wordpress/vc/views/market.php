<div class="vc_param_group-list vc_settings" data-settings="<?php echo htmlentities( json_encode( $settings ), ENT_QUOTES, 'utf-8' );?>">
<?php
foreach ( $settings['params'] as $i=>$param ) {
    $param_value = isset( $value[ $param['param_name'] ] ) ? $value[ $param['param_name'] ] : ( isset( $param['value'] ) ? $param['value'] : '' );
    $param['param_name'] = $settings['param_name'] . '_' . $param['param_name'];
    
    echo $edit_form->renderField( $param, $param_value );
    if(empty($i)){
        echo '<button type="button">Find</button>';
    }
    echo "<br/>";
}
?>
<input name="<?php echo $settings['param_name'];?>" class="wpb_vc_param_value  <?php echo $settings['param_name'] . ' ' . $settings['type'];?>_field" type="hidden" value="<?php echo $unparsed_value;?>" />
</div>