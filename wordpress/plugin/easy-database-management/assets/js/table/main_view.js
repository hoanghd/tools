(function ($) {
    "use strict";
	PageManager.Views.Main = Backbone.View.extend({
		el: PageManager.Elements.main,		
		events: {
		  "click .edm-btn-save" : 'save'
		},
		save: function(){}
	});
}(window.jQuery));