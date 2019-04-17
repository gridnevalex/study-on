<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\CourseFixtures​;
use App\Tests\AbstractTest;
use App\Repository\CourseRepository;
use App\Entity\Course;

class CourseControllerTest extends AbstractTest
{
    public function getFixtures(): array
    {
        return [CourseFixtures​::class];
    }

    public function testShowIndex()
    {
        $client->request('GET', '/courses/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowNew()
    {
        $client->request('GET', '/courses/new');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
