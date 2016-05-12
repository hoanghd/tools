<?php
/** $Id: install.xml.php 2913 2011-08-29 08:14:28Z Raymond_Benc $ **/
defined('PHPFOX') or exit('NO DICE!');
?>
<phpfox>
	<install>
		$aRows = array(
			array(
				'name' => 'Default',
				'folder' => 'default',
				'created' => '1212226813',
				'is_active' => '1',
				'is_default' => '1',
				'total_column' => '3'
			),
			array(
				'name' => 'Cosmic',
				'folder' => 'cosmic',
				'created' => '1212226813',
				'is_active' => '1',
				'is_default' => '0',
				'total_column' => '3'
			)			
		);
		foreach ($aRows as $aRow)
		{
			$aInsert = array();
			foreach ($aRow as $sKey => $sValue)
			{
				$aInsert[$sKey] = $sValue;
			}
			$this->database()->insert(Phpfox::getT('theme'), $aInsert);
		}
		
		$aRows = array(
			array(
				'theme_id' => '1',
				'name' => 'Default',
				'folder' => 'default',
				'created' => '1212227060',
				'is_active' => '1',
				'is_default' => '1',
				'logo_image' => 'logo.png'
			),
			array(
				'theme_id' => '1',
				'name' => 'Facebookish',
				'folder' => 'facebookish',
				'created' => '1212227060',
				'is_active' => '1',
				'is_default' => '0',
				'logo_image' => 'logo.png'
			),
			array(
				'theme_id' => '1',
				'name' => 'Altitude',
				'folder' => 'altitude',
				'created' => '1212227060',
				'is_active' => '1',
				'is_default' => '0',
				'logo_image' => 'logo.png'
			),
			array(
				'theme_id' => '2',
				'name' => 'Cosmic',
				'folder' => 'cosmic',
				'created' => '1212227060',
				'is_active' => '1',
				'is_default' => '0',
				'logo_image' => 'logo.png'
			),
			array(
				'theme_id' => '1',
				'name' => 'Density',
				'folder' => 'density',
				'created' => '1212227060',
				'is_active' => '1',
				'is_default' => '0',
				'logo_image' => 'logo.png'
			)			
		);
		foreach ($aRows as $aRow)
		{
			$aInsert = array();
			foreach ($aRow as $sKey => $sValue)
			{
				$aInsert[$sKey] = $sValue;
			}
			$this->database()->insert(Phpfox::getT('theme_style'), $aInsert);
		}
	</install>
</phpfox>