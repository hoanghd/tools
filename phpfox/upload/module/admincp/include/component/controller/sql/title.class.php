<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Component
 * @version 		$Id: title.class.php 1614 2010-06-01 10:01:18Z Raymond_Benc $
 */
class Admincp_Component_Controller_Sql_Title extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if ($this->request()->get('update'))
		{
			$aModules = Phpfox::massCallback('getSqlTitleField');
			$aParseTables = array();
			if (is_array($aModules) && count($aModules))
			{
				foreach ($aModules as $aModule)
				{
					if (isset($aModule['table']))
					{
						$aModule = array($aModule);	
					}
					
					foreach ($aModule as $aInfo)
					{
						$aParseTables[] = $aInfo;	
					}
				}
			}
			
			foreach ($aParseTables as $aParseTable)
			{
				if ($aParseTable['table'] == 'video')
				{
					$aVideoIndexes = Phpfox::getLib('database.support')->getIndexes(Phpfox::getT($aParseTable['table']));
					if (in_array('video_id', $aVideoIndexes))
					{				
						Phpfox::getLib('database')->query('ALTER TABLE ' . Phpfox::getT($aParseTable['table']) . ' DROP INDEX video_id');
					}
				}
				
				Phpfox::getLib('database')->query('ALTER TABLE ' . Phpfox::getT($aParseTable['table']) . ' CHANGE ' . $aParseTable['field'] . ' ' . $aParseTable['field'] . ' text');				
			}
			
			$this->url()->send('admincp.sql.title', null, Phpfox::getPhrase('admincp.database_tables_updated'));
		}
		
		$this->template()->setTitle(Phpfox::getPhrase('admincp.alter_title_fields'))
			->setBreadcrumb(Phpfox::getPhrase('admincp.alter_title_fields'))
			->assign(array(
					
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_sql_title_clean')) ? eval($sPlugin) : false);
	}
}

?>