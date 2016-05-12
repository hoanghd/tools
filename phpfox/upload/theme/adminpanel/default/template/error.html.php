{if $sPublicMessage}
<div class="public_message" id="public_message">
	{$sPublicMessage}
</div>
<script type="text/javascript">
	$('#public_message').show();
</script>
{/if}
<div id="core_js_messages">
{if count($aErrors)}
{foreach from=$aErrors item=sErrorMessage}
	<div class="error_message">{$sErrorMessage}</div>
{/foreach}
{unset var=$sErrorMessage var2=$sample}
{/if}
</div>