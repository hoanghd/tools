#### /app/Config/routes.php
```php
  Router::parseExtensions('json');
```

#### /app/Controller/AppController.php
```php
  public $components = array('RequestHandler');
```

#### Controller/*.*
```php
  $this->set("_serialize", array('page'));
```

#### Test
curl -H "Accept: application/json" http://example.com/recipies
```javascript
$.ajax({
    type: 'POST',
    url: '/test/',
    data: {"name":"jonas"},
    success: function(data){},
    contentType: "application/json",
    dataType: 'json'
});
```
