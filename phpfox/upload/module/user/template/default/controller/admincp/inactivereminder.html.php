<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_User
 * @version 		$Id: browse.html.php 2137 2010-11-15 13:37:06Z Raymond_Benc $
 * {* *}
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="table_header">
		{phrase var='user.member_search'}
</div>
<div class="table">
	<div class="table_left">
		Show users who have not logged in for:
	</div>
	<div class="table_right">
		<input type="text" id="inactive_days" size="3" value="1"> days
	</div>
	<div class="clear"></div>
</div>

<div class="table">
	<div class="table_left">
		Send mails in batches of
	</div>
	<div class="table_right">
		<input type="text" id="mails_per_batch" size="3" value="0">
		<div class="extra_info">
			Enter 0 for "all at once"
		</div>
	</div>
	<div class="clear"></div>
</div>

<div class="table">
	<div class="table_left"></div>
	<div class="table_right">
		<div class="extra_info">This feature uses the language phrase "user.mail_inactive_users" to send an email</div>
	</div>
	<div class="clear"></div>
</div>

<div class="table_clear">
	<input type="submit" value="Get Members Count" class="button" id="btnSearch" />
	<input type="submit" value="Process Mailing Job" class="button" id="btnProcess"/>
	<input type="submit" value="Stop Mailing Job" class="button" id="btnStop" />
</div>

<div id="progress"></div>