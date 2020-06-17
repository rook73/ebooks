<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function search(?string $search, ?int $limit = null)
    {
        $qb = $this->createQueryBuilder('a');

        if ($search) {
            $qb
                ->andWhere('a.title LIKE :search')
                ->setParameter(':search', '%' . $search . '%');
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function booksByDates()
    {
        return $this->createQueryBuilder('a')
            ->select('a.createdAt AS date, COUNT(a.id) AS count')
            ->groupBy('a.createdAt')
            ->getQuery()
            ->getResult();
    }
}
