<?php
/**
 * Looks for the view file according to the given view name.
 *
 * @param string $view_name view name
 * @return string the view file path, false if the view file does not exist
 */
public function getViewFile( $viewName ) {
    if( is_file( $view_name ) && file_exists( $view_name ) ) {
        return $view_name;
    }

    $class = new ReflectionClass( get_called_class() );
    return realpath( dirname( $class->getFileName() ) . "/{$viewName}.php" );
}
?>
