<?php
return array(
    'core' => array(
        array( 'add_action', 'admin_menu', 'fn(~.admin.menu)' ),
        array( 'add_action', 'admin_enqueue_scripts', 'fn(~.admin.scripts_default)' ),
        array( 'add_action', 'admin_enqueue_scripts', 'fn(~.admin.elements_default|0|/\_ope\-/)' )//
    ),
    'admin' => array(
        'menu' => array(
            'setup'      => array( 'add_menu_page', esc_html__( 'Super Social Share', OPE_LANG ), esc_html__( 'Super Social Share', OPE_LANG ), 'access_cp', 'ope-super-social-share', 'fn(Default_Setup.index)', 'div' ),
            'setting'    => array( 'add_submenu_page', 'ope-super-social-share', esc_html__( 'Advanced Settings', OPE_LANG ), esc_html__( 'Advanced Settings', OPE_LANG ), 'access_cp','ope-advanced-settings', 'fn(Default_Setting.index)'),
            'tool'       => array( 'add_submenu_page', 'ope-super-social-share', esc_html__( 'Export/Import', OPE_LANG ), esc_html__( 'Export/Import', OPE_LANG ), 'access_cp','ope-export-import', 'fn(Default_Tool.index)'),
            'analytics'  => array( 'add_submenu_page', 'ope-super-social-share', esc_html__( 'Analytics', OPE_LANG ), esc_html__( 'Analytics', OPE_LANG ), 'access_cp','ope-analytics', 'fn(Default_Analytics.index)'),
            'customizer' => array( 'add_submenu_page', 'ope-advanced-settings', 'Hidden!', 'Hidden!', 'access_cp', 'ope-customizer', 'fn(Default_Customizer.index)'),
            
            array( 'add_action', 'admin_print_scripts-{admin.menu.setup}', 'fn(~.admin.pages.setup_script)'),
            array( 'add_action', 'admin_print_scripts-{admin.menu.tool}', 'fn(~.admin.pages.tool_script)'),
            array( 'add_action', 'admin_print_scripts-{admin.menu.analytics}', 'fn(~.admin.pages.analytics_script)'),
            array( 'add_action', 'admin_print_scripts-{admin.menu.setting}', 'fn(~.admin.pages.settings_script)'),
            array( 'add_action', 'admin_print_scripts-{admin.menu.customizer}', 'fn(~.admin.pages.customizer_script)'),
            
            array( 'add_action', 'admin_menu', 'fn(Setting.admin_menu_rename)', 9999 ),
        ),
        'scripts_default' => array(
            array( 'wp_enqueue_script', 'jQuery' ),
            array( 'wp_enqueue_script', 'backbone' ),
            array( 'wp_enqueue_script', 'underscore' ),
            array( 'wp_enqueue_script', 'ope-modernizr-script', plugins_url('/assets/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js', __FILE__) ),
            array( 'wp_enqueue_script', 'ope-form-validator-script', plugins_url('/assets/libs/jquery.form-validator/jquery.form-validator.min.js', __FILE__) ),
            array( 'wp_enqueue_style', 'ope-ionicons-style', plugins_url('/assets/libs/ionicons/css/ionicons.min.css',__FILE__) ),
            array( 'wp_enqueue_style', 'ope-normalize-style', plugins_url('/assets/css/normalize.min.css',__FILE__) ),
            array( 'wp_enqueue_style', 'ope-core-style', plugins_url('/assets/css/core.css',__FILE__) ),
            array( 'wp_enqueue_style', 'ope-main-style', plugins_url('/assets/css/main.css',__FILE__) ),
        ),
        'elements_default' => array(
            array( 'wp_enqueue_script', 'jquery-ui-core' ),
            array( 'wp_enqueue_script', 'jquery-effects-core' ),
            array( 'wp_enqueue_style', 'jquery-ui-style', plugins_url('/assets/libs/jquery-ui/jquery-ui.min.css',__FILE__) ),
            array( 'wp_enqueue_script', 'jquery.tmpl', plugins_url('/assets/libs/jquery.tmpl/jquery.tmpl.min.js', __FILE__), array(), false, true ),
            array( 'wp_enqueue_style', 'ope-element-style', plugins_url('/assets/css/element.css',__FILE__)),
            array( 'wp_enqueue_script', 'ope-element-script', plugins_url('/assets/js/element.js', __FILE__), array(), false, true ),
            array( 'wp_enqueue_script', 'ope-dom-script', plugins_url('/assets/customizer/dom.js', __FILE__), array(), false, true ),
        ),
        'elements_scripts' => array(
            'media' => array(
                array('wp_enqueue_media')
            ),
            'colorpicker' => array(
                array( 'wp_enqueue_style', 'wp-color-picker' ),
                array( 'wp_enqueue_script', 'wp-color-picker' )
            ),
            'datepicker' => array(
                array( 'wp_enqueue_script', 'jquery-ui-datepicker')
            ),
            'numeric_stepper' => array(
                array( 'wp_enqueue_script', 'jquery-numeric-stepper-script', plugins_url('/assets/libs/jquery-numeric-stepper/stepper/jquery.stepper.min.js', __FILE__), array(), false, true ),
                array( 'wp_enqueue_style', 'jquery-numeric-stepper-style', plugins_url('/assets/libs/jquery-numeric-stepper/stepper/jquery.stepper.min.css',__FILE__)),
            ),
            'switchbutton' => array(
                array( 'wp_enqueue_script', 'jquery-switchbutton-script', plugins_url('/assets/libs/jquery-switchbutton/jquery.switchbutton.min.js', __FILE__), array(), false, true ),
                array( 'wp_enqueue_style', 'jquery-switchbutton-style', plugins_url('/assets/libs/jquery-switchbutton/ui.switchbutton.min.css',__FILE__)),
            ),
            'select2' => array(
                array( 'wp_enqueue_script', 'jquery-select2-script', plugins_url('/assets/libs/select2/dist/js/select2.full.min.js', __FILE__), array(), false, true ),
                array( 'wp_enqueue_style', 'jquery-select2-style', plugins_url('/assets/libs/select2/dist/css/select2.min.css',__FILE__)),
            )
        ),
        'pages' => array(
            'tool_script' => array( 
                array( 'wp_enqueue_script', 'ope-tool-script', plugins_url('/assets/js/tool.js', __FILE__), array(), false, true  )  
            ),
            'setup_script' => array( 
                array( 'wp_enqueue_script', 'ope-setup-script', plugins_url('/assets/js/setup.js', __FILE__), array(), false, true  ) 
            ),
            'settings_script' => array( 
                array( 'wp_enqueue_script', 'ope-settings-script', plugins_url('/assets/js/settings.js', __FILE__), array(), false, true  ) 
            ),
            'analytics_script' => array( 
                array( 'wp_enqueue_script', 'ope-analytics-script', plugins_url('/assets/js/analytics.js', __FILE__), array(), false, true  ) 
            ),
            'customizer_script' => array( 
                array( 'wp_enqueue_style', 'ope-customizer-style', plugins_url('/assets/css/customizer.css',__FILE__) ), 
            ),
        )
    )
);