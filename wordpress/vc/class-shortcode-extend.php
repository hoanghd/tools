<?php
/**
 * Shortcode Extend.
 * 
 * @since 1.0
 */

SlzCore::load_class( 'Abstract' );

class SlzCore_Shortcode_Extend extends SlzCore_Abstract {
    public function market($settings, $value){
        $settings["params"] = array(
            array(
                "type" => "textfield",
                "heading" => __( "Database", 'slz-core' ),
                "param_name" => "database_code",
                "value" => ''
            ),
            array(
                "type" => "slz_dropdownmultiple",
                "heading" => __( "Dataset", 'slz-core' ),
                "param_name" => "dataset_code",
                "value" => array()
            )
        );
        
        return $this->render('market', array(
            'edit_form' => new Vc_ParamGroup_Edit_Form_Fields( $settings ),
            'value' => vc_param_group_parse_atts( $value ),
            'unparsed_value' => $value,
            'settings' => $settings,
        ), true);
    }
}