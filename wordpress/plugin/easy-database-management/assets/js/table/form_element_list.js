(function ($) {
    "use strict";	
	PageManager.Collections.FormElementList = Backbone.Collection.extend({
		 'model': PageManager.Models.FormElement
	});
}(window.jQuery));