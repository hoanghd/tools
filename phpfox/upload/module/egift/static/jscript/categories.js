/**
 * This function replaces the text in the phrases for an input field so the user can edit their value
 * it shows an edit button to confirm the edit.
 */

var aEditing = {};
function showEdit(iVar)
{
	$('.tr_td_'+iVar).each(function(){

		if ($(this).attr('id') in aEditing)
		{
			return;
		}
		aEditing[$(this).attr('id')] = $(this).attr('id');
		var aClasses = $(this).attr('class').split(' ');
		
		var sLangVar = '';
		var oVar;
		for (var i in aClasses)
		{
			if (aClasses[i].match(/phraseid_/))
			{
				sLangVar = aClasses[i].replace('phraseid_','');
				oVar = sLangVar.split('_');

				debug (oVar);
			}
		}
		
		$(this).html('<input type="text" value="' + $(this).html() + '" name="val[edit]['+oVar[0]+']['+oVar[1]+']">');
	});
	$('#edit_button').show();
}