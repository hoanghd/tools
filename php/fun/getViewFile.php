<?php
/**
 * Looks for the view file according to the given view name.
 *
 * @param string $view_name view name
 * @return string the view file path, false if the view file does not exist
 */
public function get_view_file( $view_name ) {
    if( is_file( $view_name ) && file_exists( $view_name ) ) {
        return $view_name;
    }

    $folder = 'views';
    if( preg_match( '/^(?P<folder>\w+)\.(?P<view_name>\w+)$/', $view_name, $mt ) ) {
        $folder  .= '/' . $mt['folder'];
        $view_name = $mt['view_name'];
    }

    $class = new ReflectionClass( get_called_class() );

    if( ($folder == 'views') && preg_match( '/(?P<ctr>[^\-]+)\.php$/', basename( $class->getFileName() ), $mt ) ){
        $folder .= '/' . $mt['ctr'];
    }

    return realpath( dirname( $class->getFileName() ) . "/{$folder}/{$view_name}.php" );
}
?>
