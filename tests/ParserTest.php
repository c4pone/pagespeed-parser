<?php

use c4pone\PageSpeed\Parser;
use c4pone\PageSpeed\Screenshot;

class ParserTest extends PHPUnit_Framework_TestCase
{
    private $parser;
    public function __construct()
    {
        $this->parser = $this->createParser(); 
    }

    public function testGetTitle()
    {
        $this->assertEquals('I am Florian Kirchner', $this->parser->getTitle());
    }
    
    public function testGetPageStats()
    {
        $this->assertCount(11, $this->parser->getPageStats());
    }

    public function testGetScores()
    {
        $scores = $this->parser->getScores();
        $this->assertCount(2, $scores);
        $this->assertEquals('73', $scores['SPEED']);
        $this->assertEquals('99', $scores['USABILITY']);
    }

    public function testGetRecommendations()
    {
        $keys = array(
            "LINK",
            "URL",
            "LIFETIME",
            "RESPONSE_TIME",
            "NUM_SCRIPTS",
            "NUM_CSS",
            "SIZE_IN_BYTES",
            "PERCENTAGE",
            "HTML_TEXT",
            "NUM_TOO_CLOSE",
        );

        $recommendations = $this->parser->getRecommendations();
        
        foreach ($keys as $key) {
            $json_result = json_encode($recommendations);
            $this->assertTrue(strpos($json_result, $key) === false, sprintf('Key "%s" was not replaced', $key));
        }
    }

    public function testGetScreenshot()
    {
        $screenshot = $this->parser->getScreenshot();
        $this->assertNotNull($screenshot);
        $this->assertTrue($screenshot->hasData());

        $path = 'screenshot.png';

        //save the screenshot
        $screenshot->save($path);

        //check if file exists
        $this->assertTrue(file_exists($path));

        //cleanup
        unlink($path);
    }

    private function createParser($data = [])
    {
        if (count($data) === 0) {
            $data = file_get_contents(__DIR__ .'/_data/page_speed_result');
            $data = json_decode($data, true);
        }

        return new Parser($data);
    }
}

