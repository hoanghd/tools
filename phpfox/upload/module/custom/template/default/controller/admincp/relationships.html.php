<?php
defined('PHPFOX') or exit('No dice!');
?>

<form action="{url link='admincp.custom.relationships'}" method="post">
	<div class="table_header">
		Add Status
	</div>
	<div class="table">
		<div class="table_left">
			Status name:
		</div>
		<div class="table_right">
			{if isset($aEdit)}
				{module name='language.admincp.form' type='text' id='new' var_name=$aEdit.phrase.new}
			{else}
				{module name='language.admincp.form' type='text' id='new'}
			{/if}

			<div class="extra_info">
			You can add a language phrase if you enter it like this: <br />
			{l}phrase var='module.phrase_var'{r} <br />
			Otherwise the script will create the language phrase for you
			</div>
		</div>
		
	</div>
	<div class="table">
		<div class="table_left">
			Feed when confirmed:
		</div>
		<div class="table_right">
			{if isset($aEdit)}
				{module name='language.admincp.form' type='text' id='feed_with' var_name=$aEdit.phrase.feed_with}
			{else}
				{module name='language.admincp.form' type='text' id='feed_with'}
			{/if}
			<div class="extra_info">
				This is the message for the feed when the relationship has been confirmed.
				In some cases this feed is not needed and leaving this field blank will stop the feed from showing.
				For example "user1 is married to user2".
			</div>
		</div>
	</div>
	
	<div class="table">
		<div class="table_left">
			Feed before confirming:
		</div>
		<div class="table_right">
			{if isset($aEdit)}
				{module name='language.admincp.form' type='text' id='feed_new' var_name=$aEdit.phrase.feed_new}
			{else}
				{module name='language.admincp.form' type='text' id='feed_new'}
			{/if}
			<div class="extra_info">
				This message will be shown in the feed when a user has set a relationship status with another user but 
				the other user has not confirmed it. It also applies when a user sets the status without defining another user.
				For example "user1 is married".
			</div>
		</div>
	</div>
    
	<div class="table">
	    <div class="table_left">
		Requires Confirmation:
	    </div>
	    <div class="table_right">		
		    <input type="checkbox" name="val[confirmation]" {if isset($aEdit) && $aEdit.confirmation == 1}checked="checked" {/if}>
			   <div class="extra_info">
			       If this field is enabled this relationship status requires that both users agree
			       on displaying their relationship.
			   </div>
	    </div>
	</div>
	<div class="table">
	<div class="extra_info">
			For all these phrases the following transformations apply: 
			<br />{l}with_user_name{r} user name of the receiving party
			<br />{l}with_full_name{r} full name of the receiving party
			<br />{l}user_name{r} sender's user name
			<br />{l}full_name{r} sender's full name
			<br />{l}their{r} sender's possessive adjective (his, her)
		</div>
	</div>
	<div class="table_clear">
		
			<input type="submit" value="{if isset($aEdit)} Edit Status {else}Add Status{/if}" class="button">
		</div>
</form>

<form action="{url link='admincp.custom.relationships'}" method="post">
	<div class="table_header">
		Manage Relationship Statuses
	</div>
	<div class="table">		
		{if (isset($aStatuses) && is_array($aStatuses) && !empty($aStatuses))}
		<table>
			<tr>
				<th style="width:20px;"> &nbsp; </th>
				<th style="width:20px;"> &nbsp; </th>
				<th> Status name </th>
				<th> Feed when confirmed </th>
				<th> Feed when new </th>
				<th> Confirmation </th>
			</tr>
			{foreach from=$aStatuses name=status item=aStatus}
				<tr class="{if is_int($phpfox.iteration.status/2)}tr{else}{/if}" >
					<td> 
						<a href="{url link='admincp.custom.relationships' delete=$aStatus.relation_id}" onclick="return confirm('{phrase var='core.are_you_sure'}')">
							{img theme='misc/delete.png' style='vertical-align:middle;'}
						</a>
					</td>
					<td> 
						<a href="{url link='admincp.custom.relationships' edit=$aStatus.relation_id}">
							{img theme='misc/page_white_edit.png' style='vertical-align:middle;'}
						</a>
					</td>
					<td> {if isset($aStatus.phrase.new)} {module name='language.admincp.form' type='label' id=$aStatus.relation_id var_name=$aStatus.phrase.new} {/if} </td>
					<td> {if isset($aStatus.phrase.feed_with)} {module name='language.admincp.form' type='label' id=$aStatus.relation_id var_name=$aStatus.phrase.feed_with} {/if} </td>
					<td> {if isset($aStatus.phrase.feed_new)} {module name='language.admincp.form' type='label' id=$aStatus.relation_id var_name=$aStatus.phrase.feed_new}  {/if} </td>
					<td> <input type="checkbox" name="confirmation" {if $aStatus.confirmation == 1}checked="checked"{/if}> </td>
				</tr>
			{/foreach}
		</table>
		{else}
			No relationship statuses have been added.
		{/if}
				</div>
			<div class="table_clear"></div>
			</form>