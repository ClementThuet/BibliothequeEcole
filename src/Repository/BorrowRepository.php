<?php

namespace App\Repository;

use App\Entity\Borrow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
/**
 * @method Borrow|null find($id, $lockMode = null, $lockVersion = null)
 * @method Borrow|null findOneBy(array $criteria, array $orderBy = null)
 * @method Borrow[]    findAll()
 * @method Borrow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BorrowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Borrow::class);
    }

    public function findByNotReturn(){
        return $this->createQueryBuilder('borrow')
            ->andWhere('borrow.dateOfReturn is NULL')
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findByCurrentBorrow($idPupil){
        return $this->createQueryBuilder('borrow')
            ->andWhere('borrow.pupil = :idPupil')
            ->andWhere('borrow.dateOfReturn is NULL')
            ->setParameter('idPupil', $idPupil)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    public function findByBook($idBook){
        return $this->createQueryBuilder('borrow')
            ->andWhere('borrow.book = :idBook')
            ->setParameter('idBook', $idBook)
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findByPupil($idPupil){
        return $this->createQueryBuilder('borrow')
            ->andWhere('borrow.pupil = :idPupil')
            ->setParameter('idPupil', $idPupil)
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findByFieldValue($field,$value){
        
        $entityManager = $this->getEntityManager();
       
        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entity\Borrow', 'borrow');
        $rsm->addJoinedEntityFromClassMetadata('App\Entity\Book', 'book', 'borrow', 'book', array('id' => 'book_id'));
        $sql = "SELECT * FROM borrow INNER JOIN book ON borrow.book_id = book.id WHERE ".$field." LIKE '%".$value."%'";
        $query = $entityManager->createNativeQuery($sql, $rsm);
        return $query->getResult();
    }
}
