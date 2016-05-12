<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: select.html.php 2696 2011-06-30 19:30:33Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="error_message" id="js_mail_move_error" style="display:none;"></div>
<form method="post" action="#" onsubmit="$Core.processForm('#js_mail_move_submit'); $('#js_global_multi_form_holder').ajaxCall('mail.move', 'folder=' + $('#js_mail_move_folder').val()); return false;">
	<select name="val[folder]" id="js_mail_move_folder">
		<option value="">Select:</option>
		<option value="0">Inbox</option>
		<option value="trash">Trash</option>
		<optgroup label="Custom Folders">			
		{foreach from=$aFolders item=aFolder}
			<option value="{$aFolder.folder_id}">{$aFolder.name|clean}</option>
		{/foreach}
		</optgroup>
	</select>
	<div class="p_top_4" id="js_mail_move_submit">
		<ul class="table_clear_button">
			<li><input type="submit" value="Submit" class="button" /></li>
			<li class="table_clear_ajax"></li>
		</ul>
		<div class="clear"></div>
	</div>
</form>