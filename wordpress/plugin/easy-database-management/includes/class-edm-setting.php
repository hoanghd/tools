<?php
class EDM_Setting{
    public function admin_menu_rename(){
        global $menu, $submenu;
        if( isset( $submenu['edm-easy-database-management'][0][0] ) ) {
            $submenu['edm-easy-database-management'][0][0] = esc_html__( 'Manage Forms', EDM_LANG );
        }
    }
    
    public function admin_element_scripts($hook){
		echo '-------------------------------' . $hook;
		//exit;
    }
}
