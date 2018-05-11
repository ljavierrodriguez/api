## Unit Tests

https://docs.google.com/spreadsheets/d/1KFqhL3vDbJvPSDsmt6Up-83Xj1IJEamNpcAR7zh7UNY/edit#gid=0

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
    $badge = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body)->expectSuccess();
}
```

The **mockAPICall** method is available to mock an API call.

You can specify if you expect the call to be a success or to return an error by doing:

```php
//for success
$badge = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body)->expectSuccess();

//for failure
$badge = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body)->expectFailure();
```

And it will asserts for a 200 response code or a 500 if failure.

You will have to take care of the rest of the assertions, for example here we can check if the badge contains 
name equal to what we sent in the body of the request.
```php
$this->assertTrue($badge->name == $body["name"]); 
```