<?php
return array(
    'core' => array(
        array( 'add_action', 'admin_menu', 'fn(~.admin.menu)' ),
        array( 'add_action', 'admin_enqueue_scripts', 'fn(~.admin.scripts_default|0|/\_edm\-/)' ),
        array( 'add_action', 'admin_enqueue_scripts', 'fn(~.admin.elements_default|0|/\_edm\-/)' ),
		array( 'add_action', 'wp_ajax_edm_get_forms', 'fn(Default_Table.forms)' ),
		array( 'add_action', 'wp_ajax_nopriv_edm_get_forms', 'fn(Default_Table.forms)' )
    ),
    'admin' => array(
        'menu' => array(
            'table'      => array( 'add_menu_page', esc_html__( 'Easy Database Management', EDM_LANG ), esc_html__( 'Easy Database Management', EDM_LANG ), 'manage_options', 'edm-easy-database-management', 'fn(Default_Table.index)', 'div' ),
            array( 'add_action', 'admin_print_scripts-{admin.menu.table}', 'fn(~.admin.pages.table_script)'),            
            array( 'add_action', 'admin_menu', 'fn(Setting.admin_menu_rename)', 9999 ),
        ),
        'scripts_default' => array(
            array( 'wp_enqueue_script', 'jQuery' ),
            array( 'wp_enqueue_script', 'backbone' ),
            array( 'wp_enqueue_script', 'underscore' ),
			array( 'wp_enqueue_style', 'bootstrap-style', plugins_url('/assets/vender/bootstrap/css/bootstrap.min.css',__FILE__)),
			array( 'wp_enqueue_style', 'font-awesome-style', plugins_url('/assets/vender/font-awesome/css/font-awesome.min.css',__FILE__)),
			array( 'wp_enqueue_style', 'font-ionicons-style', plugins_url('/assets/vender/ionicons/css/ionicons.min.css',__FILE__)),
			array( 'wp_enqueue_style', 'bootstrap.theme-style', plugins_url('/assets/vender/bootstrap/css/bootstrap-theme.min.css',__FILE__)),
			array( 'wp_enqueue_style', 'bootstrap-editable-style', plugins_url('/assets/vender/bootstrap-editable/bootstrap3-editable/css/bootstrap-editable.css',__FILE__)),
			array( 'wp_enqueue_style', 'edm-main-style', plugins_url('/assets/css/main.css',__FILE__)),
			array( 'wp_enqueue_script', 'bootstrap-script', plugins_url('/assets/vender/bootstrap/js/bootstrap.min.js', __FILE__), array(), false, true ),
			array( 'wp_enqueue_script', 'bootstrap-editable-script', plugins_url('/assets/vender/bootstrap-editable/bootstrap3-editable/js/bootstrap-editable.min.js', __FILE__), array(), false, true ),
        ),
        'elements_default' => array(
            
        ),
        'elements_scripts' => array(
            'media' => array(
                //array('wp_enqueue_media')
            ),
            'colorpicker' => array(
                //array( 'wp_enqueue_style', 'wp-color-picker' ),
                //array( 'wp_enqueue_script', 'wp-color-picker' )
            ),
            'datepicker' => array(
                
            ),
            'numeric_stepper' => array(
            ),
            'switchbutton' => array(
            ),
            'select2' => array(
               // array( 'wp_enqueue_script', 'jquery-select2-script', plugins_url('/assets/libs/select2/dist/js/select2.full.min.js', __FILE__), array(), false, true ),
                //array( 'wp_enqueue_style', 'jquery-select2-style', plugins_url('/assets/libs/select2/dist/css/select2.min.css',__FILE__)),
            )
        ),
        'pages' => array(
            'table_script' => array( 
                array( 'wp_enqueue_script', 'edm-table-*-script', '/assets/js/table/(params|form|form_element|form_element_list|form_element_view|form_list|form_view|main_view|init).js', array(), false, true  )  
            ),
        )
    )
);