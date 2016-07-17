(function ($) {
    "use strict";	
	PageManager.Models.FormElement = Backbone.Model.extend({
		defaults: {
			'id': 0,
			'name': '',
			'type': '',
			'length': 0,
			'decimals': 0,
			'allowNull': true,
			'def': '',
			'comment': ''
		},
		
		validate: function (attrs) {
			var errors = {};
			if (!attrs.name) errors.name = "Hey! Give this thing a title.";
			if (!attrs.type) errors.type = "You gotta write a description, duh!";
			if (!_.isEmpty(errors)) return errors;
		}
	});
}(window.jQuery));