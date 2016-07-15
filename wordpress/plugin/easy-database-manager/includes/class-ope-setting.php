<?php
class OPE_Setting{
    public function admin_menu_rename(){
        global $menu, $submenu;
        if( isset( $submenu['ope-super-social-share'][0][0] ) ) {
            $submenu['ope-super-social-share'][0][0] = esc_html__( 'Quick Setup', OPE_LANG );
        }
    }
    
    public function admin_element_scripts($hook){
		echo '-------------------------------' . $hook;
		//exit;
    }
}
