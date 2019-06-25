<?php

namespace App\Repository;

use App\Entity\Lesson;
use App\Entity\Course;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Lesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lesson[]    findAll()
 * @method Lesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    public function getCombinedParentCourse($courseId, $billingClient, $user)
    {
        $entityManager = $this->getEntityManager();
        $course = $entityManager->getRepository(Course::class)->findOneBy(['id' => $courseId]);
        if (!$course) {
            throw new HttpException(404, 'Course not found');
        } else {
            return $entityManager->getRepository(Course::class)->findOneCombined($course->getSlug(), $billingClient, $user);
        }
    }
}
