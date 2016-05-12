<?php
	// {img server_id=$aVideo.image_server_id path='video.url_image' file=$aVideo.image_path suffix='_120' max_width=120 max_height=120 class='js_mp_fix_width' title=$aVideo.title}	
	$sImagePath = Phpfox::getLib('image.helper')->display(array(
			'server_id' => $aVideo['image_server_id'],
			'path' => 'video.url_image',
			'file' => $aVideo['image_path'],
			'suffix' => '_120',
			'max_width' => '120',
			'max_height' => '120',
			'return_url' => true
		)
	);
	
	if (!empty($sImagePath))
	{
		$this->template()->setMeta('og:image', $sImagePath);
	}
?>