<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: profile.html.php 1241 2009-10-29 14:10:09Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if Phpfox::isModule('report')}
<div class="t_right p_4">
	{if $aUser.user_id != Phpfox::getUserId()}<a href="#?call=report.add&amp;height=220&amp;width=400&amp;type=user&amp;id={$aUser.user_id}" class="inlinePopup" title="{phrase var='report.report_this_user'}">{phrase var='report.report_this_user'}</a>{/if}
</div>
{/if}