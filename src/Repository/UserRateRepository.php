<?php

namespace App\Repository;

use App\Entity\UserRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserRate>
 *
 * @method UserRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRate[]    findAll()
 * @method UserRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRate::class);
    }

    public function add(UserRate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserRate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return UserRate[] Returns an array of UserRate objects
    */
   public function findAllByProduct($product): array
   {
       return $this->createQueryBuilder('u')
           ->andWhere('u.product = :val')
           ->setParameter('val', $product)
           ->orderBy('u.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }
}
