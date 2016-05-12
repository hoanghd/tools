<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Blog
 * @version 		$Id: add.html.php 3342 2011-10-21 12:59:32Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if isset($aForms.blog_id)}
<div class="view_item_link">
	<a href="{permalink module='blog' id=$aForms.blog_id title=$aForms.title}">{phrase var='blog.view_blog'}</a>
</div>
{/if}

<script type="text/javascript">
{literal}
	function plugin_addFriendToSelectList()
	{
		$('#js_allow_list_input').show();
	}
{/literal}
</script>
<div class="main_break">
	{$sCreateJs}
	<form method="post" action="{url link='blog.add'}" id="core_js_blog_form" onsubmit="{$sGetJsForm}" enctype="multipart/form-data">
		<div id="js_custom_privacy_input_holder">
		{if $bIsEdit}
			{module name='privacy.build' privacy_item_id=$aForms.blog_id privacy_module_id='blog'}
		{/if}
		</div>
		<div><input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" /></div>
		<div><input type="hidden" name="val[selected_categories]" id="js_selected_categories" value="{value type='input' id='selected_categories'}" /></div>
		{if $bIsEdit && $aForms.post_status == 1}
		<div><input type="hidden" name="val[post_status]" value="1" /></div>
		{/if}
		{if $bIsEdit}
			<div><input type="hidden" name="id" value="{$aForms.blog_id}" /></div>
		{/if}
		{plugin call='blog.template_controller_add_hidden_form'}
		
		<div class="table">
			<div class="table_left">
				<label for="title">{required}{phrase var='blog.title'}:</label>
			</div>
			<div class="table_right">
				<input type="text" name="val[title]" value="{value type='input' id='title'}" id="title" size="40" />
			</div>			
		</div>
		
		{plugin call='blog.template_controller_add_textarea_start'}
		
		<div class="table">
			<div class="table_left">
				<label for="text">{required}{phrase var='blog.post'}:</label>
			</div>
			<div class="table_right">
				{editor id='text'}
			</div>			
		</div>
		
		<div class="table">
			<div class="table_left">
				{phrase var='blog.categories'}:
			</div>
			<div class="table_right">
			{if Phpfox::getUserParam('blog.blog_add_categories')}
				<div class="label_flow_menu">	
					<ul>
						<li class="label_flow_menu_active"><a href="#blog.displayCategories?sType=default{if $bIsEdit}&amp;blog_id={$aForms.blog_id}{/if}">{phrase var='blog.all'}</a></li>
						<li><a href="#blog.displayCategories?sType=public{if $bIsEdit}&amp;blog_id={$aForms.blog_id}{/if}">{phrase var='blog.public'}</a></li>
						<li id="js_user_personal_categories"><a href="#blog.displayCategories?sType=personal{if $bIsEdit}&amp;blog_id={$aForms.blog_id}{/if}">{phrase var='blog.personal'}</a></li>
						<li class="last"><a href="#blog.displayCategories?sType=used{if $bIsEdit}&amp;blog_id={$aForms.blog_id}{/if}">{phrase var='blog.most_used'}</a></li>
					</ul>
					<br class="clear" />
				</div>
			{/if}
				<div class="label_flow label_hover labelFlowContent" style="height:100px;" id="js_category_content">
				{if $bIsEdit}
					{module name='blog.add-category-list' user_id=$aForms.user_id}
				{else}
					{module name='blog.add-category-list'}
				{/if}
				</div>
				{if Phpfox::getUserParam('blog.blog_add_categories') && $bCanEditPersonalData}
				<div class="p_top_15">
					<script type="text/javascript">
					{literal}
					function addBlogCategory()
					{
						{/literal}
						if ($('#js_add_category').val() != '' && $('#js_add_category').val() != '{phrase var='blog.add_a_new_category' phpfox_squote=true}')
						{literal}
						{
							$('#js_add_category').ajaxCall('blog.addCategory');
						}
					}
					{/literal}
					</script>
					<input type="text" name="val[add]" value="{phrase var='blog.add_a_new_category'}" onclick="if(this.value=='{phrase var='blog.add_a_new_category' phpfox_squote=true}')this.value='';" onblur="if(this.value=='')this.value='{phrase var='blog.add_a_new_category' phpfox_squote=true}';" id="js_add_category" class="v_middle" /> <input type="button" value="{phrase var='blog.add'}" class="button v_middle button_off" onclick="addBlogCategory();"  />
					<span id="js_category_info"></span>
					<div class="extra_info">{phrase var='blog.multiple_categories_with_commas'}</div>
				</div>	
				{/if}
			</div>			
		</div>
		
		{if Phpfox::isModule('tag') && Phpfox::getUserParam('tag.can_add_tags_on_blogs')}{module name='tag.add' sType=blog}{/if}
		
		{if Phpfox::isModule('privacy') && Phpfox::getUserParam('privacy.can_set_allow_list_on_blogs')}
		<div class="table">
			<div class="table_left">
				{phrase var='blog.privacy'}:
			</div>
			<div class="table_right">	
				{module name='privacy.form' privacy_name='privacy' privacy_info='blog.control_who_can_see_this_blog' default_privacy='blog.default_privacy_setting'}
			</div>			
		</div>
		{/if}
		
		{if Phpfox::isModule('comment') && Phpfox::isModule('privacy') && Phpfox::getUserParam('blog.can_control_comments_on_blogs')}
		<div class="table">
			<div class="table_left">
				{phrase var='blog.comment_privacy'}:
			</div>
			<div class="table_right">	
				{module name='privacy.form' privacy_name='privacy_comment' privacy_info='blog.control_who_can_comment_on_this_blog' privacy_no_custom=true}
			</div>			
		</div>
		{/if}			
		
		{if Phpfox::isModule('captcha') && Phpfox::getUserParam('captcha.captcha_on_blog_add')}{module name='captcha.form' sType=blog}{/if}
		
		{plugin call='blog-template_controller_add_textarea_end'}
		
		<div class="table_clear">
			<ul class="table_clear_button">
				{plugin call='blog.template_controller_add_submit_buttons'}
				{if $bIsEdit && $aForms.post_status == 2}						
				<li><input type="submit" name="val[draft_update]" value="{phrase var='blog.update'}" class="button" /></li>
				<li><input type="submit" name="val[draft_publish]" value="{phrase var='blog.publish'}" class="button button_off" /></li>
				{else}
				<li><input type="submit" name="val[{if $bIsEdit}update{else}publish{/if}]" value="{if $bIsEdit}{phrase var='blog.update'}{else}{phrase var='blog.publish'}{/if}" class="button" /></li>
				{/if}			
				{if !$bIsEdit}<li><input type="submit" name="val[draft]" value="{phrase var='blog.save_as_draft'}" class="button button_off" /></li>{/if}
				<li><input type="button" name="val[preview]" value="{phrase var='blog.preview'}" class="button button_off" onclick="tb_show('{phrase var='blog.blog_preview' phpfox_squote=true}', $.ajaxBox('blog.preview', 'height=400&amp;width=600&amp;text=' + encodeURIComponent(Editor.getContent())));" /></li>
			</ul>
			<div class="clear"></div>
		</div>		
	
	</form>
	
	{if Phpfox::getParam('core.display_required')}
	<div class="table_clear">
		{required} {phrase var='core.required_fields'}
	</div>
	{/if}
</div>
