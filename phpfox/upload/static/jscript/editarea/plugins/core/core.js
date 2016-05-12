

var EditArea_core = 
{		
	execCommand: function(cmd, param)
	{	
		switch (cmd)
		{		
			case 'file_switch_on':
				param = param.id;
			
				if (parent.$('#' + param).length <= 0)
				{
					return true;
				}
			
				eval('var oObject = ' + parent.document.getElementById(param).innerHTML + ';');
				
				if (parent.isset(oObject.style_id))
				{
					parent.$('#js_css_style_id').val(oObject.style_id);
					parent.$('#js_css_file').val(oObject.file);
					parent.$('#js_css_module').val(oObject.module);
				}
				else
				{
					parent.document.getElementById('js_template_type').value = oObject.type;
					parent.document.getElementById('js_template_module').value = oObject.module;
					parent.document.getElementById('js_template_theme').value = oObject.theme;
					parent.document.getElementById('js_template_name').value = oObject.name;			
				}
				
				parent.$('#js_template_product_id').val(oObject.product);
			
				parent.$('.js_theme_last_modified').hide();
				if (parent.$('#modify_' + param).length > 0)
				{
					parent.$('#modify_' + param).show();
					parent.$('#js_last_modified').show();
				}
				else
				{
					parent.$('#js_last_modified').hide();
				}
				
				if (oObject.custom == '1')
				{
					parent.$('#js_delete_custom').show();
				}
				else
				{
					parent.$('#js_delete_custom').hide();
				}
				
				break;
			case 'close_file':			
				parent.$('#' + param).remove();
				
				var iCnt = 0;
				parent.$('.js_append_theme_layer').each(function()
				{
					iCnt++;
				});
				
				if (iCnt === 0)
				{
					parent.$('#js_template_content_loader').show();
				}
				
				break;
			default:
			
				break;
		}		
		
		// parent.p(cmd);
		
		return true;
	}	
}

editArea.add_plugin("core", EditArea_core);