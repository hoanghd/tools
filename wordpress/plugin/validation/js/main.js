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
    "radio": "1",
    otherSpies: [
        { name: 'Lana' },
        { name: 'Cyrril' }
    ]
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
    events: {
        'click a[href]': 'redirect'
    },
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
        this.listenTo( this.model, 'change:name.*', this.render );
        Backbone.Validation.bind(this);
    },
    
    redirect: function(e){
        e.preventDefault();
        
        var self = this;
        _.com().redirect(e, function(){
            self.render();
        });
    },
    
    render: function() {
        var self = this;
        
        var params = {
            '$form': [
                ['textfield', [ 'username', 'Nguyen Van A', {'class':'form-field'} ]],
                ['textfield', [ 'fullname', 'Nguyen Van C', {'class':'form-field'} ]]
            ],
            '$listView': [{
                  rows: [
                    {id:1, name:'Monotonectally procras 1', test: {first:"Hoang1", last:"Hoa4"}},
                    {id:2, name:'Monotonectally procras 2', test: {first:"Hoang2", last:"Hoa5"}},
                    {id:3, name:'Monotonectally procras 3', test: {first:"Hoang3", last:"Hoa6"}},
                    {id:4, name:'Monotonectally procras 4', test: {first:"Hoang4", last:"Hoa7"}},
                    {id:5, name:'Monotonectally procras 5', test: {first:"Hoang5", last:"Hoa9"}}
                  ], 
                  total: 100,
                  limit: 10,
                  page: (_.com().query().page || 1)
              }, 
              [
                {name: 'id', header: 'ID'},
                {name: 'name', header: 'Name', populate:'test.first|date:"parameter1":"parameter2"'}
              ]
            ],
            '$partial': {
                'partial_01': {template: 'modules/demo/partial_01', params: {name: 'Test 01'}},
                'partial_02': {template: 'modules/demo/partial_02', params: {name: 'Test 02'}}
            },
            'data': self.model.toJSON()
        };
        
        _.com().render( 'modules/demo/main', params, function( content ){
            self.$el.html( content );
            self.stickit();
        });
        
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