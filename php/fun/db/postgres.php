<?php
/**
 * Postgres Database 
 */
class DB_Postgres
{
	private $_hLink  = null;	
	private $_query  = array();
    private $_params = array();
		
	public function connect( $host, $user, $pass, $name, $port = false, $persistent = false ) {	
		$connection = "host='{$host}' dbname='{$name}' user='{$user}' password='{$pass}'";

		if ( $port ) {
			$connection .= " port='{$port}'";
		}
		
		$this->_hLink = ( $persistent ? @pg_pconnect( $connection ) : @pg_connect( $connection ) );

		// Unable to connect to master
		if ( !$this->_hLink ) {
			throw new Exception( 'Cannot connect to the database:' . $this->_sqlError() );
            return false;
		}	
		
		return true;
	}

    /**
     * Performs sql query with error reporting and logging.
     * 
     * @access public
     * @param  string $sql query string
     * @return int query result handle
     */
	public function query( $sql, $params = array() ) {
        if( !empty( $params ) ) {            
            $this->_params = $params;
            $sql = preg_replace_callback( '|\:(\w+)|', array( &$this, 'replace' ), $sql );
        }
        
        $this->_hQuery = @pg_query( $this->_hLink, $sql ); 	

        if ( !$this->_hQuery ) {
        	throw new Exception( 'Query Error: ' . $this->_sqlError() . ' <br /> Query: ' . $sql . '' );
        }
        
        return $this->_hQuery;
    }
	
    /** 
     * Returns one field from a row
     * 
     * @param string $sql SQL query
     * @return mixed field value
     */
    public function getField( $sql, $params = array() ) {
        $res = '';
        $row = $this->getRow( $sql, $params, false );
        if ( $row ) {
            $res = $row[0];
        }

        return $res;
    }

    /**
     * Returns exactly one row as array. If there is number of rows
     * satisfying the condition then the first one will be returned.
     * 
     * @access	public
     * @param string $sql   select query
     * @param string $assoc  type of returned rows array
     * @return array exact one row (first if multiply row selected): or false on error
     */
	public function getRow( $sql, $params = array(), $assoc = true ) {   	
    	// Run the query
        $res = $this->query( $sql, $params );

        // Get the array
       	$res =  pg_fetch_array($res, null, ($assoc ? PGSQL_ASSOC : PGSQL_NUM));

        return $res ? $res : array();
    } 
    
    /**
     * Gets data returned by sql query
     * 
     * @access	public
     * @param string $sql    select query
     * @param string $assoc  type of returned rows array
     * @return array selected rows (each row is array of specified type) or emprt array on error
     */
    public function getRows( $sql, $params = array(), $assoc = true ) {
        $rows = array();
        $assoc = ( $assoc ? PGSQL_ASSOC : PGSQL_NUM );

		// Run the query
        $this->rQuery = $this->query( $sql, $params );
		
        // Put it into a while look
        while( $row = pg_fetch_array( $this->rQuery, null, $assoc ) ) {        	
            $rows[] = $row;
        }

        return $rows; //empty array on error
    }
    
    /**
     * Performs insert of one row. Accepts values to insert as an array:
     *    'column1' => 'value1'
     *    'column2' => 'value2'
     * 
     * @access	public
     * @param string  $table    table name
     * @param array   $values   column and values to insert
     * @param boolean $escape true - method escapes values (with "), false - not escapes
     * @return int last ID (or 0 on error)
     */
    public function insert( $table, $values, $escape = true, $returnQuery = false ) {    	
    	$cols = '`' . implode( '`, `', array_keys( $values ) ) . '`';
        
        if ( $escape ) {
            $values = $this->escape( $values );
            $vals = "'" . implode( "', '", array_values( $values ) ) . "'";
        } else {
            $vals = implode( ',', array_values( $values ) );
        }

        $sql =  'INSERT INTO ' . $table . ' ' .
                '        (' . str_replace( '`', '', $cols ) . ')'.
                ' VALUES (' . $vals . ')';

        if ( $res = $this->query( $sql ) ) {
        	if ( $returnQuery ) {
        		return $sql;
        	}

            return $this->getLastId();
		}

        return 0;
    }
    
    /**
     * Performs update of rows.
     * 
     * @access public
     * @param string $table  table name
     * @param array  $values array of column=>new_value
     * @param string $cond   condition (without WHERE)
     * @param boolean $escape true - method escapes values (with "), false - not escapes
     * @return boolean true - update successfule, false - error
     */
    public function update( $table, $values, $cond, $params = array(), $escape = true ) {
        if ( !is_array( $values ) ) {
            return false;
		}

        $sets = '';
        foreach ( $values as $col => $val ) {
            if( is_null( $val ) ) {
               $sets .= '' . $col . ' = NULL, ';
            } else if ($escape) {
                $sets .= "" . $col . " = '" . $this->escape( $val ) . "', ";
            } else {
                $sets .= '' . $col . ' = ' . $val . ', ';
            }
        }

        $sets[ strlen( $sets ) - 2 ]='  ';

        if( !empty( $params ) ) {            
            $this->_params = $params;
            $cond = preg_replace_callback( '|\:(\w+)|', array( &$this, 'replace' ), $cond );
        }

        $sql = 'UPDATE ' . $table . ' SET ' . $sets . ' WHERE ' . $cond;
        
        return $this->query( $sql );
    }  
    
    /**
     * Delete entry from the database
     * 
     * @access public
     * @param string $table is the table name
     * @param string $query is the query we will run
     */
    public function delete( $table, $query, $params = array() ) {
    	$this->query( "DELETE FROM ". $table ." WHERE ". $query, $params );
    }

	public function getVersion() {
		return @pg_parameter_status( $this->_hLink, 'server_version' );
	}
	
	public function getServerInfo() {
		return 'PostgreSQL ' . $this->getVersion();
	}

    /**
     * For callback replace
     */
    private function replace( $matches ){
        $value = NULL;        
        if( isset( $this->_params[ $matches[1] ] ) ) {
            $value = $this->_params[ $matches[1] ];
        }
        
        if( !is_null( $value ) ) {
            return ( is_int( $value ) ? $value : "'" . $this->escape( $value ) . "'" );
        } else {
            return 'NULL';
        }        
    }
    
    /**
     * Prepares string to store in db (performs  addslashes() )
     * 
     * @access	public
     * @param mixed $param string or array of string need to be escaped
     * @return mixed escaped string or array of escaped strings
     */
    public function escape( $param ) {
        if ( is_array( $param ) ) {
            return array_map( array( &$this, 'escape' ), $param );
		}

        if ( get_magic_quotes_gpc() ) {
            $param = stripslashes( $param );
        }

        $param = pg_escape_string( $this->_hLink, $param );

        return $param;
    }
    
    /**
     * Returns row id from last executed query
     * 
     * @access	public
     * @return int id of last INSERT operation
     */
    public function getLastId() {        	
    	return true;
    }

    public function freeResult() {
		if ( is_resource( $this->rQuery ) ) {
			@pg_free_result( $this->rQuery );
		}
	}
	
	public function affectedRows() {
		return @pg_affected_rows( $this->rQuery );
	}
	
	private function _sqlError()
	{
		return pg_errormessage();
	}  	
}
?>
