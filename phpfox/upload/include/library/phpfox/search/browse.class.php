<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Parent browse routine
 * Controls how we browse all the sectons on the site.
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: browse.class.php 2600 2011-05-11 19:54:09Z Raymond_Benc $
 */
final class Phpfox_Search_Browse
{
	/**
	 * Item count.
	 * 
	 * @var int
	 */
	private $_iCnt = 0;
	
	/**
	 * ARRAY of items
	 * 
	 * @var array
	 */
	private $_aRows = array();
	
	/**
	 * ARRAY of params we are going to work with.
	 * 
	 * @var array
	 */
	private $_aParams = array();
	
	/**
	 * Service object for the specific module we are working with
	 * 
	 * @var object
	 */
	private $_oBrowse = null;
	
	/**
	 * Short access to the "view" request.
	 * 
	 * @var string
	 */
	private $_sView = '';
	
	/**
	 * Class constructor.
	 *
	 */
	public function __construct()
	{		
	}
	
	/**	 
	 * Set the params for the browse routine.
	 * 	 
	 * @param array	$aParams ARRAY of params.
	 * @return object Return self.
	 */
	public function params($aParams)
	{
		$this->_aParams = $aParams;
		$this->_aParams['service'] = $aParams['module_id'] . '.browse';
		
		$this->_oBrowse = Phpfox::getService($this->_aParams['service']);
		
		$this->_sView = Phpfox::getLib('request')->get('view');
		
		if ($this->_sView == 'friend')
		{
			Phpfox::isUser(true);
		}
		
		return $this;
	}
	
	/**
	 * 
	 * Execute the browse routine. Runs the SQL query.
	 */
	public function execute()
	{
		$aActualConditions = (array) $this->search()->getConditions();
		
		$this->_aConditions = array();
		foreach ($aActualConditions as $sCond)
		{
			switch ($this->_sView)
			{
				case 'friend':
					$this->_aConditions[] = str_replace('%PRIVACY%', '0,1,2', $sCond);
					break;
				case 'my':
					$this->_aConditions[] = str_replace('%PRIVACY%', '0,1,2,3,4', $sCond);
					break;				
				default:
					$this->_aConditions[] = str_replace('%PRIVACY%', '0', $sCond);
					break;
			}
		}		

		if (Phpfox::getParam('core.section_privacy_item_browsing') && (isset($this->_aParams['hide_view']) && !in_array($this->_sView, $this->_aParams['hide_view'])))
		{			
			Phpfox::getService('privacy')->buildPrivacy(array_merge($this->_aParams, array('count' => true)));			
				
			$this->_iCnt = $this->database()->joinCount('total_item')->execute('getSlaveField');		
		}
		else 
		{
			$this->_oBrowse->getQueryJoins(true);
			
			$this->_iCnt = $this->database()->select((isset($this->_aParams['distinct']) ? 'COUNT(DISTINCT ' . $this->_aParams['field'] . ')' : 'COUNT(*)'))
				->from($this->_aParams['table'], $this->_aParams['alias'])
				->where($this->_aConditions)
				->execute('getSlaveField');
		}
		
		if ($this->_iCnt)
		{
			if (Phpfox::getParam('core.section_privacy_item_browsing') && (isset($this->_aParams['hide_view']) && !in_array($this->_sView, $this->_aParams['hide_view'])))
			{
				Phpfox::getService('privacy')->buildPrivacy($this->_aParams);
				
				$this->database()->unionFrom($this->_aParams['alias']);
			}
			else 
			{				
				$this->_oBrowse->getQueryJoins();
				
				$this->database()->from($this->_aParams['table'], $this->_aParams['alias'])->where($this->_aConditions);
			}		

			$this->_oBrowse->query();

			$this->_aRows = $this->database()->select($this->_aParams['alias'] . '.*, ' . (isset($this->_aParams['select']) ? $this->_aParams['select'] : '') . Phpfox::getUserField())
				->join(Phpfox::getT('user'), 'u', 'u.user_id = ' . $this->_aParams['alias'] . '.user_id')
				->order($this->search()->getSort())
				->limit($this->search()->getPage(), $this->search()->getDisplay(), $this->_iCnt)
				->execute('getSlaveRows');
				
			if (method_exists($this->_oBrowse, 'processRows'))
			{
				$this->_oBrowse->processRows($this->_aRows);
			}
		}			
	}
	
	/**
	 * Gets the count.
	 * 
	 * @return int Total items.
	 */
	public function getCount()
	{
		return (int) $this->_iCnt;
	}
	
	/**
	 * Get items
	 * 
	 * @return array ARRAY of items.
	 */
	public function getRows()
	{
		return (array) $this->_aRows;
	}
	
	/**
	 * Extends database class
	 * 
	 * @see Phpfox_Database
	 * @return object Returns database object
	 */
	public function database()
	{
		return Phpfox::getLib('database');
	}
	
	/**
	 * Extends search class
	 * 
	 * @see Phpfox_Search
	 * @return object Returns the search object
	 */
	public function search()
	{
		return Phpfox::getLib('search');
	}
	
	/**
	 * Reset the search
	 *
	 */
	public function reset()
	{
		$this->_aRows = array();
		$this->_iCnt = 0;
		$this->_aConditions = array();
		$this->_aParams = array();
		
		Phpfox::getLib('search')->reset();
	}
}

?>