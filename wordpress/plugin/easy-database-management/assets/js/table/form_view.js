(function ($) {
    "use strict";
	PageManager.Views.Forms = Backbone.View.extend({
		template: PageManager.Templates.forms,
		el: PageManager.Elements.forms,
		events: {
		  'click .edm-btn-delete' : 'remove',
		  'click .edm-btn-add' : 'add',
		  'click .edm-data-list > li' : 'active'
		},
		initialize: function() {
			this.listenTo(PageManager.Data.formList, 'all', this.render);
			this.listenTo(PageManager.Data.params, 'change', this.render);
			
			PageManager.Data.formList.fetch({
				data: {action: 'edm_get_forms'}
			});
		},
		render: function(){
			return this.$('.edm-data-list').html(
			 	this.template({'forms': PageManager.Data.formList.toJSON(), 'params': PageManager.Data.params.toJSON()})
			);
		},
		remove: function(e){},
		add: function(e){},
		active: function(e){
			PageManager.Data.params.set({'current': parseInt($(e.target).parents('li').attr('data-id'))});
		}
	});
}(window.jQuery));