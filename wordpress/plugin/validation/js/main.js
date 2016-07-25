var Address = Backbone.DeepModel.extend({
  defaults: {
    "name": {first: "Baloo", last:"The Bear"},
    "email": "balu@junglebook.com",
    "street": "Middle of Nowhere 1",
    "zip": "12345",
    "city": "Amazon Delta",
    "country": "Brasil",
    "phone": "555-123123",
    "web": "https://www.youtube.com/watch?v=lz0J88gnINc",
	"gender": 'male',
	"checkbox": ["1"],
	"radio": "1"
  },
  validation: {
    name: {
      required: true
    },
    email: {
      pattern: 'email'
    }
  }

});

var NewAddress = Backbone.View.extend({
	el: $('#content'),
	template: _.template( jQuery("#Preview").html() ),
	template1: _.template( jQuery("#Preview1").html() ),
	bindings: {
		'#name': 'name.first',
		'#email': 'email',
		'#street': 'street',
		'#zip': 'zip',
		'#city': 'city',
		'#country': 'country',
		'#phone': 'phone',
		'#web': 'web',
		'#gender': 'gender',
		'.checkbox': 'checkbox',
		'.radio': 'radio'
	},
	initialize: function() {
		this.render();
		this.render1();
		this.listenTo( this.model, 'change:name.*', this.render1 );
		Backbone.Validation.bind(this);
	},
	render: function() {
		this.$el.html( this.template( {'data': this.model.toJSON() } ));
		this.stickit();
		return this;
	},
	render1: function() {
		$("#content1").html( this.template1( {'data': this.model.toJSON(), model: this.model } ));
		return this;
	}
});

var model = new Address();
model.bind('validated', function(isValid, model, errors) {
  console.dir(errors);
});
var view = new NewAddress( {
	'model': model
} );

