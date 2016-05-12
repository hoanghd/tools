<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 2696 2011-06-30 19:30:33Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="error_message" id="js_mail_folder_add_error" style="display:none;"></div>
<form method="post" action="#" onsubmit="$Core.processForm('#js_mail_folder_add_submit'); $(this).ajaxCall('mail.addFolder'); return false;">
	<input type="text" name="add_folder" value="" size="40" /> 
	<div class="extra_info">
		Enter the name of your custom folder.
	</div>
	<div class="p_top_4" id="js_mail_folder_add_submit">
		<ul class="table_clear_button">
			<li><input type="submit" value="Submit" class="button" /></li>
			<li class="table_clear_ajax"></li>
		</ul>
		<div class="clear"></div>
	</div>
</form>
