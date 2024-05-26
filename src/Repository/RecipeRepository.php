<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Recipe::class);
    }


    public function  findTotalDuration():int
    {
        return $this->createQueryBuilder('r')
        ->select('SUM(r.duration) as total')
            ->getQuery()
            ->getSingleScalarResult();

    }



    public function paginationRecipes(int $page,?int $userId): PaginationInterface
    {

        $builder = $this->createQueryBuilder('r')->leftJoin('r.category', 'c')->select('r', 'c');


        if($userId){
            $builder = $builder->andWhere('r.user = :user')
                ->setParameter('user', $userId);
        }

        return $this->paginator->paginate(
            $builder,
            $page,
            6,
            [
                'distinct' => false,
                'sortFieldAllowList' => ['r.id', 'r.titre']
            ]
        );

//        return new Paginator($this->createQueryBuilder('r')
//        ->setFirstResult(($page - 1) * $limit)
//        ->setMaxResults($limit)
//            ->getQuery()
//            ->setHint(Paginator::HINT_ENABLE_DISTINCT, false),
//        false
//        );


    }

    public function searchRecipes(string $searchTerm, int $page, int $limit, ?int $userId)
    {
        $builder = $this->createQueryBuilder('r')
            ->leftJoin('r.category', 'c')
            ->select('r', 'c')
            ->where('r.titre LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%');

        if ($userId) {
            $builder->andWhere('r.user = :user')
                ->setParameter('user', $userId);
        }

        return $this->paginator->paginate(
            $builder,
            $page,
            $limit,
            [
                'distinct' => false,
                'sortFieldAllowList' => ['r.id', 'r.titre']
            ]
        );
    }

//    public function paginationRecipes(int $page, ?int $userId)
//    {
//        $builder = $this->createQueryBuilder('r')
//            ->leftJoin('r.category', 'c')
//            ->select('r', 'c');
//
//        if ($userId) {
//            $builder->andWhere('r.user = :user')
//                ->setParameter('user', $userId);
//        }
//
//        return $this->paginator->paginate(
//            $builder,
//            $page,
//            5,
//            [
//                'distinct' => false,
//                'sortFieldAllowList' => ['r.id', 'r.titre']
//            ]
//        );
//    }

//    pour genere la requeste c'est avec le getQuery, seParameter c'est pour dire c'est que  on doit mettre quand on a fait une reqeust prepareer
   //cette fonction va return un tableau de nos recette.
    public  function  findwithDurationLowerThan(int $duration){
        return $this-> createQueryBuilder('r')
            ->where('r.duration <= :duration')
            ->orderBy('r.duration', 'ASC')
            ->setMaxResults(1)
            ->setParameter('duration', $duration)
            ->getQuery()
            ->getResult();
    }



    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
