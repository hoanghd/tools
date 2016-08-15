<?php
/**
 * Database abstraction layer.
 * @package core
 * @see Object.class.php
 */
class Database
{
	/** Error messages.
     * @var array
     */
    var $aMessage = array('db.common'  	 => 'Database error',
						  'db.connect' 	 => 'db.connect %s, %s, %s',
						  'db.select_db' => 'db.select_db',
						  'db.sql'       => 'sql: "%s",  error: "%s"');
	
    /** SQL-requests history
     * @var array
     */
    var $aHistory = array();

    /** Database handler
     * @var    handler
     * @access private
     */
    var $_hLink    = 0;
	
	var $aErrors   = array();

   /** Default constructor.
    * Do not use it direct. Use static get() method instead.
    * @return Database
    * @see get()
    */
    function Database(){}

    /** Connects to database server and selects db.
     * @param string $sHost database host
     * @param string $sName database name
     * @param string $sUser user name
     * @param string $sPass user password
     * @return boolean
     * @access public
     */
    function connect($sHost, $sName, $sUser, $sPass)
    {
        $this->_hLink = mysql_connect($sHost, $sUser, $sPass);
        if (! $this->_hLink)		
            $this->_addError('db.connect', array($sHost, $sUser, $sPass));
        if (! mysql_select_db($sName, $this->_hLink))
            $this->_addError('db.select_db', array($sHost, $sName, $sUser, $sPass));
        return true;
    }

    /** Performs sql query with error reporting and logging.
     * @param  string $sSql query string
     * @return int query result handle
     * @access public
     */
    function query($sSql)
    {
        if (Main::getParam('debug') || Main::getRequest()->get("debug"))//make timestap of query start
        {
            list($nUsec, $nSec) = explode(' ', microtime());
            $nStart =  ((float)$nUsec + (float)$nSec);
        }
        
        $hRes = mysql_query($sSql, $this->_hLink);

        if (Main::getParam('debug') || Main::getRequest()->get("debug"))//make timestapm of query end
        {
            list($nUsec, $nSec) = explode(' ', microtime());
            $nEnd =  ((float)$nUsec + (float)$nSec);
            $iRow = is_bool($hRes) ? '-' : mysql_num_rows($hRes);
            $this->aHistory[] = array('sql'=>$sSql, 'time'=>($nEnd-$nStart), 'rows'=>$iRow);			
        }

        if (!$hRes)
            $this->_addError('db.sql', array($sSql, mysql_error($this->_hLink)));
        
        return $hRes;
    }

    /** Returns row id from last executed query
     * @return int id of last INSERT operation
     */
    function getLastID()
    {
        return mysql_insert_id($this->_hLink);
    }

    /** Escapes string for LIKE expression ('%' => '\%', '_' => '\_')
     * @param mixed $mParam string or array of string need to be escaped
     * @return mixed escaped string or array of escaped strings
     */
    function escapeLike($sParam)
    {
        return str_replace(array('%','_'), array('\%','\_'), Database::escape($sParam));
    }

    /** Prepares string to store in db (performs  addslashes() )
     * @param mixed $mParam string or array of string need to be escaped
     * @return mixed escaped string or array of escaped strings
     * @see unescape()
     */
    function escape($mParam)
    {
        if (is_array($mParam))
            return array_map(array('Database', 'escape'), $mParam);

        if (get_magic_quotes_gpc())
            $mParam = stripslashes($mParam);

        $mParam = mysql_real_escape_string($mParam);
        
        return $mParam;
    }

    /** Unescapes string stored in db (performs striptslashes())
     * @param mixed   $mParam  string or array of string need to be unescaped
     * @return mixed unescaped string or array of unescaped strings
     */
    function unescape($mParam)
    {
        if (is_array($mParam))
            return array_map(array('Database', 'unescape'), $mParam);

        $mParam = stripslashes($mParam, ENT_QUOTES);
        return str_replace(array('&#092;' ,'&#095;' ,'&#037;'), array('\\', '_', '%'), $mParam);
    }


    /** Gets data returned by sql query
     * @param string $sSql    select query
     * @param string $bAssoc  type of returned rows array
     * @return array selected rows (each row is array of specified type) or emprt array on error
     */
    function getRows($sSql, $bAssoc = true, $sKeyField='')
    {
        $aRows = array();
        $bAssoc = ($bAssoc ? MYSQL_ASSOC : MYSQL_NUM);

        $hRes = $this->query($sSql);
        while($aRow = mysql_fetch_array($hRes, $bAssoc)) {
            if ($sKeyField && isset($aRow[$sKeyField]))
                $aRows[$aRow[$sKeyField]] = $aRow; //add with given key
            else
                $aRows[] = $aRow; //add with default keys
        }
        return $aRows; //empty array on error
    }

    /** Returns exactly one row as array. If there is number of rows
     * satisfying the condition then the first one will be returned.
     * @param string $sSql   select query
     * @return array exact one row (first if multiply row selected):
     *                 or false on error
     * @see getRows()
     */
    function getRow($sSql, $bAssoc = true)
    {
        $hRes = $this->query($sSql);
        $aRes = mysql_fetch_array($hRes, ($bAssoc ? MYSQL_ASSOC : MYSQL_NUM));
        return $aRes?$aRes:array();
    }

    /** Makes hash array from two columns 'col1'=>'col2'
     * @param string $sSql sql query
     * @return array hash array with keys from first column
     *               and values from second
     */
    function getHash($sSql)
    {
        $aRows = $this->getRows($sSql, false);
        $aRes = array();
        foreach($aRows as $sVal)
            $aRes[$sVal[0]] = $sVal[1];
        return $aRes; //empty array on error
    }

    /** Returns one field from a row
     * @param string $sSql SQL query
     * @return mixed field value
     */
    function getField($sSql)
    {
        $sRes = '';
        $aRow = $this->getRow($sSql, false);
        if ($aRow)
            $sRes = $aRow[0];
        return $sRes;
    }

    /** Returns number of rows returned by a given SQL query
     * Use this  function only if query 'SELECT COUNT(*) ...' is very complicated
     * @param string $sSql sql query
     * @return int rows count
     */
    function countRows($sSql)
    {
        $hRes = $this->query($sSql);
        return mysql_num_rows($hRes);
    }

    /** Performs insert of one row. Accepts values to insert as an array:
     *    'column1' => 'value1'
     *    'column2' => 'value2'
     *    ...
     * @param string  $sTable    table name
     * @param array   $aValues   column and values to insert
     * @param boolean $bEscape true - method escapes values (with "), false - not escapes
     * @return int last ID (or 0 on error)
     */
    function insert($sTable, $aValues, $bEscape=true)
    {
        $sCols = '`'.implode('`, `',array_keys($aValues)).'`';
        if ($bEscape)
        {
            $aValues = $this->escape($aValues);
            $sVals = '"'.implode('","',array_values($aValues)).'"';
        }
        else
            $sVals = implode(',',array_values($aValues));

        $sSql = 'INSERT INTO `'.$sTable.'` '.
                '        ('.$sCols.')'.
                ' VALUES ('.$sVals.')';
		
        if ($this->query($sSql))
            return $this->getLastID();

        return 0;
    }

    /** Performs update of rows.
     * @param string $sTable  table name
     * @param array  $aValues array of column=>new_value
     * @param string $sCond   condition (without WHERE)
     * @param boolean $bEscape true - method escapes values (with "), false - not escapes
     * @return boolean true - update successfule, false - error
     */
    function update($sTable, $aValues, $sCond, $bEscape=true)
    {   
        if (!is_array($aValues))
            return false;

        $sSets = '';
        foreach ($aValues as $sCol=>$sValue)
        {
            if ($bEscape)
                $sSets .= '`'.$sCol.'` = "'.$this->escape($sValue).'", ';
            else
                $sSets .= '`'.$sCol.'` = '.$sValue.', ';
        }
        $sSets[strlen($sSets)-2]='  '; //replace trailing ','
        $sSql = 'UPDATE `'.$sTable.'` SET '.$sSets.' WHERE '.$sCond;
        return $this->query($sSql);
    }

    /** Format date as string for MySQL
     * @param int $timestamp datetime as timestamp (current time if omitted)
     * @return string fomratted datetime
     */
    function date($timestamp = 0) {
        $timestamp = $timestamp?$timestamp:time();
        return date('Y-m-d H:i:s', $timestamp);
    }


    /** Fetch all rows from query result.
     * @param handle  $hRes      result handle
     * @param boolean $bAssoc    true - returns associative array, false - numbered
     * @param string  $sKeyField field for keys in array or natural order if none given
     * @see fetchRow()
     */
    function fetchRows($hRes, $bAssoc=true, $sKeyField = '')
    {
        $aRows = array();
        $bAssoc = ($bAssoc ? MYSQL_ASSOC : MYSQL_NUM);
        while($aRow = mysql_fetch_array($hRes, $bAssoc)) {
            if ($sKeyField && isset($aRow[$sKeyField]))
                $aRows[$aRow[$sKeyField]] = $aRow; //added by key
            else
                $aRows[] = $aRow; //added by selected order
        }
        return $aRows;
    }

    /** Get data from selected table (create query text, perform query
     *  and returns values as array of rows).
     * @param string $sTable    table name
     * @param string $sCond     condition without WHERE (no condtion by default)
     * @param mixed  $mCols     columns ('*' by default - all)
     * @param int    $iLimRows  limit rows (0 by default - no limit)
     * @param int    $iLimOff   limit offset (0 by default - from first row)
     * @param string $sType     type of returned rows array
     * @param string $sKeyField field for keys or natural order if none
     * @return array selected rows (each row is array of specified type) or emprt array on error
     * @see fetchRow()
     * @see fetchRows()
     * @see selectRows()
     */
    function selectRows($sTable, $mCols = '*', $sCond='', $iLimRows=0, $iLimOff=0, $sKeyField = '', $bAssoc = true)
    {
        if (is_array($mCols))
            $mCols = $this->_mergeCols($mCols);

        $sSQL = 'SELECT '.$mCols.' FROM '.$sTable;
        if ($sCond)
            $sSQL .= ' WHERE '.$sCond;
        if ($sKeyField)
            $sSQL .= ' ORDER BY '.$sKeyField;
        if ($iLimRows > 0 ) {
            $sSQL .= '   LIMIT '.intval($iLimOff).','.intval($iLimRows);
        }

        $hRows = $this->query($sSQL);
        if ($hRows)
                return $this->fetchRows($hRows, $bAssoc, $sKeyField);

        return array(); //emty array on error
    }

    /** Returns exact one row as array. If there is number of rows
     * satisfying condition then first one will be returned.
     * @param string $sTable table name
     * @param mixed  $mCols  string or array of columns
     * @param string $sCond  condition (without WHERE)
     * @return array exact one row (col1=>val1, col2=>val2, ...) or false on error
     * @see getrows()
     */
    function selectRow($sTable, $mCols = '*', $sCond='') {
        return current($this->selectRows($sTable, $mCols, $sCond, 1));
    }

    /** Get one field from one row
     * @param string $sTable table name
     * @param string $sCol   condition (without WHERE)
     * @param string $sCond  colum name (only one colum!!!)
     * @return mixed field value or false on error
     */
    function selectField($sTable, $sCol, $sCond) {
        if ($aRow = $this->selectRow($sTable, $sCol, $sCond))
            return $aRow[$sCol];
        else
            return false; //no rows selected on given condition
    } 
	
	/** Returns all errors generated by object
     * @return array errors
     */
    function getErrors()
    {
        return $this->aErrors;
    }

    /** Adds an error to object error pool.
     * @param string  $sKey   key of error message
     * @param mixed   $aParm  paramteres of error message
     * @param boolean $bFatal true - fatal error, false (deafult) - recoverable error
     * @return boolean false
     */
    function _addError($sKey, $mParam=array())
    {
        if ($mParam && !is_array($mParam))
		{
            $mParam = array($mParam);
		}
        $sErr = vsprintf($this->aMessage[$sKey], $mParam);
		print_r($mParam);
		//$this->addLog( $sErr);
		$this->aErrors[] = substr($sErr, 0, 1024);        			
    }
	
	function addLog($sContent)
	{	
		$sFile = Main::getParam('csv_dir')."/".date('Y')."_".date('m')."_".date('d').".txt";
		
		if (!$handle = fopen($sFile, 'a')) 
		{
			echo "Cannot open file ($sFile)<BR>";			
		}
		else
		{	
		   if (fwrite($handle, "{$sContent}\n\n") === FALSE) 
		   {
			   echo "Cannot write to file ($sFile)<BR>";			 
		   }
		   else
		   {		
				//echo "Success, ($sFile) <BR>";		
			}
		   fclose($handle);
		}	
	} 
	
	/**
     * Get offset for given page (fix page number if needed)
     * 
     * @param int $iPage      page number
     * @param int $iPageSize  page size (rows per page)
     * @param int $iCnt       records number
     * @return int offset of LIMIT in SQL
     */
    function getOffset($iPage, $iPageSize, $iCnt)
    {
        if ($iPageSize) //if get page -- fix current page and get offset
        {
            $iPages  = ceil($iCnt / $iPageSize);
            $iPage   = max(1, min($iPages, $iPage));
            return $iPageSize*($iPage-1);
        }

        return 0;
    }
	
	function close()
	{
		mysql_close($this->_hLink); 
	}
	
    /** Create temporary table
     * @param array $aFields fields (ex. array('field'=>'fname', 'type'=>'VARCHAR(255)', 'attribute'=>'NOT NULL'))
     * @param array $aAttr   table attributes
     * @return string temporary table name
     */
    function createTempTable($aFields, $aAttr='')
    {
        $sTmpName = "`".md5(uniqid(rand(),1))."`";
        $sSql = 'CREATE TEMPORARY TABLE IF NOT EXISTS '.$sTmpName.' (';
        $sSep = '';
        for ($i=0; $i<count($aFields); $i++)
        {
            $sSql .= $sSep.$aFields[$i]["field"]." ".$aFields[$i]["type"]." ".(isset($aFields[$i]["attribute"])?$aFields[$i]["attribute"]:'');
            $sSep = ', ';
        }
        if ( $aAttr ) $sSql .= $sSep.$aAttr;
        $sSql .= ');';
        $this->query($sSql);
        return $sTmpName;
    }

}
?>
