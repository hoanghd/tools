//https://github.com/dperrymorrow/example-backbone-app/tree/master/js
var PageManager = {
	Models: {
		FormElement: function(){},
		DbElement: function(){},
		Params: function(){},
		Form: function(){}
	},
	Collections: {
		FormElementList: function(){},
		DbElementList: function(){},
		FormList: function(){}
	},
	Views: {
		FormElements: function(){},
		DbElements: function(){},
		Forms: function(){},
		Main: function(){}
	},
	Templates:{},
	Elements: {
		main: '#edm-container-main'	
	}
};

(function ($) {
    "use strict";		
	PageManager.Models.Params = Backbone.Model.extend({
		defaults: {
			'current': 0
		}
	});
}(window.jQuery));