<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class SpecialtyTest extends BaseTestCase {

    protected $app;
    public function setUp()
    {
        parent::setUp();
        $this->app->addRoutes(['specialty']);
        $this->app->addRoutes(['profile']);
        $this->app->addRoutes(['badge']);

    }

    function testForCreateProfile(){
        $body = [
            "slug"=> "web-developer",
            "name"=> "Web Developer",
            "description"=> "Create websites using a CMS"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/profile/'], $body)
            ->expectSuccess()
            ->getParsedBody();

        return $profile->data;
    }
}
?>