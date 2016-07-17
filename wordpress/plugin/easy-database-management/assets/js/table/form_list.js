(function ($) {
    "use strict";		
	PageManager.Collections.FormList = Backbone.Collection.extend({
		 model: PageManager.Models.Form,
		 url: ajaxurl
	});	
}(window.jQuery));