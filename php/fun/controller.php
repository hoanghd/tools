<?php
class Controller {  
  public $layout = NULL;
  public $view = NULL;

  public function run(){}

  /**
	 * Renders a view with a layout.
	 *
	 * @param string $view name of the view to be rendered. See {@link getViewFile} for details
	 * about how the view script is resolved.
	 * @param array $data data to be extracted into PHP variables and made available to the view script
	 * @param boolean $return whether the rendering result should be returned instead of being displayed to end users.
	 * @return string the rendering result. Null if the rendering result is not required.
	 * @see renderPartial
	 * @see getLayoutFile
	 */
	public function render( $data = null, $return = false) {
		$output = App::renderFile( $data, $this->getViewFile(), true );
        if ( ( $layoutFile = $this->getLayoutFile() ) !== false ) {
            $output = App::renderFile( array( 'content' => $output ), $layoutFile, true );
        }            
        
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
	}  
  
  /**
   * Looks for the view file according to the given view name.
   *
   * @return string the view file path, false if the view file does not exist
   */
  public function getViewFile() {
    if( $this->view == NULL ) {
        $this->view = str_replace( '_', DIRECTORY_SEPARATOR, substr( get_called_class(), 0, -11 ) );
    } else if( is_file( $this->view ) && file_exists( $this->view ) ) {
        return $this->view;
    }

    return App::path( array( 'view', $this->view . '.php' ) );
  } 

  /**
   * Looks for the view file according to the given view name.
   *
   * @return string the view file path, false if the view file does not exist
   */
  public function getLayoutFile() {
    if( $this->layout == NULL ) {
        return false;
    }

    return App::path( array( 'view', $this->layout . '.php' ) );
  }
}
?>
