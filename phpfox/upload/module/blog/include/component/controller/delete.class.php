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
 * @package  		Module_Blog
 * @version 		$Id: delete.class.php 225 2009-02-13 13:24:59Z Raymond_Benc $
 */
class Blog_Component_Controller_Delete extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{	
		Phpfox::isUser(true);		
		
		if ($iId = $this->request()->getInt('id'))
		{
			Phpfox::getService('blog.process')->deleteInline($iId);
			$this->url()->send('blog', array('delete'));
		}
	}
}

?>