<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: preview.html.php 3008 2011-09-05 18:22:14Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div style="padding:10px;">
	<div id="js_preview_data"></div>
</div>
{literal}
<script type="text/javascript">
	$(function()
	{		
		$('#js_preview_data').html(window.opener.$('#html_code').val());
	});
</script>
{/literal}