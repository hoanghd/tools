(function ($) {
    "use strict";		
	PageManager.Models.Form = Backbone.Model.extend({
		defaults: {
			'id': 0,
			'name': '',
			'formList': new PageManager.Collections.FormList()
		},
		parse: function(response){
			var formList = new PageManager.Collections.FormList();
			formList.add(response.formList);
			response.formList = formList;
			return response;
		},
		validate: function (attrs) {
			var errors = {};
			if (!attrs.name) errors.name = "Hey! Give this thing a title.";
			if (!attrs.type) errors.type = "You gotta write a description, duh!";
			if (!_.isEmpty(errors)) return errors;
		}
	});
}(window.jQuery));