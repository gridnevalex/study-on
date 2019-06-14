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
        $courseName = ['MERN Stack Front To Back: Full Stack React, Redux & Node.js', 'Build a Blockchain and a Cryptocurrency from Scratch', 'Java Programming Masterclass for Software Developers'];
        $courseContent = ['Build and deploy a social network with Node.js, Express, React, Redux & MongoDB. Learn how to put it all together.', 'Build a blockchain and cryptocurrency. Discover the engineering ideas behind technologies like Bitcoin and Ethereum.', 'Learn Java Lang In This Course And Become a Computer Programmer.'];
        
        $lessonName = ['Basic Express Setup', 'User API Routes & JWT Authentication', 'Getting Started With React & The Frontend'];
        $lessonContent = 'Building an extensive backend API with Node.js & Express. Protecting routes/endpoints with JWT (JSON Web Tokens)';
        $lessonNumber = [1, 2, 3];

        for ($i = 0; $i < count($courseName); $i++) {
            $course = new Course();
            $course->setName($courseName[$i]);
            $course->setDescription($courseContent[$i]);
            $manager->persist($course);
        }

        $manager->flush();

        $courses = $manager->getRepository(Course::class)->findAll();

        for ($i = 0; $i < count($courses); $i++) {
            $course = $manager->getRepository(Course::class)->find($courses[$i]->getId());
            $lesson = new Lesson();
            $lesson->setCourse($course);
            $lesson->setName($lessonName[$i]);
            $lesson->setContent($lessonContent);
            $lesson->setSerialNumber($lessonNumber[$i]);
            $manager->persist($lesson);
        }

        $manager->flush();
    }
}
