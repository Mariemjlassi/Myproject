<?php

namespace App\Repository;
use App\Entity\Categories;
use App\Entity\Livres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livres>
 *
 * @method Livres|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livres|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livres[]    findAll()
 * @method Livres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livres::class);
    }

    /**
     * @return Livres[] Returns an array of Livres objects
     */
    public function findGreaterThan($prix): array
    {
       return $this->createQueryBuilder('l')
            ->andWhere('l.prix >:val')
            ->setParameter('val', $prix)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    public function search($term)
    {
        return $this->createQueryBuilder('l')
            ->where('l.titre LIKE :term')
            ->orWhere('l.categorie IN (SELECT c.id FROM categorie c WHERE c.libelle LIKE :term)')
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }

    public function findMostSoldBook()
{
    return $this->createQueryBuilder('l')
        ->select('l.titre, SUM(lc.quantite) as total')
        ->join('l.ligneCommandes', 'lc')
        ->groupBy('l.titre')
        ->orderBy('total', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
}

public function findByCriteria($filtreTitre, $filtrePrixMin, $filtrePrixMax, $filtreEditeur)
    {
        $queryBuilder = $this->createQueryBuilder('l');
    
        if ($filtreTitre) {
            $queryBuilder->andWhere('l.titre LIKE :titre')
                ->setParameter('titre', '%' . $filtreTitre . '%');
        }
    
        if ($filtrePrixMin) {
            $queryBuilder->andWhere('l.prix >= :prix_min')
                ->setParameter('prix_min', $filtrePrixMin);
        }
    
        if ($filtrePrixMax) {
            $queryBuilder->andWhere('l.prix <= :prix_max')
                ->setParameter('prix_max', $filtrePrixMax);
        }
    
        if ($filtreEditeur) {
            $queryBuilder->andWhere('l.editeur LIKE :editeur')
                ->setParameter('editeur', '%' . $filtreEditeur . '%');
        }
    
        return $queryBuilder->getQuery()->getResult();
    }

//    public function findOneBySomeField($value): ?Livres
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
