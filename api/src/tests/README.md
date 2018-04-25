## Running Tests

To run the testes just do:
```
$ bash api/src/tests/initialize.sh 
```
It will an empty database and run all the unit tests.

## Creating new tests

1. First step is to create a test class that extends from BaseTestCase:
```php
namespace Tests;

class TalentTreeATest extends BaseTestCase{

    //this setup method is mandatory, and you have to add the routes you can to test for
    public function setUp(){
        parent::setUp();
        $this->app->addRoutes(['badge']);
    }
}
```
2. Next start adding new methods into the class, like for example:

This code creates a sample fake call to the API:
```php
function testForCreateBadge(){
    $body = [
          "slug" => "identator",
          "name" => "Identatior for oscar",
          "points_to_achieve" => 100,
          "technologies" => "css, html",
          "description" => "wululu"
    ];
    $badge = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body);
}
```

The **mockAPICall** method is available to mock an API call, and it already asserts for a 200 response code,
you will have to take care of the rest of the assertions, for example here we can check if the badge contains 
name equal to what we sent in the body of the request.
```php
$this->assertTrue($badge->name == $body["name"]); 
```