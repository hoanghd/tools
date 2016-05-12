<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Display the image details when viewing an image.
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Announcement
 * @version 		$Id: index.html.php 1238 2009-10-28 15:39:43Z Raymond_Benc $
 */
?>
<div id="announcement">
	{if $aAnnouncement.can_be_closed == 1 && Phpfox::getUserParam('announcement.can_close_announcement')}
		<div class="js_announcement_close">
			<a href="#" onclick="$.ajaxCall('announcement.hide', 'id={$aAnnouncement.announcement_id}'); return false;">
			{img theme='misc/delete_hover.gif' alt=''}
			</a>
		</div>
	{/if}	
	<div class="js_announcement_subject">	
		<div class="announcement_date">
			{$aAnnouncement.time_stamp|date}
		</div>
		{phrase var=$aAnnouncement.subject_var}
	</div>
	<div class="js_announcement_content">
		{if !empty($aAnnouncement.intro_var)}
			{$aAnnouncement.intro_var}
		{else}
			{phrase var=$aAnnouncement.content_var}
		{/if}
		{if !empty($aAnnouncement.content_var)}
		<div class="js_announcement_more">
			( <a href="{url link='announcement.view' id=$aAnnouncement.announcement_id}">{phrase var='announcement.read_more'}</a> )
		</div>
		{/if}		
	</div>
</div>