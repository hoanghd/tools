<?php
if (Phpfox::getParam('core.wysiwyg') == 'tiny_mce')
{	
		if (Phpfox::getParam('core.site_wide_ajax_browsing'))
		{
			$oTpl->setHeader(array(
					'wysiwyg/tiny_mce/tiny_mce.js' => 'static_script',
					'wysiwyg/tiny_mce/core.js' => 'static_script'
				)
			);			
		}
	
		if (Phpfox::getService('tinymce')->load())
		{			
				// ' . ($sControllFullName == 'profile.index' && Phpfox::getUserId() && Phpfox::getParam('feed.integrate_comments_into_feeds') && Phpfox::isModule('comment') ? 'customTinyMCE_init(\'feed_text\');' : '') . '
		
			$oTpl->setHeader(array(
					'wysiwyg/tiny_mce/tiny_mce.js' => 'static_script',
					'wysiwyg/tiny_mce/core.js' => 'static_script',
					Phpfox::getService('tinymce')->getJsCode()
				)
			);
		}
		else 
		{
			// $oTpl->setHeader('<script type="text/javascript">$Behavior.loadTinymceEditor = function() {}</script>');
		}
}
?>