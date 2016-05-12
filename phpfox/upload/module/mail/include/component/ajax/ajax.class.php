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
 * @package  		Module_Mail
 * @version 		$Id: ajax.class.php 3335 2011-10-20 17:26:57Z Raymond_Benc $
 */
class Mail_Component_Ajax_Ajax extends Phpfox_Ajax
{	
	public function delete()
	{
		Phpfox::isUser(true);
		
		$sType = $this->get('type');		
				
		if (($sType == 'trash' ? Phpfox::getService('mail.process')->deleteTrash($this->get('id')) : Phpfox::getService('mail.process')->delete($this->get('id'), ($sType == 'sentbox' ? true : false))))
		{
			$this->slideUp('#js_message_' . $this->get('id'));
		}
	}
	
	public function newFolder()
	{
		Phpfox::isUser(true);
		$this->setTitle(Phpfox::getPhrase('mail.new_folder'));
		Phpfox::getBlock('mail.box.add');
	}
	
	public function move()
	{
		Phpfox::isUser(true);
				
		if (Phpfox::getService('mail.folder.process')->move($this->get('folder'), $this->get('item_moderate')))
		{
			$this->call('$Core.moderationLinkClear();');
			$this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('mail', array('view' => 'box', 'id' => $this->get('folder'))) . '\';');
		}
	}	
	
	public function addFolder()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('mail.can_add_folders', true);
		
		$sFolder = $this->get('add_folder');

		if (Phpfox::getService('mail.folder')->reachedLimit()) // Did they reach their limit?
		{
			$this->html('#js_mail_folder_add_error', Phpfox::getPhrase('mail.you_have_reached_your_limit'), '.show()');
			$this->call('$Core.processForm(\'#js_mail_folder_add_submit\', true);');
		}		
		elseif (Phpfox::getService('mail.folder')->isFolder($sFolder)) // Is there already a folder like this one?
		{
			$this->html('#js_mail_folder_add_error', Phpfox::getPhrase('mail.folder_already_use', array('phpfox_squote' => true)), '.show()');
			$this->call('$Core.processForm(\'#js_mail_folder_add_submit\', true);');			
		}
		elseif (Phpfox::getLib('parse.format')->isEmpty($sFolder))
		{
			$this->html('#js_mail_folder_add_error', Phpfox::getPhrase('mail.provide_a_name_for_your_folder', array('phpfox_squote' => true)), '.show()');
			$this->call('$Core.processForm(\'#js_mail_folder_add_submit\', true);');						
		}
		else // Everything is okay, lets add the folder
		{			
			if ($iId = Phpfox::getService('mail.folder.process')->add($sFolder))
			{
				$sNew = Phpfox::getLib('parse.output')->clean(Phpfox::getLib('parse.input')->clean($sFolder, 255));

				$this->call('js_box_remove($(\'#js_mail_folder_add_error\'));');
				$this->alert(Phpfox::getPhrase('mail.folder_successfully_created'), Phpfox::getPhrase('mail.create_new_folder'), 400, 150, true);
				$this->append('.sub_section_menu ul', '<li><a href="' . Phpfox::getLib('url')->makeUrl('mail', array('view' => 'box', 'id' => $iId)) . '">' . str_replace("'", "\\'", Phpfox::getLib('parse.input')->clean($sNew)) . '</a></li>');
			}
		}
	}
	
	public function editFolders()
	{
		Phpfox::getBlock('mail.box.edit');
		$this->call("$('#js_mail_box_folders').hide();");			
		$this->html('#js_edit_folders', $this->getContent(false), '.show()');	
		$this->call('$Core.loadInit();');
	}
	
	public function updateFolder()
	{
	    $aVal = $this->get('val');	    
	    $sFolder = reset($aVal['name']);
	    if (Phpfox::getLib('parse.format')->isEmpty($sFolder))
	    {
		
		
	    	$this->call("$('#js_process_form_image').hide(); alert('" . Phpfox::getPhrase('mail.provide_a_name_for_your_folder', array('phpfox_squote' => true)) . "'); $('#js_edit_input_folder_1 :input').focus();");
	    }
	    elseif (Phpfox::getService('mail.folder.process')->update($aVal))
		{
			Phpfox::getBlock('mail.folder', array(
				'bIsAjax' => true
			));			
			
			$this->call("$('#js_mail_box_folders').parent().html('" . $this->getContent() . "').show(); $('#js_block_bottom_link_1').html('" . Phpfox::getPhrase('mail.edit_folders', array('phpfox_squote' => true)) . "'); $Core.loadInit();");
		}
	}
	
	public function deleteFolder()
	{
		if (Phpfox::getService('mail.folder.process')->delete($this->get('id')))
		{
			Phpfox::addMessage('Folder successfully deleted.');
			$this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('mail') . '\'');
		}
	}
	
	public function compose()
	{
		Phpfox::isUser(true);
		
		$this->setTitle(Phpfox::getPhrase('mail.new_message'));
		
		Phpfox::getComponent('mail.compose', null, 'controller');		
		
		(($sPlugin = Phpfox_Plugin::get('mail.component_ajax_compose')) ? eval($sPlugin) : false);
		
		echo '<script type="text/javascript">$Core.loadInit();</script>';
	}
	
	public function composeProcess()
	{
		Phpfox::isUser(true);
		
		$this->errorSet('#js_ajax_compose_error_message');
		
		$oObject = Phpfox::getComponent('mail.compose', null, 'controller');
		
		if ($oObject->getReturn())
		{
			$this->call('$(\'#\' + tb_get_active()).find(\'.js_box_content:first\').html(\'<div class="message">Your message was successfully sent</div>\'); setTimeout(\'tb_remove();\', 2000);');
		}
	}

	/**
	 * Allows reading a message given its id
	 * @return void
	 */
	public function readMessage()
	{
		// security checks
		Phpfox::getUserParam('admincp.has_admin_access', true);
		Phpfox::getUserParam('mail.can_read_private_messages', true);
		
		$iId = $this->get('id');
		
		if (!is_numeric($iId))
		{
			return false;
		}
		Phpfox::getBlock('mail.message');
	}

	/**
	 * delicate function, deletes a message from the mail and mail_text table
	 */
	public function deleteMessage()
	{
		Phpfox::getUserParam('admincp.has_admin_access', true);
		Phpfox::getUserParam('mail.can_read_private_messages', true);

		$iId = $this->get('id');
		
		if (!is_numeric($iId))
		{
			return false;
		}
		Phpfox::getService('mail.process')->adminDelete($iId);
		$this->call('$("#js_mail_'.$iId.'").remove();');

	}
	
	public function getLatest()
	{
		if (!Phpfox::isUser())
		{
			$this->call('<script type="text/javascript">window.location.href = \'' . Phpfox::getLib('url')->makeUrl('user.login') . '\';</script>');
		}
		else
		{
			Phpfox::getBlock('mail.latest');
		}
	}
	
	public function toggleRead()
	{
		if (Phpfox::getService('mail.process')->toggleRead($this->get('id')))
		{
			
		}
	}
	
	public function moderation()
	{
		switch ($this->get('action'))
		{
			case 'delete':
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					if ($this->get('trash'))
					{
						Phpfox::getService('mail.process')->deleteTrash($iId);
					}
					else
					{
						Phpfox::getService('mail.process')->delete($iId, ($this->get('sent') ? true : false));
					}					
					//$this->slideUp('#js_message_' . $iId);
					$this->call('$("#js_message_'. $iId.'").slideUp("slow", function(){$(this).remove();});');
				}				
				$sMessage = 'Message(s) successfully deleted.';				
				break;
		}
		
		$this->alert($sMessage, 'Moderation', 300, 150, true);
		$this->hide('.moderation_process');		
	}
	
	public function listFolders()
	{
		$this->setTitle(Phpfox::getPhrase('mail.select_folder'));		
		
		Phpfox::getBlock('mail.box.select');
	}
}

?>