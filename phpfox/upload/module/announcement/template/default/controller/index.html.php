<?php 
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox
 * @version 		$Id: index.html.php 3097 2011-09-14 14:57:37Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="announcements_holder">
	{if is_array($aAnnouncements) && empty($aAnnouncements)}
	<div class="extra_info">
		{phrase var='announcement.that_announcement_cannot_be_found'}
	</div>
	{elseif $aAnnouncements === false}
	<div class="extra_info">
		{phrase var='announcement.no_announcements_have_been_added'}
	</div>
	{else}
		{foreach from=$aAnnouncements item=aAnnouncement name=announcement}		
			<div class="js_announcement_{$aAnnouncement.announcement_id} {if is_int($phpfox.iteration.announcement/2)}row1{else}row2{/if}{if $phpfox.iteration.announcement == 1} row_first{/if}">
				<div class="js_announcement_{$aAnnouncement.announcement_id}_subject h3" style="font-size:12pt; font-weight:bold;">
					{if !empty($aAnnouncement.content_var) && !isset($aAnnouncement.is_specific)}
					<a href="{url link='announcement.view' id=$aAnnouncement.announcement_id}">{$aAnnouncement.subject_text}</a>
					{/if}
					<div class="announcement_{$aAnnouncement.announcement_id}_date extra_info" style="font-size: 9pt; font-weight:normal;">
						{$aAnnouncement.posted_on}
					</div>
				</div>
				<div class="js_announcement_{$aAnnouncement.announcement_id}_content" style="margin-top: 5px;">					
					{if !empty($aAnnouncement.intro_var) && (!isset($aAnnouncement.is_specific) || $aAnnouncement.is_specific == 0)}
					{$aAnnouncement.intro_text}
					<ul class="js_announcement_{$aAnnouncement.announcement_id}_more item_menu" style="margin-bottom: 15px;margin-top: 5px; float:right;">
						<li><a href="{url link='announcement.view' id=$aAnnouncement.announcement_id}">{phrase var='announcement.read_more'}</a></li>
					</ul>
					{else}
					<div class="announcement_text js_content_{$aAnnouncement.announcement_id}">
						{$aAnnouncement.content_text}
					</div>
					{/if}
				</div>
			</div>
			<div class="clear"></div>
		{/foreach}
	{/if}
</div>