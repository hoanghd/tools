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
    
    redirect: function(e){
        e.preventDefault();
        
        var self = this;
        var href = $(e.currentTarget).attr("href");
        _.com().redirect(href, function(){
            self.render();    
        });
    },
    
    initialize: function() {
        this.render();
        this.render1();
        this.listenTo( this.model, 'change:name.*', this.render );
        //this.listenTo( this.model, 'all:otherSpies.*', this.render1 );
        this.listenTo( this.model, 'change:name.*', this.log );
        Backbone.Validation.bind(this);
    },
    
    render: function() {
        var self = this;
        
        var params = {
            form: [
                ['textfield', [ 'username', 'Nguyen Van A', {'class':'form-field'} ]],
                ['textfield', [ 'fullname', 'Nguyen Van C', {'class':'form-field'} ]]
            ],
            listView: [{
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
            'data': self.model.toJSON()
        };
        
        _.com().render( 'modules/demo/main', params, function( content ){
            self.$el.html( content );
            self.stickit();
        });
        
        return this;
    },
    
    render1: function() {
        var view = new ListView( {collection: this.model.get('otherSpies') } );
        
        $("#content1").html( view.render().el );
        console.log(this.model.get('otherSpies').toJSON());
        return this;
    },
    
    log: function(){
        console.dir(this.model.toJSON());
    }
});


var OtherSpiesModel = Backbone.Model.extend({
  defaults: {
    name: "Baloo"
  }
});

   
var OtherSpiesCollections = Backbone.Collection.extend({
     model: OtherSpiesModel
});

var ListView = Backbone.View.extend({
  tagName: 'ul',
  render: function() {
    var self = this;
    this.collection.each(function(item) {
      var itemView = new ItemView({model:item});
      self.$el.append(itemView.render().el);
    });
    
    return this;
  }
});

var ItemView = Backbone.View.extend({
  tagName: 'li',
  bindings: { '.name': 'name' },
  
  template: _.template( jQuery("#PreviewLi").html() ),
  initialize: function() {
        this.render();
        this.listenTo( this.model, 'change:name', this.log );
    },
  render: function() {
    this.$el.html( this.template({model:this.model}));
    this.stickit();
    return this;
  },
  log: function(){
      console.log(this.model.toJSON());
      
  }
});

var model = new Address();

var list = new OtherSpiesCollections();
list.set([
        { name: 'Lana' },
        { name: 'Cyrril' }
    ]);

model.set({otherSpies: list});

model.bind('validated', function(isValid, model, errors) {
  console.dir(errors);
});
var view = new NewAddress( {
    'model': model
} );

/*
model.set({
    'user.name.first': 'Lana',
    'user.name.last':  'Kang'
});

//Use get() with path names so you can create getters later
console.log(model.get('user.type'));    // 'Spy'

//You can use index notation to fetch from arrays
console.log(model.get('otherSpies.0.name')) //'Lana'
*/
