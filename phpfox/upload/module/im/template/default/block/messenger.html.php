<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: messenger.html.php 2810 2011-08-05 11:33:28Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_footer_im_holder" class="im_holder js_footer_holder">
	<div class="im_header" id="js_main_chat_header">
		{phrase var='im.chat'}		
	</div>
	<div style="overflow:auto; height:200px;" id="js_im_friend_list">
		{module name='im.list'}
	</div>
</div>