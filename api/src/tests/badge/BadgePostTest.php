<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class BadgePostTest extends BaseTestCase {
 
    protected $app;
    public function setUp()
    {
        parent::setUp();
        $this->app->addRoutes(['badge']);

    }

    function testForCreateBadge(){
        $body = [
            "slug" => "identator",
            "name" => "Identatior for xxxxxxx",
            "points_to_achieve" => 100,
            "technologies" => "css, html",
            "description" => "wululu"
        ];
        $badge = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body)->expectSuccess();
    }

    function testCreateDoubleSlug(){
        $body = [
            "slug" => "identator",
            "name" => "Identatior for xxxxxxx",
            "points_to_achieve" => 100,
            "technologies" => "css, html",
            "description" => "wululu"
        ];
        $badge = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body)->expectFailure();
    }

    function testSlugCharacterSpecials(){
        $body = [
            "slug" => "12%ˆˆ&",
            "name" => "Identatior for xxxxxxx",
            "points_to_achieve" => 100,
            "technologies" => "css, html",
            "description" => "wululu"
        ];
        $badge = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body)->expectFailure();
    }

    function testEmptySlug(){
        $body = [
            "slug" => "",
            "name" => "Identatior for xxxxxxx",
            "points_to_achieve" => 100,
            "technologies" => "css, html",
            "description" => "wululu"
        ];
        $badge = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body)->expectFailure();
    }

    function testEmpty(){
        $body = [
            "slug" => "prueba",
            "name" => "",
            "points_to_achieve" => '',
            "technologies" => "",
            "description" => ""
        ];
        $badge = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body)->expectFailure();
    }

    function testPointLetters(){
        $body = [
            "slug" => "prueba2",
            "name" => "Identatior for xxxxxxx",
            "points_to_achieve" => 'hola',
            "technologies" => "css, html",
            "description" => "wululu"
        ];
        $badge = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body)->expectFailure();
    }

    function testTechnologiesNumbers(){
        $body = [
            "slug" => "prueba3",
            "name" => "Identatior for xxxxxxx",
            "points_to_achieve" => 100,
            "technologies" => 123,
            "description" => "wululu"
        ];
        $badge = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body)->expectSuccess();
    }

    function testDescriptionTechnologiesCharacterSpecials(){
        $body = [
            "slug" => "prueba4",
            "name" => "Identatior for xxxxxxx",
            "points_to_achieve" => 100,
            "technologies" => 123,
            "description" => "wululu!@#!$"
        ];
        $badge = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body)->expectSuccess();
    }

    function testCreateDoubleDescription(){
        $body = [
            "slug" => "prueba5",
            "name" => "Identatior for xxxxxxx",
            "points_to_achieve" => 100,
            "technologies" => 123,
            "description" => "wululu"
        ];
        $badge = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body)->expectSuccess();
    }
}