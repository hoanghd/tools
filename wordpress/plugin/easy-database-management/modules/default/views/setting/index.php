<div style="padding:50px;background:#FFF;">
<hr/>
<label>Textfield</label><br/>
<?php echo EDM_Element::textfield('textfield', '', array('class'=>''));?>
<hr/>
<label>NumberField</label><br/>
<?php echo EDM_Element::numberField('numberField', '');?>
<hr/>
<label>RangeField</label><br/>
<?php echo EDM_Element::rangeField('rangeField', '');?>
<hr/>
<label>TextArea</label><br/>
<?php echo EDM_Element::textArea('textArea', '');?>
<hr/>
<label>RadioButton</label><br/>
<?php echo EDM_Element::radioButton('radioButton', true);?>
<hr/>
<label>CheckBox</label><br/>
<?php echo EDM_Element::checkBox('checkBox', true);?>
<hr/>
<label>DropDownList</label><br/>
<?php echo EDM_Element::dropDownList('dropDownList', 1, array('Red', 'Blue'));?>
<hr/>
<label>Select2</label><br/>
<?php echo EDM_Element::select2('dropDownList', 1, array('Red', 'Blue'));?>
<hr/>
<label>ListBox</label><br/>
<?php echo EDM_Element::listBox('listBox', 1, array('Red', 'Blue'));?>
<hr/>
<label>CheckBoxList</label><br/>
<?php echo EDM_Element::checkBoxList('checkBoxList', 1, array('Red', 'Blue'));?>
<hr/>
<label>RadioButtonList</label><br/>
<?php echo EDM_Element::radioButtonList('radioButtonList', 1, array('Red', 'Blue'));?>
<hr/>
<label>Slider</label><br/>
<?php echo EDM_Element::slider('slider', 50);?>
<hr/>
<label>Switch Button</label><br/>
<?php echo EDM_Element::switchButton('switchButton', true, array(
    'checkedLabel' => 'YES',
    'uncheckedLabel' => 'NO'
));?>
<hr/>
<label>Color Picker</label><br/>
<?php echo EDM_Element::colorpicker('colorpicker', '#dd4f33');?>
<hr/>
<label>Datepicker</label><br/>
<?php echo EDM_Element::datepicker('datepicker', '');?>
<hr/>
<label>Stepper</label><br/>
<?php echo EDM_Element::stepper('stepper', 5);?>
<hr/>
<label>Media</label><br/>
<?php 
//, json_encode(array('attachment_id'=>33, 'attachment_size'=>'large'))
echo EDM_Element::media('media');?>
<hr/>
<label>Wysiwyg</label><br/>
<?php echo EDM_Element::wysiwyg('wysiwyg', '');?>
</div>
