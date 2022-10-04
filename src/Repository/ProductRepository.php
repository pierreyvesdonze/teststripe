<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Product[] Returns an array of Product objects
    */
   public function findAllActivated(): array
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.isActiv = :val')
           ->setParameter('val', 1)
           ->orderBy('p.id', 'DESC')
           ->getQuery()
           ->getResult()
       ;
   }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findAllActivatedByCategory($id): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.categoryProduct = :categProd')
            ->andWhere('p.isActiv = :isActiv')
            ->setParameter('isActiv', 1)
            ->setParameter('categProd', $id)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findProductsByKeyword($keyword): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :keyword')
            ->andWhere('p.isActiv = :isActiv')
            ->setParameter('isActiv', 1)
            ->setParameter('keyword', '%'.$keyword.'%')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
