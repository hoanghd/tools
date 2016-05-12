<?php
	if (Phpfox::getParam('facebook.enable_facebook_connect') && defined('PHPFOX_IS_FB_USER'))
	{
		$aInsert['status_id'] = 0;
		$aInsert['view_id'] = 0;
		$bSkipVerifyEmail = true;
	}
?>