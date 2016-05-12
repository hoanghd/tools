<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: view.html.php 1337 2009-12-17 18:58:27Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{phrase var='mail.private_message_from_timestamp' time_stamp=$aMessage.time_stamp|date:'mail.mail_time_stamp' owner=$aMessage|user:'owner_' viewer=$aMessage|user:'viewer_'}
<br />
<br />
{$aMessage.text|parse}
<div class="t_right">
	<ul class="item_menu">
		<li><a href="{url link='admincp.mail.private' delete=$aMessage.mail_id}" class="sJsConfirm">{phrase var='mail.delete'}</a></li>
	</ul>
	<div class="clear"></div>
</div>