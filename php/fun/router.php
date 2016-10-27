<?php
class Router {
    public static $routes = array();
    public static $slugs = array();

    /**
     * Traverse the routes and match the request
     */
    public static function traverse(){
        if ( !empty( self::$routes ) ) {
            $url = isset( $_SERVER[ 'REQUEST_URI' ] ) ? $_SERVER[ 'REQUEST_URI' ] : '/' ;
            $uri = parse_url( $url, PHP_URL_PATH );

            foreach( self::$routes as $route => $params ) {
                list( $layout, $valid ) = is_array( $params ) ? $params : array( $params, array() );

                if( self::compare( $route, $uri, $valid ) ) {
                    require_once( App::path( array( 'layout',  $layout . '.php' ) ) );
                    return;
                }
            }
        }

        require_once( App::path( array( 'layout',  '404.php' ) ) );
    }

    /**  
     * Match 2 uris
     * @param $uri_segments
     * @param $route_segments
     * @return bool
     */
    public static function compare( $route, $uri, $params = array() ){
        $uri_segments = preg_split('/[\/]+/', $uri, null, PREG_SPLIT_NO_EMPTY);
        $route_segments = preg_split('/[\/]+/', $route, null, PREG_SPLIT_NO_EMPTY);

        if ( count( $uri_segments ) != count( $route_segments ) ) return false;

        foreach ( $uri_segments as $segment_index => $segment ) {
            $segment_route = $route_segments[ $segment_index ];
            
            $is_slug = preg_match( '/^{[^\/]*}$/', $segment_route ) || preg_match( '/^:[^\/]*/', $segment_route, $matches );
            if ( $is_slug ) {//Note php does not support named parameters
                if ( strlen( trim( $segment ) ) === 0 ) {
                    return false;
                }

                $slug = str_ireplace( array(':', '{', '}'), '', $segment_route );

                if( isset( $params[ $slug ] ) && !preg_match( $params[ $slug ],  $segment ) ) {
                    return false;
                }

                self::$slugs[ $slug ] = $segment;//save slug key => value
            } else if( $segment_route !== $segment && $is_slug !== 1 ) {
                return false;
            }            
        }
        
        return true;
    }
}
?>
