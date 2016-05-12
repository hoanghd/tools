<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: logo.html.php 1298 2009-12-05 16:19:23Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.theme.style.logo' id=$aStyle.style_id}" enctype="multipart/form-data">
	<div class="table_header">
		{phrase var='theme.logo'}
	</div>
	<div class="table">
		<div class="table_left">
			{phrase var='theme.select_a_logo'}:
		</div>
		<div class="table_right">
			<input type="file" name="logo" />
			<div class="extra_info">
				{phrase var='theme.you_can_upload_a_jpg_gif_or_png_file'}
				{if $sCurrentStyleLogo}
				<br /><br />
				{phrase var='theme.recommended_width_height_width_x_height_pixels' width=$iWidth height=$iHeight}
				{/if}
			</div>	
		</div>
		<div class="clear"></div>
	</div>
	{if $sCurrentStyleLogo}
	<div class="table">
		<div class="table_left">
			{phrase var='theme.automatically_resize'}:
		</div>
		<div class="table_right">
			<select name="resize">
				<option value="0">{phrase var='theme.no'}</option>
				<option value="1">{phrase var='theme.yes'}</option>
			</select>
		</div>
		<div class="clear"></div>
	</div>		
	<div class="table">
		<div class="table_left">
			{phrase var='theme.current_logo'}:
		</div>
		<div class="table_right">
			<div class="p_4">
				<img src="{$sCurrentStyleLogo}" alt="" />
				{if $bIsNewLogo}
				<div class="extra_info">
					<a href="{url link='admincp.theme.style.logo' id=$aStyle.style_id revert='1'}" onclick="return confirm('{phrase var='theme.are_you_sure' phpfox_squote=true}');">{phrase var='theme.revert_logo'}</a>
				</div>
				{/if}
			</div>
		</div>
		<div class="clear"></div>
	</div>	
	{/if}
	<div class="table_clear">
		<input type="submit" value="{phrase var='theme.upload_logo'}" class="button" />
	</div>
</form>