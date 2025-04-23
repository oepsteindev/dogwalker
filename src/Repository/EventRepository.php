<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Find events in a specific date range
     */
    public function findByDateRange(\DateTime $startDate, \DateTime $endDate): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.startDateTime >= :startDate')
            ->andWhere('e.startDateTime <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('e.startDateTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find upcoming events
     */
    public function findUpcoming(int $limit = 10): array
    {
        $now = new \DateTime();
        
        return $this->createQueryBuilder('e')
            ->andWhere('e.startDateTime >= :now')
            ->setParameter('now', $now)
            ->orderBy('e.startDateTime', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find events for specific customer
     */
    public function findByCustomer($customer, bool $upcomingOnly = false): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.customer = :customer')
            ->setParameter('customer', $customer)
            ->orderBy('e.startDateTime', 'ASC');
            
        if ($upcomingOnly) {
            $now = new \DateTime();
            $qb->andWhere('e.startDateTime >= :now')
               ->setParameter('now', $now);
        }
        
        return $qb->getQuery()->getResult();
    }
}