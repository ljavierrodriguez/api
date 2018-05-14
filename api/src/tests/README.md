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

### Assertin the response body:

Two methods have been created for that purpose:

#### withProperties
Checks that the response body contains specific properties
```php
$this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/student/2'])
                        ->expectSuccess()
                        ->withProperties(["name", "last_name"]);
```
#### withPropertiesAndValues
Checks that the response body contains specific proprtes but also checks for their values
```php
$this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/student/2'])
                        ->expectSuccess()
                        ->withPropertiesAndValues([
                                "name" => $body["name"]
                            ]);
```

### Other assertions

You can retrieve the response body and do your own assertions by calling **getParsedBody** like this:
```php
$response = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/student/2'])
                        ->expectSuccess()
                        ->getParsedBody();

//You can use any PHP unit assertion method like this:
$this->assertTrue($response->foo == $var); 
```