<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: featured.html.php 3214 2011-09-30 12:05:14Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="t_center">
	<a href="{permalink module='photo' id=$aFeaturedImage.photo_id title=$aFeaturedImage.title}">{img server_id=$aFeaturedImage.server_id path='photo.url_photo' file=$aFeaturedImage.destination suffix='_240' max_width=240 max_height=240}</a>
</div>
{if Phpfox::getParam('photo.ajax_refresh_on_featured_photos')}
<script type="text/javascript">
<!--
	setTimeout("$.ajaxCall('photo.refreshFeaturedImage', '', 'GET');", {$iRefreshTime});
-->
</script>
{/if}