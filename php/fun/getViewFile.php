<?php
/**
 * Cần định nghĩa 
 * BASE_DIR
 * VIEW_DIR = BASE_DIR . DIRECTORY_SEPARATOR . "views"
 */

/**
 * Looks for the view file according to the given view name.
 *
 * @param string $view_name view name
 * @return string the view file path, false if the view file does not exist
 */
function getViewFile( $viewName = NULL ) {
    if( $viewName == NULL ) {
        $viewName = str_replace( '_', DIRECTORY_SEPARATOR, substr( get_called_class(), 0, -11 ) );
    } else if( is_file( $viewName ) && file_exists( $viewName ) ) {
        return $viewName;
    }
        
    return VIEW_DIR . DIRECTORY_SEPARATOR . $viewPath . ".php" );
}
?>
