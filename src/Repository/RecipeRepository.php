<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

   /**
    * doctrine query to paginate recipes list
    */
   public function paginationQuery()
   {
       return $this->createQueryBuilder('r')
           ->orderBy('r.id', 'ASC')
           ->getQuery()
       ;
   }

    /**
    * doctrine query to find recipe by name
    */
    public function paginationfindByName($submitName)
    {
        return $this->createQueryBuilder('r')
            ->where('r.name LIKE :submitName')
            ->setParameter('submitName', '%'.$submitName.'%')
            ->getQuery()
        ;
    }
}
