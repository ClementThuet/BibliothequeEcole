<?php

namespace App\Repository;

use App\Entity\Pupil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * @method Pupil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pupil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pupil[]    findAll()
 * @method Pupil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PupilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pupil::class);
    }

    public function findByFieldValue($field,$value){
        
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entity\Pupil', 'pupil');
        $query = $entityManager->createNativeQuery('SELECT * FROM `pupil` WHERE `'.$field.'` LIKE "%'.$value.'%";', $rsm);
        return $query->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Pupil
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
