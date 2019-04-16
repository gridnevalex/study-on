<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Course;
use App\Entity\Lesson;

class CourseFixtures​ extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $lesson_name = ['Basic Express Setup', 'User API Routes & JWT Authentication', 'Getting Started With React & The Frontend'];
        $lesson_content = 'Building an extensive backend API with Node.js & Express. Protecting routes/endpoints with JWT (JSON Web Tokens)';
        $number = [1, 2, 3];
        for ($i=0; $i < count($lesson_name); $i++) {
            $course = $manager->getRepository(Course::class)->find(15);
            $lesson = new Lesson();
            $lesson->setCourse($course);
            $lesson->setName($lesson_name[$i]);
            $lesson->setContent($lesson_content);
            $lesson->setSerialNumber($number[$i]);
            $manager->persist($lesson);
        }
        $manager->flush();
    }
}
