<?php
$aFeed = Phpfox::getService('feed')->get(Phpfox::getUserId(), $this->get('feed_id'));

if (isset($aFeed[0]) && isset($aFeed[0]['feed_id']))
{
	if ($this->get('facebook_connection') == '1')
	{
		$this->call("FB.api('/me/feed', 'post', {link: '" . Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']) . "', message: '" . str_replace('\'', '\\\'', html_entity_decode($aFeed[0]['feed_status'], null, 'UTF-8')) . "'}, function(response){});");		
	}

	if ($this->get('twitter_connection') == '1')
	{		
		Phpfox::getLib('twitter')->post(html_entity_decode($aFeed[0]['feed_status'], null, 'UTF-8') . ' ' . Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']));
	}
}

?>