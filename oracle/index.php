<?php
//="array('"&B3&"','"&C3&"','"&D3&"','"&E3&"'),"
$handle = opendir('./def');
while( false !== ( $entry = readdir( $handle ) ) ){
    if( $entry != "." && $entry != ".." ){
        $entry = "./def/{$entry}";
        file_put_contents(str_replace(array('/def', '.php'), array('/sql', '.sql'), $entry), fetch('./tmp.sql', require($entry)));
    }
}

function fetch( $_viewFile_, $_data_ = null ){
    if( is_array( $_data_ ) )
        extract( $_data_, EXTR_PREFIX_SAME, 'data' );
    else
        $data = $_data_;
        
    ob_start();
    ob_implicit_flush(false);
    require($_viewFile_);
    return ob_get_clean();
}
?>