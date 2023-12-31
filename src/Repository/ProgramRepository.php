<?php

namespace App\Repository;

use App\Entity\Program;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Program>
 *
 * @method Program|null find($id, $lockMode = null, $lockVersion = null)
 * @method Program|null findOneBy(array $criteria, array $orderBy = null)
 * @method Program[]    findAll()
 * @method Program[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Program::class);
    }
    /*public function findLikeName(string $name)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.title LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('p.title', 'ASC')
            ->getQuery();

        return $queryBuilder->getResult();
    }*/
    /*public function findLikeName(string $name)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.actors', 'a') // jointure avec la table des acteurs
            ->where('p.title LIKE :name')
            ->orWhere('a.name LIKE :actorName') // condition sur le nom de l'acteur
            ->setParameter('name', '%' . $name . '%')
            ->setParameter('actorName', '%' . $name . '%')
            ->orderBy('p.title', 'ASC')
            ->getQuery();

        return $queryBuilder->getResult();
    }*/
    public function findLikeName(string $name): array
    {
        $result = [];

        if (!empty($name)) {
            $result = $this->createQueryBuilder('p')
                ->andWhere('p.title LIKE :name')
                ->setParameter('name', '%' . $name . '%')
                ->orderBy('p.title', 'ASC')
                ->getQuery()
                ->getResult();
        }

        return $result;
    }

}
