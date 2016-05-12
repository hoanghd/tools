<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 2692 2011-06-27 19:13:17Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if Phpfox::getParam('video.allow_video_uploading')}
<div id="js_upload_video_file" class="page_section_menu_holder">
	{module name='video.file'}
</div>
{/if}
<div id="js_upload_video_url" class="page_section_menu_holder"{if Phpfox::getParam('video.allow_video_uploading')} style="display:none;"{/if}>
	{module name='video.url'}
</div>