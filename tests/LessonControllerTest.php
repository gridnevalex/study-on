<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\AbstractTest;
use App\Entity\Lesson;

class LessonControllerTest extends AbstractTest
{
    public function getFixtures(): array
    {
        return [CourseFixtures​::class];
    }
    
    public function testShowIndex()
    {
        $client->request('GET', '/lessons/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowNew()
    {
        $client->request('GET', '/lessons/new');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
