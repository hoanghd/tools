<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Service
 * @version 		$Id: group.class.php 1572 2010-05-06 12:37:24Z Raymond_Benc $
 */
class Custom_Service_Group_Group extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('custom_group');
	}	
	
	public function get()
	{
		return $this->database()->select('cg.*')
			->from($this->_sTable, 'cg')
			->join(Phpfox::getT('module'), 'm', 'm.module_id = cg.module_id AND m.is_active = 1')
			->order('cg.ordering ASC')
			->execute('getSlaveRows');		
	}
	
	public function getGroup($iId)
	{		
		return $this->database()->select('*')
			->from($this->_sTable)
			->where('group_id = ' . (int) $iId)
			->execute('getRow');		
	}
	
	public function getGroups($sType, $iUserGroup)
	{		
		$iGroup = 0;
		if (Phpfox::getUserGroupParam($iUserGroup, 'custom.has_special_custom_fields'))
		{
			$iGroup = $iUserGroup;
		}
		
		return $this->database()->select('*')
			->from($this->_sTable)
			->where('user_group_id = ' . (int) $iGroup . ' AND type_id = \'' . $this->database()->escape($sType) . '\' AND is_active = 1')
			->order('ordering ASC')
			->execute('getSlaveRows');		
	}
	
	public function getForEdit($iId)
	{
		$aGroup =  $this->database()->select('*')
			->from($this->_sTable)
			->where('group_id = ' . (int) $iId)
			->execute('getRow');
		
		list($sModule, $sVarName) = explode('.', $aGroup['phrase_var_name']);		
		
		$aPhrases = $this->database()->select('language_id, text')
			->from(Phpfox::getT('language_phrase'))
			->where('var_name = \'' . $this->database()->escape($sVarName) . '\'')
			->execute('getSlaveRows');		
			
		foreach ($aPhrases as $aPhrase)
		{
			$aGroup['group'][$aGroup['phrase_var_name']][$aPhrase['language_id']] = $aPhrase['text'];
		}
			
		return $aGroup;
	}
	
	public function getId($sVarName)
	{
		return $this->database()->select('group_id')
			->from($this->_sTable)
			->where('phrase_var_name = \'' . $this->database()->escape($sVarName) . '\'')
			->execute('getSlaveField');
	}
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('custom.service_group_group__call'))
		{
			return eval($sPlugin);
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>