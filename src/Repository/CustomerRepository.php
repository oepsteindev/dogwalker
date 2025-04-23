<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 * 
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * Find customers by search term (in first name, last name, or email)
     */
    public function findBySearchTerm(string $term): array
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.firstName) LIKE LOWER(:term)')
            ->orWhere('LOWER(c.lastName) LIKE LOWER(:term)')
            ->orWhere('LOWER(c.email) LIKE LOWER(:term)')
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->orderBy('c.lastName', 'ASC')
            ->addOrderBy('c.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find newest customers
     */
    public function findNewest(int $limit = 10): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find customers with upcoming events
     */
    public function findWithUpcomingEvents(): array
    {
        $now = new \DateTime();
        $future = (new \DateTime())->modify('+30 days');

        return $this->createQueryBuilder('c')
            ->join('c.events', 'e')
            ->where('e.startDateTime >= :now')
            ->andWhere('e.startDateTime <= :future')
            ->setParameter('now', $now)
            ->setParameter('future', $future)
            ->groupBy('c.id')
            ->orderBy('e.startDateTime', 'ASC')
            ->getQuery()
            ->getResult();
    }
}