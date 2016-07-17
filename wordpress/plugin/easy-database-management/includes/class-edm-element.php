<?php
class EDM_Element{
    /**
     * Generates a text field input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated input field
     * @see clientChange
     * @see inputField
     */
    public static function textfield( $name, $value='', $htmlOptions=array() ){
        return self::inputField( 'text', $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a number field input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated input field
     * @see clientChange
     * @see inputField
     * @since 1.1.14
     */
    public static function numberField( $name, $value='', $htmlOptions=array() ) {
        return self::inputField( 'number', $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a range field input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated input field
     * @see inputField
     * @since 1.1.14
     */
    public static function rangeField( $name, $value='', $htmlOptions=array() ) {
        return self::inputField( 'range', $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a date field input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated input field
     * @see clientChange
     * @see inputField
     * @since 1.1.14
     */
    public static function dateField( $name, $value='', $htmlOptions=array() ) {
        return self::inputField( 'date', $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a time field input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated input field
     * @see clientChange
     * @see inputField
     * @since 1.1.14
     */
    public static function timeField( $name, $value='', $htmlOptions=array() ) {
        return self::inputField( 'time', $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a datetime field input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated input field
     * @see clientChange
     * @see inputField
     * @since 1.1.16
     */
    public static function dateTimeField( $name, $value='', $htmlOptions=array() ) {
        return self::inputField( 'datetime', $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a local datetime field input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated input field
     * @see clientChange
     * @see inputField
     * @since 1.1.16
     */
    public static function dateTimeLocalField( $name, $value='', $htmlOptions=array() ) {
        return self::inputField( 'datetime-local', $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a week field input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated input field
     * @see clientChange
     * @see inputField
     * @since 1.1.16
     */
    public static function weekField( $name, $value='', $htmlOptions=array() ) {
        return self::inputField( 'week', $name, $value, $htmlOptions );
    }

    /**
     * Generates an email field input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated input field
     * @see clientChange
     * @see inputField
     * @since 1.1.14
     */
    public static function emailField( $name, $value='', $htmlOptions=array()) {
        return self::inputField( 'email', $name, $value, $htmlOptions );
    }

    /**
     * Generates a telephone field input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated input field
     * @see clientChange
     * @see inputField
     * @since 1.1.14
     */
    public static function telField( $name, $value='', $htmlOptions=array() ) {
        return self::inputField( 'tel', $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a URL field input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated input field
     * @see clientChange
     * @see inputField
     * @since 1.1.14
     */
    public static function urlField( $name, $value='', $htmlOptions=array() ) {
        return self::inputField( 'url', $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a hidden input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes (see {@link tag}).
     * @return string the generated input field
     * @see inputField
     */
    public static function hiddenField( $name, $value='', $htmlOptions=array() ) {
        return self::inputField( 'hidden', $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a password field input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated input field
     * @see clientChange
     * @see inputField
     */
    public static function passwordField( $name, $value='', $htmlOptions=array() ) {
        return self::inputField( 'password', $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a file input.
     * Note, you have to set the enclosing form's 'enctype' attribute to be 'multipart/form-data'.
     * After the form is submitted, the uploaded file information can be obtained via $_FILES[$name] (see
     * PHP documentation).
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes (see {@link tag}).
     * @return string the generated input field
     * @see inputField
     */
    public static function fileField( $name, $value='', $htmlOptions=array() ) {
        return self::inputField( 'file', $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a text area input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated text area
     * @see clientChange
     * @see inputField
     */
    public static function textArea( $name, $value='', $htmlOptions=array() ) {
        $htmlOptions['name']=$name;
        
        if(!isset($htmlOptions['id']))
            $htmlOptions['id'] = self::getIdByName($name);
        elseif($htmlOptions['id']===false)
            unset($htmlOptions['id']);
            
        return self::tag( 'textarea', $htmlOptions, isset($htmlOptions['encode']) && !$htmlOptions['encode'] ? $value : self::encode($value) );
    }
    
    /**
     * Generates a radio button.
     * @param string $name the input name
     * @param boolean $checked whether the radio button is checked
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * Since version 1.1.2, a special option named 'uncheckValue' is available that can be used to specify
     * the value returned when the radio button is not checked. When set, a hidden field is rendered so that
     * when the radio button is not checked, we can still obtain the posted uncheck value.
     * If 'uncheckValue' is not set or set to NULL, the hidden field will not be rendered.
     * @return string the generated radio button
     * @see clientChange
     * @see inputField
     */
    public static function radioButton( $name, $checked=false, $htmlOptions=array() ) {
        if($checked)
            $htmlOptions['checked']='checked';
        else
            unset($htmlOptions['checked']);
            
        $value=isset($htmlOptions['value']) ? $htmlOptions['value'] : 1;
        
        if( array_key_exists( 'uncheckValue', $htmlOptions ) ) {
            $uncheck=$htmlOptions['uncheckValue'];
            unset($htmlOptions['uncheckValue']);
        }
        else
            $uncheck=null;
        
        if( $uncheck!==null ) {
            $hidden = self::hiddenField( $name, $uncheck, $uncheckOptions );
        }
        else
            $hidden='';
        
        // add a hidden field so that if the radio button is not selected, it still submits a value
        return $hidden . self::inputField( 'radio', $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a check box.
     * @param string $name the input name
     * @param boolean $checked whether the check box is checked
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * Since version 1.1.2, a special option named 'uncheckValue' is available that can be used to specify
     * the value returned when the checkbox is not checked. When set, a hidden field is rendered so that
     * when the checkbox is not checked, we can still obtain the posted uncheck value.
     * If 'uncheckValue' is not set or set to NULL, the hidden field will not be rendered.
     * @return string the generated check box
     * @see clientChange
     * @see inputField
     */
    public static function checkBox( $name, $checked=false, $htmlOptions=array() ) {
        if($checked)
            $htmlOptions['checked']='checked';
        else
            unset($htmlOptions['checked']);
        $value=isset($htmlOptions['value']) ? $htmlOptions['value'] : 1;
        
        if( array_key_exists( 'uncheckValue', $htmlOptions ) ) {
            $uncheck=$htmlOptions['uncheckValue'];
            unset($htmlOptions['uncheckValue']);
        }
        else
            $uncheck=null;
        
        if($uncheck!==null) {
            $hidden = self::hiddenField( $name, $uncheck, $uncheckOptions );
        }
        else
            $hidden='';
        
        // add a hidden field so that if the check box is not checked, it still submits a value
        return $hidden . self::inputField( 'checkbox', $name, $value, $htmlOptions );
    }

    /**
     * Generates a drop down list.
     * @param string $name the input name
     * @param string $select the selected value
     * @param array $data data for generating the list options (value=>display).
     * You may use {@link listData} to generate this data.
     * Please refer to {@link listOptions} on how this data is used to generate the list options.
     * Note, the values and labels will be automatically HTML-encoded by this method.
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * In addition, the following options are also supported specifically for dropdown list:
     * <ul>
     * <li>encode: boolean, specifies whether to encode the values. Defaults to true.</li>
     * <li>prompt: string, specifies the prompt text shown as the first list option. Its value is empty. Note, the prompt text will NOT be HTML-encoded.</li>
     * <li>empty: string, specifies the text corresponding to empty selection. Its value is empty.
     * The 'empty' option can also be an array of value-label pairs.
     * Each pair will be used to render a list option at the beginning. Note, the text label will NOT be HTML-encoded.</li>
     * <li>options: array, specifies additional attributes for each OPTION tag.
     *     The array keys must be the option values, and the array values are the extra
     *     OPTION tag attributes in the name-value pairs. For example,
     * <pre>
     *     array(
     *         'value1'=>array('disabled'=>true,'label'=>'value 1'),
     *         'value2'=>array('label'=>'value 2'),
     *     );
     * </pre>
     * </li>
     * </ul>
     * Since 1.1.13, a special option named 'unselectValue' is available. It can be used to set the value
     * that will be returned when no option is selected in multiple mode. When set, a hidden field is
     * rendered so that if no option is selected in multiple mode, we can still obtain the posted
     * unselect value. If 'unselectValue' is not set or set to NULL, the hidden field will not be rendered.
     * @return string the generated drop down list
     * @see clientChange
     * @see inputField
     * @see listData
     */
    public static function dropDownList( $name, $select, $data, $htmlOptions=array() ) {
        $htmlOptions['name']=$name;
        
        if( !isset( $htmlOptions['id'] ) )
            $htmlOptions['id'] = self::getIdByName( $name );
        elseif($htmlOptions['id']===false)
            unset($htmlOptions['id']);
            
        $options = "\n" . self::listOptions( $select, $data, $htmlOptions );
        $hidden = '';
        
        if(!empty($htmlOptions['multiple'])) {
            if(substr($htmlOptions['name'],-2)!=='[]')
                $htmlOptions['name'].='[]';
            
            if(isset($htmlOptions['unselectValue'])) {
                $hidden = self::hiddenField( substr( $htmlOptions['name'], 0, -2 ), $htmlOptions['unselectValue'] );
                unset( $htmlOptions['unselectValue'] );
            }
        }
        // add a hidden field so that if the option is not selected, it still submits a value
        return $hidden . self::tag( 'select', $htmlOptions ,$options );
    }
    
    /**
     * Generates a list box.
     * @param string $name the input name
     * @param mixed $select the selected value(s). This can be either a string for single selection or an array for multiple selections.
     * @param array $data data for generating the list options (value=>display)
     * You may use {@link listData} to generate this data.
     * Please refer to {@link listOptions} on how this data is used to generate the list options.
     * Note, the values and labels will be automatically HTML-encoded by this method.
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * attributes are also recognized. See {@link clientChange} and {@link tag} for more details.
     * In addition, the following options are also supported specifically for list box:
     * <ul>
     * <li>encode: boolean, specifies whether to encode the values. Defaults to true.</li>
     * <li>prompt: string, specifies the prompt text shown as the first list option. Its value is empty. Note, the prompt text will NOT be HTML-encoded.</li>
     * <li>empty: string, specifies the text corresponding to empty selection. Its value is empty.
     * The 'empty' option can also be an array of value-label pairs.
     * Each pair will be used to render a list option at the beginning. Note, the text label will NOT be HTML-encoded.</li>
     * <li>options: array, specifies additional attributes for each OPTION tag.
     *     The array keys must be the option values, and the array values are the extra
     *     OPTION tag attributes in the name-value pairs. For example,
     * <pre>
     *     array(
     *         'value1'=>array('disabled'=>true,'label'=>'value 1'),
     *         'value2'=>array('label'=>'value 2'),
     *     );
     * </pre>
     * </li>
     * </ul>
     * @return string the generated list box
     * @see inputField
     * @see listData
     */
    public static function listBox( $name, $select, $data, $htmlOptions=array() ) {
        if(!isset($htmlOptions['size']))
            $htmlOptions['size']=4;
        if(!empty($htmlOptions['multiple'])) {
            if(substr($name,-2)!=='[]')
                $name.='[]';
        }
        return self::dropDownList( $name, $select, $data, $htmlOptions );
    }

    /**
     * Generates a check box list.
     * A check box list allows multiple selection, like {@link listBox}.
     * As a result, the corresponding POST value is an array.
     * @param string $name name of the check box list. You can use this name to retrieve
     * the selected value(s) once the form is submitted.
     * @param mixed $select selection of the check boxes. This can be either a string
     * for single selection or an array for multiple selections.
     * @param array $data value-label pairs used to generate the check box list.
     * Note, the values will be automatically HTML-encoded, while the labels will not.
     * @param array $htmlOptions additional HTML options. The options will be applied to
     * each checkbox input. The following special options are recognized:
     * <ul>
     * <li>template: string, specifies how each checkbox is rendered. Defaults
     * to "{input} {label}", where "{input}" will be replaced by the generated
     * check box input tag while "{label}" be replaced by the corresponding check box label,
     * {beginLabel} will be replaced by &lt;label&gt; with labelOptions, {labelTitle} will be replaced
     * by the corresponding check box label title and {endLabel} will be replaced by &lt;/label&gt;</li>
     * <li>separator: string, specifies the string that separates the generated check boxes.</li>
     * <li>checkAll: string, specifies the label for the "check all" checkbox.
     * If this option is specified, a 'check all' checkbox will be displayed. Clicking on
     * this checkbox will cause all checkboxes checked or unchecked.</li>
     * <li>checkAllLast: boolean, specifies whether the 'check all' checkbox should be
     * displayed at the end of the checkbox list. If this option is not set (default)
     * or is false, the 'check all' checkbox will be displayed at the beginning of
     * the checkbox list.</li>
     * <li>labelOptions: array, specifies the additional HTML attributes to be rendered
     * for every label tag in the list.</li>
     * <li>container: string, specifies the checkboxes enclosing tag. Defaults to 'span'.
     * If the value is an empty string, no enclosing tag will be generated</li>
     * <li>baseID: string, specifies the base ID prefix to be used for checkboxes in the list.
     * This option is available since version 1.1.13.</li>
     * </ul>
     * @return string the generated check box list
     */
    public static function checkBoxList( $name, $select, $data, $htmlOptions=array() ) {
        $template=isset($htmlOptions['template'])?$htmlOptions['template']:'{input} {label}';
        $separator=isset($htmlOptions['separator'])?$htmlOptions['separator']:self::tag('br');
        unset( $htmlOptions['template'], $htmlOptions['separator'] );
       
        if(substr($name,-2)!=='[]')
            $name.='[]';
        
        if(isset($htmlOptions['checkAll'])) {
            $checkAllLabel = $htmlOptions['checkAll'];
            $checkAllLast=isset($htmlOptions['checkAllLast']) && $htmlOptions['checkAllLast'];
        }
        unset( $htmlOptions['checkAll'], $htmlOptions['checkAllLast'] );
        
        $labelOptions = isset($htmlOptions['labelOptions'])?$htmlOptions['labelOptions']:array();
        unset($htmlOptions['labelOptions']);
        
        $baseID=isset($htmlOptions['baseID']) ? $htmlOptions['baseID'] : self::getIdByName($name);
        unset($htmlOptions['baseID']);
        
        $items=array();
        $checkAll=true;
        $id=0;
        
        foreach($data as $value=>$labelTitle) {
            $checked=!is_array($select) && !strcmp($value,$select) || is_array($select) && in_array($value,$select);
            $checkAll=$checkAll && $checked;
            $htmlOptions['value'] = $value;
            $htmlOptions['id']    = $baseID.'_'.$id++;
            $option         = self::checkBox( $name, $checked, $htmlOptions );
            $beginLabel     = self::openTag( 'label', $labelOptions );
            $label          = self::label( $labelTitle, $htmlOptions['id'], $labelOptions );
            $endLabel       = self::closeTag( 'label' );
            $items[]        = strtr( $template, array(
                '{input}'        => $option,
                '{beginLabel}'   => $beginLabel,
                '{label}'        => $label,
                '{labelTitle}'   => $labelTitle,
                '{endLabel}'     => $endLabel,
            ));
        }
        
        return implode($separator, $items);
    }
    
    /**
     * Generates a radio button list.
     * A radio button list is like a {@link checkBoxList check box list}, except that
     * it only allows single selection.
     * @param string $name name of the radio button list. You can use this name to retrieve
     * the selected value(s) once the form is submitted.
     * @param string $select selection of the radio buttons.
     * @param array $data value-label pairs used to generate the radio button list.
     * Note, the values will be automatically HTML-encoded, while the labels will not.
     * @param array $htmlOptions additional HTML options. The options will be applied to
     * each radio button input. The following special options are recognized:
     * <ul>
     * <li>template: string, specifies how each radio button is rendered. Defaults
     * to "{input} {label}", where "{input}" will be replaced by the generated
     * radio button input tag while "{label}" will be replaced by the corresponding radio button label,
     * {beginLabel} will be replaced by &lt;label&gt; with labelOptions, {labelTitle} will be replaced
     * by the corresponding radio button label title and {endLabel} will be replaced by &lt;/label&gt;</li>
     * <li>separator: string, specifies the string that separates the generated radio buttons. Defaults to new line (<br/>).</li>
     * <li>labelOptions: array, specifies the additional HTML attributes to be rendered
     * for every label tag in the list.</li>
     * <li>container: string, specifies the radio buttons enclosing tag. Defaults to 'span'.
     * If the value is an empty string, no enclosing tag will be generated</li>
     * <li>baseID: string, specifies the base ID prefix to be used for radio buttons in the list.
     * This option is available since version 1.1.13.</li>
     * <li>empty: string, specifies the text corresponding to empty selection. Its value is empty.
     * The 'empty' option can also be an array of value-label pairs.
     * Each pair will be used to render a radio button at the beginning. Note, the text label will NOT be HTML-encoded.
     * This option is available since version 1.1.14.</li>
     * </ul>
     * @return string the generated radio button list
     */
    public static function radioButtonList( $name, $select, $data, $htmlOptions=array() ) {
        $template=isset($htmlOptions['template'])?$htmlOptions['template']:'{input} {label}';
        $separator=isset($htmlOptions['separator'])?$htmlOptions['separator']:self::tag('br');
        unset( $htmlOptions['template'], $htmlOptions['separator'] );
        
        $labelOptions=isset($htmlOptions['labelOptions'])?$htmlOptions['labelOptions']:array();
        unset($htmlOptions['labelOptions']);
        
        if( isset( $htmlOptions['empty'] ) ) {
            if( !is_array( $htmlOptions['empty'] ) )
                $htmlOptions['empty'] = array( ''=> $htmlOptions['empty'] );
            
            $data = self::mergeArray( $htmlOptions['empty'], $data );
            unset($htmlOptions['empty']);
        }
        
        $items=array();
        $baseID=isset($htmlOptions['baseID']) ? $htmlOptions['baseID'] : self::getIdByName($name);
        unset($htmlOptions['baseID']);
        $id=0;
        
        foreach( $data as $value=>$labelTitle ) {
            $checked = !strcmp( $value, $select );
            $htmlOptions['value']     = $value;
            $htmlOptions['id']        = $baseID.'_'.$id++;
            $option        = self::radioButton( $name, $checked, $htmlOptions );
            $beginLabel    = self::openTag( 'label', $labelOptions );
            $label         = self::label( $labelTitle, $htmlOptions['id'] ,$labelOptions );
            $endLabel      = self::closeTag('label');
            $items[]       = strtr($template, array(
                '{input}'        => $option,
                '{beginLabel}'   => $beginLabel,
                '{label}'        => $label,
                '{labelTitle}'   => $labelTitle,
                '{endLabel}'     => $endLabel,
            ));
        }
        
        return implode( $separator, $items );
    }
    
    /**
     * Generates a color picker input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated color picker input field
     * @see inputField
     */
    public static function colorpicker($name, $value, $htmlOptions=array()){
        if( empty( $htmlOptions[ 'class' ] ) )
            $htmlOptions[ 'class' ] = '';
        
        $htmlOptions[ 'class' ] .= ' wp-edm-color-picker';
        
        EDM_Management::register('admin.elements_scripts.colorpicker');
        return self::textfield( $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a datepicker input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * @return string the generated datepicker input field
     * @see inputField
     */
    public static function datepicker($name, $value, $htmlOptions=array()){
        if( empty( $htmlOptions[ 'class' ] ) )
            $htmlOptions[ 'class' ] = '';
        
        $htmlOptions[ 'class' ] .= ' wp-ope-date-picker';
        
        EDM_Management::register('admin.elements_scripts.datepicker');
        return self::textfield( $name, $value, $htmlOptions );
    }
    
    /**
     * Generates a wysiwyg input.
     * @param string $name the input name
     * @param string $value the input value
     * @return string the generated wysiwyg
     * @see inputField
     */
    public static function wysiwyg($name, $value){
        wp_editor( $value, $name );
    }
    
    /**
     * Generates a switch button.
     * @param string $name the input name
     * @param string $value the input value
     * @return string the generated switch button.
     * @see inputField
     */
    public static function switchButton($name, $checked=false, $htmlOptions = array()){
        $theme = isset( $htmlOptions['theme'] ) ? $htmlOptions['theme'] : '';
        $labelOn = isset( $htmlOptions['labelOn'] ) ? $htmlOptions['labelOn'] : 'On';
        $labelOff = isset( $htmlOptions['labelOff'] ) ? $htmlOptions['labelOff'] : 'Off';
        $template = isset( $htmlOptions['template'] ) ? $htmlOptions['template'] : '
            <label class="switch {theme}">
                {checkbox}
                <span class="switch-label" data-on="{labelOn}" data-off="{labelOff}"></span>
                <span class="switch-handle"></span>
            </label>';
            
        unset( $htmlOptions['template'], $htmlOptions['theme'], $htmlOptions['labelOn'], $htmlOptions['labelOff'] );
        
        EDM_Management::register('admin.elements_scripts.switchbutton');
        return strtr($template, array(
                '{checkbox}' => self::checkBox( $name, $checked, $htmlOptions ),
                '{theme}'    => $theme,
                '{labelOn}'  => $labelOn,
                '{labelOff}' => $labelOff
            ));
    }
    
    /**
     * Generates a slider.
     * @param string $name the input name
     * @param string $value the input value
     * @return string the generated slider.
     * @see inputField
     */
    public static function slider( $name, $value, $htmlOptions=array() ){
        $template       = isset($htmlOptions['template'])?$htmlOptions['template']:'{slider} {stepper}';
        $stepperOptions = isset($htmlOptions['stepper'])?$htmlOptions['stepper']:array();
        $sliderOptions  = isset($htmlOptions['slider'])?$htmlOptions['slider']:array();
        
        unset( $htmlOptions['template'], $htmlOptions['stepper'], $htmlOptions['slider']  );
        
        if( empty( $htmlOptions[ 'class' ] ) )
            $htmlOptions[ 'class' ] = '';
        
        $htmlOptions[ 'class' ] .= ' wp-edm-slider';
        $sliderOptions['value'] = $value;
        
        foreach($sliderOptions as $key=>$val){
            $htmlOptions[ 'data-' . $key ] = $val;
        }
        
        EDM_Management::register('admin.elements_scripts.numeric_stepper');
        return self::openTag('div', array('class' => 'wp-edm-group-slider')) . strtr($template, array(
            '{slider}' => self::tag( 'div', $htmlOptions, '' ),
            '{stepper}'  => self::stepper( $name, $value, $stepperOptions )
        )) . '</div>';
    }
    
    /**
     * Generates a slider.
     * @param string $name the input name
     * @param string $value the input value
     * @return string the generated slider.
     * @see inputField
     */
    public static function stepper( $name, $value, $htmlOptions=array()){
        $options = isset($htmlOptions['options'])?$htmlOptions['options']:array();
        unset( $htmlOptions['options'] );
        
        if( empty( $htmlOptions[ 'class' ] ) )
            $htmlOptions[ 'class' ] = '';
        
        $htmlOptions[ 'class' ] .= ' wp-edm-stepper';
        
        foreach($options as $key=>$val){
            $htmlOptions[ 'data-' . $key ] = $val;
        }
        
        EDM_Management::register('admin.elements_scripts.numeric_stepper');
        return self::textfield( $name, $value, $htmlOptions );
    }

    /**
     * Generates a select2.
     * @param string $name the input name
     * @param string $value the input value
     * @return string the generated select2.
     * @see inputField
     */
    public static function select2( $name, $select, $data, $htmlOptions=array() ){
        $options = isset($htmlOptions['options'])?$htmlOptions['options']:array();
        unset( $htmlOptions['options'] );
        
        if( empty( $htmlOptions[ 'class' ] ) )
            $htmlOptions[ 'class' ] = '';
        
        $htmlOptions[ 'class' ] .= ' wp-edm-select2';
        
        foreach($options as $key=>$val){
            $htmlOptions[ 'data-' . $key ] = $val;
        }
        
        EDM_Management::register('admin.elements_scripts.select2');
        return self::dropDownList( $name, $select, $data, $htmlOptions );
    }
    
    /**
     * Generates a slider.
     * @param string $name the input name
     * @param string $value the input value
     * @return string the generated slider.
     * @see inputField
     */
    public static function media($name, $value=NULL, $htmlOptions = array() ){
        $template = isset( $htmlOptions['template'] ) ? $htmlOptions['template'] : '
            <div class="wp-edm-media-group">
                {hidden}
                <div class="image"></div>
                <div class="sizes"><select></select></div>
                <button class="remove">Remove</button>
                <button class="open">Select Image</button>
            </div>';
        $options = isset( $htmlOptions['options'] ) ? $htmlOptions['options'] : array();
        unset( $htmlOptions['options'], $htmlOptions['template'] );
        
        if( !empty($value) ) {
            foreach( json_decode($value, true ) as $key=>$val ) {
                $options[ $key ] = $val; 
            }
            
            if( isset($options[ 'attachment_id' ] ) ) {
                $post = get_post( $options[ 'attachment_id' ] );
                $metadata = wp_get_attachment_metadata( $options[ 'attachment_id' ] );
                if( isset($metadata['sizes']) ) {
                    $options[ 'sizes' ] = array();
                    foreach( $metadata['sizes'] as $key=>$row ) {
                        $options[ 'sizes' ][ $key ] = "{$key} {$row['width']}x{$row['height']}";
                    }
                }
                
                if(  $post && $post->guid ) {
                    $options[ 'image' ] = $post->guid;
                }
            }
        }
        
        if( empty( $htmlOptions[ 'class' ] ) )
            $htmlOptions[ 'class' ] = '';
        
        $htmlOptions[ 'class' ] .= ' hidden';
        
        foreach($options as $key=>$val){
            $htmlOptions[ 'data-' . $key ] = $val;
        }
        
        EDM_Management::register('admin.elements_scripts.media');
        return strtr($template, array(
            '{hidden}' => self::hiddenField( $name , $value , $htmlOptions )
        ));
    }
    
    /**
     * Generates an input HTML tag.
     * This method generates an input HTML tag based on the given input name and value.
     * @param string $type the input type (e.g. 'text', 'radio')
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes for the HTML tag (see {@link tag}).
     * @return string the generated input tag
     */
    protected static function inputField( $type, $name, $value, $htmlOptions) {
        $htmlOptions['type']  = $type;
        $htmlOptions['value'] = $value;
        $htmlOptions['name']  = $name;
        
        if( !isset( $htmlOptions['id'] ) )
            $htmlOptions['id'] = self::getIdByName( $name );
        elseif( $htmlOptions['id']===false )
            unset($htmlOptions['id']);
            
        return self::tag('input', $htmlOptions);
    }
    
    /**
     * Encodes special characters into HTML entities.
     * @param string $text data to be encoded
     * @return string the encoded data
     * @see http://www.php.net/manual/en/function.htmlspecialchars.php
     */
    public static function encode( $text ) {
        return htmlspecialchars( $text, ENT_QUOTES );
    }

    /**
     * Decodes special HTML entities back to the corresponding characters.
     * This is the opposite of {@link encode()}.
     * @param string $text data to be decoded
     * @return string the decoded data
     * @see http://www.php.net/manual/en/function.htmlspecialchars-decode.php
     * @since 1.1.8
     */
    public static function decode( $text ) {
        return htmlspecialchars_decode( $text , ENT_QUOTES );
    }
    
    /**
     * Encodes special characters in an array of strings into HTML entities.
     * Both the array keys and values will be encoded if needed.
     * If a value is an array, this method will also encode it recursively.
     * @param array $data data to be encoded
     * @return array the encoded data
     * @see http://www.php.net/manual/en/function.htmlspecialchars.php
     */
    public static function encodeArray($data) {
        $d = array();
        foreach( $data as $key=>$value ) {
            if( is_string( $key ) )
                $key = htmlspecialchars( $key, ENT_QUOTES );
            if( is_string( $value ) )
                $value = htmlspecialchars( $value, ENT_QUOTES );
            elseif( is_array( $value ) )
                $value = self::encodeArray( $value );
            $d[$key] = $value;
        }
        
        return $d;
    }
    
    /**
     * Renders the HTML tag attributes.
     * Since version 1.1.5, attributes whose value is null will not be rendered.
     * Special attributes, such as 'checked', 'disabled', 'readonly', will be rendered
     * properly based on their corresponding boolean value.
     * @param array $htmlOptions attributes to be rendered
     * @return string the rendering result
     */
    public static function renderAttributes( $htmlOptions ) {
        static $specialAttributes = array(
            'autofocus'=>1,
            'autoplay'=>1,
            'async'=>1,
            'checked'=>1,
            'controls'=>1,
            'declare'=>1,
            'default'=>1,
            'defer'=>1,
            'disabled'=>1,
            'formnovalidate'=>1,
            'hidden'=>1,
            'ismap'=>1,
            'itemscope'=>1,
            'loop'=>1,
            'multiple'=>1,
            'muted'=>1,
            'nohref'=>1,
            'noresize'=>1,
            'novalidate'=>1,
            'open'=>1,
            'readonly'=>1,
            'required'=>1,
            'reversed'=>1,
            'scoped'=>1,
            'seamless'=>1,
            'selected'=>1,
            'typemustmatch'=>1,
        );
        
        if( $htmlOptions===array() )
            return '';
        
        $html = '';
        if( isset( $htmlOptions['encode'] ) ) {
            $raw = !$htmlOptions['encode'];
            unset( $htmlOptions['encode'] );
        }
        else
            $raw = false;
        
        foreach( $htmlOptions as $name=>$value ) {
            if( isset( $specialAttributes[ $name ] ) ) {
                if( $value === false && $name === 'async' ) {
                    $html .= ' ' . $name.'="false"';
                }
                elseif( $value ) {
                    $html .= ' ' . $name . '="' . $name . '"';
                }
            }
            else if( $value !== null )
                $html .= ' ' . $name . '="' . ($raw ? $value : self::encode( is_array( $value ) ? json_encode( $value ) : $value)) . '"';
        }
        
        return $html;
    }
    
    /**
     * Generates an HTML element.
     * @param string $tag the tag name
     * @param array $htmlOptions the element attributes. The values will be HTML-encoded using {@link encode()}.
     * If an 'encode' attribute is given and its value is false,
     * the rest of the attribute values will NOT be HTML-encoded.
     * Since version 1.1.5, attributes whose value is null will not be rendered.
     * @param mixed $content the content to be enclosed between open and close element tags. It will not be HTML-encoded.
     * If false, it means there is no body content.
     * @param boolean $closeTag whether to generate the close tag.
     * @return string the generated HTML element tag
     */
    public static function tag($tag, $htmlOptions=array(), $content=false, $closeTag=true) {
        $html='<' . $tag . self::renderAttributes( $htmlOptions );
        if($content===false)
            return $closeTag ? $html.' />' : $html.'>';
        else
            return $closeTag ? $html.'>'.$content.'</'.$tag.'>' : $html.'>'.$content;
    }
    
    /**
     * Generates an open HTML element.
     * @param string $tag the tag name
     * @param array $htmlOptions the element attributes. The values will be HTML-encoded using {@link encode()}.
     * If an 'encode' attribute is given and its value is false,
     * the rest of the attribute values will NOT be HTML-encoded.
     * Since version 1.1.5, attributes whose value is null will not be rendered.
     * @return string the generated HTML element tag
     */
    public static function openTag($tag, $htmlOptions=array()) {
        return '<' . $tag . self::renderAttributes( $htmlOptions ) . '>';
    }
    
    /**
     * Generates a close HTML element.
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    public static function closeTag($tag) {
        return '</'.$tag.'>';
    }
    
    /**
     * Encloses the given string within a CDATA tag.
     * @param string $text the string to be enclosed
     * @return string the CDATA tag with the enclosed content.
     */
    public static function cdata($text) {
        return '<![CDATA[' . $text . ']]>';
    }
    
    /**
     * Merges two or more arrays into one recursively.
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from array_merge_recursive).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array.
     * @param array $a array to be merged to
     * @param array $b array to be merged from. You can specify additional
     * arrays via third argument, fourth argument etc.
     * @return array the merged array (the original arrays are not changed.)
     * @see mergeWith
     */
    public static function mergeArray($a,$b) {
        $args = func_get_args();
        $res  = array_shift($args);
        while(!empty($args)) {
            $next=array_shift($args);
            foreach($next as $k => $v) {
                if(is_integer($k))
                    isset($res[$k]) ? $res[]=$v : $res[$k]=$v;
                elseif(is_array($v) && isset($res[$k]) && is_array($res[$k]))
                    $res[$k]=self::mergeArray($res[$k],$v);
                else
                    $res[$k]=$v;
            }
        }
        return $res;
    }
    
    /**
     * Generates the list options.
     * @param mixed $selection the selected value(s). This can be either a string for single selection or an array for multiple selections.
     * @param array $listData the option data (see {@link listData})
     * @param array $htmlOptions additional HTML attributes. The following two special attributes are recognized:
     * <ul>
     * <li>encode: boolean, specifies whether to encode the values. Defaults to true.</li>
     * <li>prompt: string, specifies the prompt text shown as the first list option. Its value is empty. Note, the prompt text will NOT be HTML-encoded.</li>
     * <li>empty: string, specifies the text corresponding to empty selection. Its value is empty.
     * The 'empty' option can also be an array of value-label pairs.
     * Each pair will be used to render a list option at the beginning. Note, the text label will NOT be HTML-encoded.</li>
     * <li>options: array, specifies additional attributes for each OPTION tag.
     *     The array keys must be the option values, and the array values are the extra
     *     OPTION tag attributes in the name-value pairs. For example,
     * <pre>
     *     array(
     *         'value1'=>array('disabled'=>true,'label'=>'value 1'),
     *         'value2'=>array('label'=>'value 2'),
     *     );
     * </pre>
     * </li>
     * <li>key: string, specifies the name of key attribute of the selection object(s).
     * This is used when the selection is represented in terms of objects. In this case,
     * the property named by the key option of the objects will be treated as the actual selection value.
     * This option defaults to 'primaryKey', meaning using the 'primaryKey' property value of the objects in the selection.
     * This option has been available since version 1.1.3.</li>
     * </ul>
     * @return string the generated list options
     */
    public static function listOptions( $selection, $listData, &$htmlOptions ) {
        $raw=isset($htmlOptions['encode']) && !$htmlOptions['encode'];
        $content='';
        if( isset( $htmlOptions['prompt'] ) ) {
            $content.='<option value="">'.strtr( $htmlOptions['prompt'], array('<'=>'&lt;' , '>'=>'&gt;'  ))."</option>\n";
            unset( $htmlOptions['prompt'] );
        }
        if(isset($htmlOptions['empty'])) {
            if(!is_array($htmlOptions['empty']))
                $htmlOptions['empty']=array(''=>$htmlOptions['empty']);
            foreach($htmlOptions['empty'] as $value=>$label)
                $content.='<option value="'.self::encode($value).'">'.strtr($label,array('<'=>'&lt;','>'=>'&gt;'))."</option>\n";
            unset($htmlOptions['empty']);
        }
        
        if(isset($htmlOptions['options'])) {
            $options=$htmlOptions['options'];
            unset($htmlOptions['options']);
        }
        else
            $options=array();
        
        $key=isset($htmlOptions['key']) ? $htmlOptions['key'] : 'primaryKey';
        if(is_array($selection)) {
            foreach($selection as $i=>$item) {
                if(is_object($item))
                    $selection[$i]=$item->$key;
            }
        }
        elseif(is_object($selection))
            $selection=$selection->$key;
        
        foreach($listData as $key=>$value) {
            if(is_array($value)) {
                $content.='<optgroup label="'.($raw?$key : self::encode($key))."\">\n";
                $dummy=array('options'=>$options);
                if(isset($htmlOptions['encode']))
                    $dummy['encode']=$htmlOptions['encode'];
                $content.=self::listOptions($selection,$value,$dummy);
                $content.='</optgroup>'."\n";
            }
            else {
                $attributes=array('value'=>(string)$key,'encode'=>!$raw);
                if(!is_array($selection) && !strcmp($key,$selection) || is_array($selection) && in_array($key,$selection))
                    $attributes['selected']='selected';
                if(isset($options[$key]))
                    $attributes=array_merge($attributes,$options[$key]);
                $content.=self::tag('option',$attributes,$raw?(string)$value : self::encode((string)$value))."\n";
            }
        }
        
        unset($htmlOptions['key']);
        
        return $content;
    }
 
    /**
     * Generates a label tag.
     * @param string $label label text. Note, you should HTML-encode the text if needed.
     * @param string $for the ID of the HTML element that this label is associated with.
     * If this is false, the 'for' attribute for the label tag will not be rendered.
     * @param array $htmlOptions additional HTML attributes.
     * The following HTML option is recognized:
     * @return string the generated label tag
     */
    public static function label($label, $for, $htmlOptions=array()) {
        if($for===false)
            unset($htmlOptions['for']);
        else
            $htmlOptions['for']=$for;
        
        return self::tag( 'label', $htmlOptions, $label );
    }
    
    /**
     * Generates a valid HTML ID based on name.
     * @param string $name name from which to generate HTML ID
     * @return string the ID generated based on name.
     */
    public static function getIdByName($name) {
        return str_replace(array('[]','][','[',']',' '),array('','_','_','','_'),$name);
    }
}
