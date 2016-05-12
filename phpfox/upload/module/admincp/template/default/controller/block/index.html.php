<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: index.html.php 2831 2011-08-12 19:44:19Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<a href="#" onclick="$.ajaxCall('core.designdnd', 'enable=1'); return false;" id="admincp_enabled_dnd">Enable DnD Mode</a>
<form method="post" action="{url link='admincp.user.group.add'}" onsubmit="$('#js_setting_saved').html($.ajaxProcess('Saving')).show(); $(this).ajaxCall('user.updateSettings'); return false;">	
	<div class="table_header">
		{phrase var='admincp.controllers'}<span id="js_editing_block" style="display:none;"> - <span id="js_editing_block_text"></span></span>
	</div>	
	<div id="content_editor_holder">
		<div id="content_editor_menu">
			<ul>
				{foreach from=$aBlocks key=sUrl item=aModules}
				<li><a href="#" onclick="$.ajaxCall('admincp.getBlocks', 'm_connection={$sUrl}', 'GET'); $(this).blur(); $('#content_editor_menu a').removeClass('cem_active'); $(this).addClass('cem_active'); return false;">{if empty($sUrl)}{phrase var='admincp.site_wide'}{else}{$sUrl}{/if}</a></li>
				{/foreach}
			</ul>
		</div>
		<div id="content_editor_text">
			<div id="js_setting_block">
				<div class="extra_info">
					To your left you will find all the available controllers that have blocks associated with them. Once you click on a controller it will list all the blocks and from there you can drag/drop
					to order the positioning of each block. You can also "Enable DnD Mode", which will allow you to visually browse the site and drag/drop blocks as well as add new blocks on the spot.
				</div>
			</div>			
		</div>
		<div class="clear"></div>
	</div>
</form>