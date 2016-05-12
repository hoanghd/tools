<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Attachment
 * @version 		$Id: upload.html.php 3342 2011-10-21 12:59:32Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bIsAllowed}
<div class="js_upload_attachment_parent_holder">
	<script type="text/javascript">$Core.loadStaticFile('{jscript file='share.js' module='attachment'}');</script>
	{if $sCustomAttachment == 'photo'}
	<div class="global_attachment_sub_menu">
		<ul>
			<li><a href="#" class="active" onclick="$(this).parents('.global_attachment_sub_menu:first a').removeClass('active'); $(this).addClass('active'); $(this).parents('.js_upload_attachment_parent_holder:first .js_upload_form_holder_global_temp:first').hide(); $(this).parents('.js_upload_attachment_parent_holder:first .js_upload_form_holder_global:first').show(); return false;">{phrase var='attachment.upload_a_photo'}</a></li>
			<li><a href="#" onclick="$(this).parents('.global_attachment_sub_menu:first a').removeClass('active'); $(this).parents('.global_attachment_sub_menu:first .js_global_attachment_loader:first').show();  $(this).addClass('active'); $.ajaxCall('photo.getForAttachment', 'obj-id={$sAttachmentObjId}&amp;input={$sAttachmentInput}&amp;category={$sCategoryId}&amp;div-id=' + $(this).parents('.js_upload_attachment_parent_holder:first').find('.js_default_upload_form:first').attr('id') + '&amp;attachment-inline={if $bIsAttachmentInline}1{else}0{/if}', 'GET'); return false;">{phrase var='attachment.import_a_photo'}</a></li>
			<li class="js_global_attachment_loader" style="display:none;">{img theme='ajax/add.gif' class='v_middle'}</li>
		</ul>
		<div class="clear"></div>
	</div>	
	{/if}
		
	<div class="js_default_upload_form p_bottom_4" id="js_new_temp_form_0_{$sCategoryId}">
		<div class="js_upload_form_holder_global_temp">
	
		</div>
		<div class="js_upload_form_holder_global">
			<div class="js_upload_form_holder">
				<form method="post" enctype="multipart/form-data" action="{url link='current' frame='attachment-frame'}" target="js_upload_frame" class="js_upload_frame_form">
					<div><input type="hidden" name="category_name" value="{$sCategoryId}" class="category_name" /></div>
					<div><input type="hidden" name="input" value="{$sAttachmentInput}" /></div>
					<div><input type="hidden" name="attachment_obj_id" value="{$sAttachmentObjId}" /></div>
					<div><input type="hidden" name="upload_id" value="js_new_temp_form_0_{$sCategoryId}" class="js_temp_upload_id" /></div>
					{if $bIsAttachmentInline}
					<div><input type="hidden" name="attachment_inline" value="true" /></div>
					{/if}								
					{if $sCustomAttachment}
					<div><input type="hidden" name="custom_attachment" value="{$sCustomAttachment}" /></div>
					{/if}
					<input class="js_file_attachment" type="file" name="file[]" value="" onchange="$Core.uploadNewAttachment(this, {if $sCustomAttachment == 'video'}false{else}true{/if});" />
					<iframe name="js_upload_frame" height="500" width="500" frameborder="1" style="display:none;"></iframe>
					<div class="extra_info">
						<b>{phrase var='attachment.valid_file_extensions'}:</b>
						{if empty($sAttachmentInput) && empty($sCustomAttachment)}
						{foreach from=$aValidExtensions key=iKey item=sValidExtension}
						{if $iKey != 0},{/if} {$sValidExtension}
						{/foreach}
						{else}
						{if $sCustomAttachment == 'photo'}
						{phrase var='attachment.jpg_gif_or_png'}
						{elseif $sCustomAttachment == 'video'}
						{$sVideoFileExt}
						{else}
	 					{phrase var='attachment.jpg_gif_or_png'}
						{/if}
						{/if}
						{if $iMaxFileSize !== null}
						<br />
						{phrase var='attachment.the_file_size_limit_is_max_file_size' max_file_size=$iMaxFileSize}
						{/if}
					</div>
				</form>
			</div>
			<div class="js_upload_form_image_holder">
				<div class="js_upload_form_image_holder_image">{img theme='ajax/add.gif'}</div>
				<span></span>
			</div>
		</div>
	</div>
	<div class="js_add_new_form"></div>
</div>
{else}
{phrase var='attachment.you_have_reached_your_upload_limit'}
{/if}