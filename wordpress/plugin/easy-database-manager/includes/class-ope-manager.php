<?php
class OPE_Manager{
    public static $_mixed = array();
    
    public static function find($name, $data = NULL, $def = NULL ){
        $value = $data;
        
        foreach( preg_split( "/[\.]+/", $name ) as $key ) {
            if( is_array( $value ) && isset( $value[ $key ] ) ) {
                $value = $value[ $key ];
            }
            else $value = $def;
        }
        
        return $value;
    }
    
    /**
     * Call a callback with an array of parameters.
     *
     * @param array $item Callback parameters.
     */
    public static function register( $name, $args = NULL ) {
        if( preg_match( '/^(?P<name>[^\|]+)\|(?P<arg>[^\|]+)\|(?P<cond>.+)$/' , $name, $mt ) ){
            $name = $mt['name'];
            
            if( !empty( $args ) ){
                $value = self::find($mt['arg'], $args);
                if($value == NULL || !is_string($value) || !preg_match( $mt['cond'],  $value) ){
                    return;
                }
            }
            else return;
        }
        
        foreach( self::get_config( $name ) as $i=>$row ){
            if( $fn = array_shift( $row ) ) {
                self::$_mixed[$name . '.' . $i] = call_user_func_array( $fn, self::magic( $row ) );
            }
        }
    }
    
    /**
     * Replace params config
     */
    public static function magic( $param ) {
        if ( is_array( $param ) )
            return array_map( array( self, 'magic' ), $param );
        
        if( preg_match( '/^fn\((?P<class>[^\.]+)\.(?P<method>.+)\)$/' , $param ) ) {
            return array( 'OPE_Manager', $param );
        } else if( preg_match_all( '|\{(?P<name>[^\}]+)\}|U' , $param, $mt, PREG_SET_ORDER ) ){
            $data = array();
            foreach($mt as $row){
                $data[$row[0]] = isset(self::$_mixed[$row['name']]) ? self::$_mixed[$row['name']] : NULL;
            }
            return strtr($param, $data);
        }
        
        return $param;
    }
    
    /**
     * Retrieve value from the config file.
     *
     * @param string $name The key name of first level.
     * @param string $field optional The key name of second level.
     * @return mixed.
     */
    public static function get_config( $name ) {
        static $setting = false;
        if( false == $setting ) {
            if( file_exists( OPE_DIR . '/config.php' ) ) {
                $setting = require( OPE_DIR . '/config.php' );
            }
        }
        
        return self::find( $name, $setting, array() );
    }
    
    /**
     * Overwrite.
     */
    public static function __callStatic( $name, $args ) {
        if( preg_match( '/^fn\((?P<class>[^\.]+)\.(?P<method>.+)\)$/' , $name, $mt ) ) {
            if( ! empty( $mt[ 'class' ] ) && ! empty( $mt[ 'method' ] ) ) {
                if( $mt[ 'class' ] == '~' ) {
                    self::register($mt[ 'method' ], $args);
                }
                else if( self::import( $mt[ 'class' ] ) ) {
                    $obj = self::new_object( $mt[ 'class' ] );
                    return call_user_func_array( array( $obj, $mt[ 'method' ] ), $args );
                }
            }
        }
    }
    
    /**
     * Load classes
     *
     * @param string $class The class name to initialize.
     * @param string $path optional Load the class in modules directory.
     * @return bool Whether or not the given class has been defined.
     */
    public static function import($name){
        if( preg_match( '/^(?P<mod>\w+)\_(?P<class>\w+)$/', $name, $mt ) ) {
            $path  = OPE_MOD_DIR . '/' . $mt['mod'];
            $class = $mt['class'];
        } else {
            $path = OPE_INC_DIR;
            $class = $name;
        }
        
        if( !class_exists( 'OPE_' . $name ) ) {
            $path .=  '/class-ope-' . str_replace( '_', '-', $class ) . '.php';
            $path = strtolower($path);
            
            if( file_exists( $path ) ) {
                require_once( $path );
            }
        }
        
        return class_exists( 'OPE_' . $name );
    }
    
    /**
     * Creates a new class instance.
     *
     * @param string $class The class name.
     * @param array $attr optional attributes assigned to the object after initialization.
     * @return object.
     */
    public static function new_object( $class, $attr = array() ) {
        if ( self::import( $class ) ) {
            $class_name = 'OPE_' . $class;
            $obj = new $class_name();
            
            if( ! empty($attr) ) {
                foreach( $attr as $key => $val ) {
                    $obj->{$key} = $val;
                }
            }
            
            if(method_exists($obj, 'init')){
                $obj->init();
            }
            return $obj;
        }
        else exit( 'Can\'t not load class OPE_'. $class );
    }
}
