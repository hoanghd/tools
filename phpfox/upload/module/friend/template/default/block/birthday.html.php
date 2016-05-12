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
 * @package  		Module_<INSERT MODULE NAME HERE>
 * @version 		$Id: detail.class.php 254 2009-02-23 12:36:20Z Miguel_Espinoza $
 */

?>
{if !$bIsEventSection}
<div class="block_event_form">
	<form method="post" action="{url link='event.add'}">
		<div id="js_quick_event_default" style="display:none;">{phrase var='event.what_s_the_event'}</div>
		<div><input class="block_event_form_input block_event_form_input_off" type="text" name="val[title]" value="{phrase var='event.what_s_the_event'}" onfocus="if (this.value == $('#js_quick_event_default').html()) {l} $('.block_event_sub_holder').show(); this.value = ''; $(this).removeClass('block_event_form_input_off'); {r}" /></div>
		<div style="display:none;">{select_date prefix='end_' start_year='current_year' end_year='+1' field_separator=' / ' field_order='MDY' default_all=true add_time=true start_hour='+4' time_separator='event.time_separator'}</div>
		<div class="block_event_sub_holder">			
			<div class="block_event_sub">
				{select_date prefix='start_' start_year='current_year' end_year='+1' field_separator=' / ' field_order='MDY' default_all=true add_time=true start_hour='+1' time_separator='event.time_separator'}
			</div>

			<div class="block_event_sub">
				<div id="js_quick_event_default_where" style="display:none;">{phrase var='event.where'}</div>
				<input class="block_event_form_input block_event_form_input_off" type="text" name="val[location]" value="{phrase var='event.where'}" onfocus="if (this.value == $('#js_quick_event_default_where').html()) {l} this.value = ''; $(this).removeClass('block_event_form_input_off'); {r}" />
			</div>

			<div class="block_event_sub">
				<input type="submit" class="button" value="{phrase var='event.create_event'}" />
			</div>
		</div>		
	</form>
</div>
{/if}

{foreach from=$aUpcomingEvents key=sEventDate name=events item=aUpcomingEvent}
<div class="block_event_title_holder">
	<div class="block_event_title">{$sEventDate}</div>
	{foreach from=$aUpcomingEvent name=actualevents item=aEvent}
	<a href="{$aEvent.url}" class="link">{$aEvent.title|clean|split:30}</a> {if $phpfox.iteration.actualevents != count($aUpcomingEvent)}{if $phpfox.iteration.actualevents == (count($aUpcomingEvent) - 1)} <span class="detail_info">{phrase var='event.and'}</span> {else},{/if}{/if}
	{/foreach}
</div>
{/foreach}

{if isset($aBirthdays) && is_array($aBirthdays) && count($aBirthdays)}
{if !$bIsEventSection}
<div class="block_headline">{phrase var='friend.birthdays'}</div>
{/if}
{foreach from=$aBirthdays key=sDaysLeft item=aBirthDatas name=birthdays}
	<div class="block_event_title_holder">
		<div class="block_event_title">
			{if $sDaysLeft == 1}
				{phrase var='friend.tomorrow'}
			{elseif $sDaysLeft == 2}
				{phrase var='friend.after_tomorrow'}
			{elseif $sDaysLeft < 1}
				{phrase var='friend.today_normal'}
			{else}
				{phrase var='friend.days_left_days' days_left=$sDaysLeft}
			{/if}
		</div>
		{foreach from=$aBirthDatas item=aBirthday name=userbirthdays}
			{$aBirthday|user} {if $aBirthday.show_age}<span class="detail_info">({$aBirthday.new_age})</span>{/if}
			{if $phpfox.iteration.userbirthdays != count($aBirthDatas)}{if $phpfox.iteration.userbirthdays == (count($aBirthDatas) - 1)} <span class="detail_info">{phrase var='friend.and'}</span> {else},{/if}{/if}
		{/foreach}
	</div>
{/foreach}
{/if}

{if (empty($aBirthdays) || !isset($aBirthdays))}
{if $bIsEventSection}
<div class="extra_info">
	{phrase var='friend.no_birthdays_coming_up'}
</div>
{/if}
{/if}