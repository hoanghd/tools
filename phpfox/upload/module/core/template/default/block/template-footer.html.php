<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: template-footer.html.php 3244 2011-10-07 11:42:15Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
		{if !defined('PHPFOX_SKIP_IM')}
		{module name='im.footer'}
		{$sDebugInfo}
		{/if}
		<script type="text/javascript">
			$Core.init();
		</script>
		{plugin call='theme_template_body__end'}