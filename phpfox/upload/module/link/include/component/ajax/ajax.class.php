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
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 3335 2011-10-20 17:26:57Z Raymond_Benc $
 */
class Link_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function preview()
	{
		$this->error(false);
		
		Phpfox::getBlock('link.preview');
		
		if (!Phpfox_Error::isPassed())
		{
			echo json_encode(array('error' => implode('', Phpfox_Error::get())));
		}
		else 
		{
			$this->call('<script text/javascript">$Core.loadInit();</script>');
		}
	}
	
	public function addViaStatusUpdate()
	{
		Phpfox::isUser(true);
		
		define('PHPFOX_FORCE_IFRAME', true);
		
		$aVals = (array) $this->get('val');		
		
		$aCallback = null;
		if (isset($aVals['callback_module']) && Phpfox::hasCallback($aVals['callback_module'], 'addLink'))
		{
			$aCallback = Phpfox::callback($aVals['callback_module'] . '.addLink', $aVals);	
		}		
		
		if (($iId = Phpfox::getService('link.process')->add($aVals, false, $aCallback)))
		{
			(($sPlugin = Phpfox_Plugin::get('link.component_ajax_addviastatusupdate')) ? eval($sPlugin) : false);
			
			Phpfox::getService('feed')->callback($aCallback)->processAjax($iId);		
		}		
	}
	
	public function play()
	{
		$sEmbedCode = Phpfox::getService('link')->getEmbedCode($this->get('id'), ($this->get('popup') ? true : false));
		
		if ($this->get('popup'))
		{
			$this->setTitle(Phpfox::getPhrase('link.viewing_video'));
			echo '<div class="t_center">';
			echo $sEmbedCode;
			echo '</div>';
		}		
		elseif ($this->get('feed_id'))
		{
			$this->call('$(\'#js_item_feed_' . $this->get('feed_id') . '\').find(\'.activity_feed_content_link:first\').html(\'' . str_replace("'", "\\'", $sEmbedCode) . '\');');
		}
		else 
		{
			$this->html('#js_global_link_id_' . $this->get('id'), str_replace("'", "\\'", $sEmbedCode));
		}
	}
	
	public function attach()
	{
		Phpfox::isUser(true);
		
		$this->setTitle(Phpfox::getPhrase('link.attach_a_link'));
		
		Phpfox::getBlock('link.attach');		
	}
}

?>