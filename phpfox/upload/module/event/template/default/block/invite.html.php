<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: invite.html.php 2546 2011-04-18 19:51:26Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<ul class="block_listing">
{foreach from=$aEventInvites item=aEventInvite}
	<li>
		<div class="block_listing_image">
			{img server_id=$aEventInvite.server_id title=$aEventInvite.title path='event.url_image' file=$aEventInvite.image_path suffix='_50' max_width='32' max_height='32'}
		</div>
		<div class="block_listing_title" style="padding-left:36px;">
			<a href="#">{$aEventInvite.title|clean}</a>
			<div class="extra_info">
				{$aEventInvite.start_time_phrase} at {$aEventInvite.start_time_phrase_stamp}	
				<div class="event_rsvp_invite_image" id="js_event_rsvp_invite_image_{$aEventInvite.event_id}">
					{img theme='ajax/add.gif'}
				</div>
				<ul class="event_rsvp_invite"><li>RSVP:</li><li><a href="#" onclick="$(this).parent().parent().hide(); $('#js_event_rsvp_invite_image_{$aEventInvite.event_id}').show(); $.ajaxCall('event.addRsvp', 'id={$aEventInvite.event_id}&amp;rsvp=1&amp;inline=1'); return false;">Yes</a></li><li><span>&middot;</span></li><li><a href="#" onclick="$(this).parent().parent().hide(); $('#js_event_rsvp_invite_image_{$aEventInvite.event_id}').show(); $.ajaxCall('event.addRsvp', 'id={$aEventInvite.event_id}&amp;rsvp=3&amp;inline=1'); return false;">No</a></li><li><span>&middot;</span></li><li><a href="#" onclick="$(this).parent().parent().hide(); $('#js_event_rsvp_invite_image_{$aEventInvite.event_id}').show(); $.ajaxCall('event.addRsvp', 'id={$aEventInvite.event_id}&amp;rsvp=2&amp;inline=1'); return false;">Maybe</a></li></ul>
			</div>
		</div>		
		<div class="clear"></div>
	</li>
{/foreach}
</ul>