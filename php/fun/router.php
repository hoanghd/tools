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
                    require_once( App::path( array( 'layout',  $layout ) ) );
                    return;
                }
            }
        }

        require_once( App::path( array( 'layout',  '404' ) ) );
    }

    /**  
     * Match 2 uris
     * @param $uriSegments
     * @param $routeSegments
     * @return bool
     */
    public static function compare( $route, $uri, $params = array() ){
        $uriSegments = preg_split('/[\/]+/', $uri, null, PREG_SPLIT_NO_EMPTY);
        $routeSegments = preg_split('/[\/]+/', $route, null, PREG_SPLIT_NO_EMPTY);

        if ( count( $uriSegments ) != count( $routeSegments ) ) return false;

        foreach ( $uriSegments as $segmentIndex => $segment ) {
            $segmentRoute = $routeSegments[ $segmentIndex ];
            
            $isSlug = preg_match( '/^{[^\/]*}$/', $segmentRoute ) || preg_match( '/^:[^\/]*/', $segmentRoute, $matches );
            if ( $isSlug ) {//Note php does not support named parameters
                if ( strlen( trim( $segment ) ) === 0 ) {
                    return false;
                }

                $slug = str_ireplace( array(':', '{', '}'), '', $segmentRoute );

                if( isset( $params[ $slug ] ) && !preg_match( $params[ $slug ],  $segment ) ) {
                    return false;
                }

                self::$slugs[ $slug ] = $segment;//save slug key => value
            } else if( $segmentRoute !== $segment && $isSlug !== 1 ) {
                return false;
            }            
        }
        
        return true;
    }
}
?>
