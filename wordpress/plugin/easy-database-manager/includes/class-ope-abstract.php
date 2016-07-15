<?php
class OPE_Abstract{
    /**
     * Renders a view file.
     *
     * @param string $_view_file_ view file path
     * @param array $_data_ optional data to be extracted as local view variables
     * @param boolean $_return_ whether to return the rendering result instead of displaying it
     * @return mixed the rendering result if required. Null otherwise.
     */
    public function render( $_view_file_, $_data_ = null, $_return_ = false ) {
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { 
            return;
        }
        
        if( is_array ( $_data_ ) ) {
            extract( $_data_, EXTR_PREFIX_SAME, 'data' );
        } else {
            $data = $_data_;
        }
        
        if( $_return_ ) {
            ob_start();
            ob_implicit_flush( false );
            require( $this->get_view_file( $_view_file_ ) );
            return ob_get_clean();
        } else {
            require( $this->get_view_file( $_view_file_ ) );
        }
    }
    
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
}
?>