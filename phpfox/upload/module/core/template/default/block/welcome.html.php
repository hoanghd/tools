<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Core
 * @version 		$Id: welcome.html.php 3170 2011-09-21 17:59:56Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="welcome">
	<div class="welcome_profile_right">
		<div id="theme"><a href="{url link='core.index-member.customize'}" class="no_ajax_link">{phrase var='core.customize_dashboard'}</a></div>
		<div id="time_stamp">
			<a href="{url link='user.setting'}" title="{phrase var='core.change_your_time_zone_preference'}">{$sCurrentTimeStamp}</a>
		</div>
	</div>	
	<div class="welcome_profile_image">
		{$sUserProfileImage}
	</div>
	<div class="welcome_profile_name">
		<div class="user_display_name"><a href="{$sUserProfileUrl}">{$sCurrentUserName}</a></div>
		<div class="welcome_quick_link">
			<ul>				
				<li><a href="#core.info">{phrase var='core.account_info'}</a><span>&middot;</span></li>
				<li><a href="#core.activity">{phrase var='core.activity_points'}<span id="js_global_total_activity_points">({$iTotalActivityPoints|number_format})</span></a><span>&middot;</span></li>
				<li><a href="{url link='user.profile'}">{phrase var='core.edit_profile'}</a><span>&middot;</span></li>
				<li><a href="{url link='profile'}">{phrase var='core.profile_views'}<span>({$iTotalProfileViews|number_format})</span></a></li>
			</ul>
			<div class="clear"></div>
		</div>		
	</div>
</div>