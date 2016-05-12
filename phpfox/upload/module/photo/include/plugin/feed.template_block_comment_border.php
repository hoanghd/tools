<?php
if ((Phpfox::getLib('module')->getFullControllerName() == 'photo.view' || (PHPFOX_IS_AJAX && Phpfox::getLib('request')->get('theater') == 'true')) && Phpfox::isUser() && !Phpfox::isMobile())
{
	echo '<div class="feed_comment_extra"><a href="#" id="js_tag_photo">' . Phpfox::getPhrase('photo.tag_this_photo') . '</a></div>';
}
?>