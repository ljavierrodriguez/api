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

    function testForCreateSpecialty(){
        $body = [
            "profile_slug" => "web-developer",
            "name" => "RTF Master",
            "slug" => "rtf-master",
            "image_url" => "",
            "description" => "Loren ipsum orbat thinkin ir latbongen sidoment",
            "badges" => ["identator","identator2"],
            "points_to_achieve" => 40,
            "description" => "Create websites using a CMS"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/specialty/'], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body)
            ->getParsedBody();

        return $profile->data;
    }
}
?>