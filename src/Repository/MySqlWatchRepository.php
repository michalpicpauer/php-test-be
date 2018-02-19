<?php

namespace App\Repository;

use App\DTO\MySqlWatchDTO;
use App\Entity\Watch;
use App\Exception\MySqlRepositoryException;
use App\Exception\MySqlWatchNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MySqlWatchRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Watch::class);
    }

    /**
     * @param int $id
     *
     * @return MySqlWatchDTO
     *
     * @throws MySqlWatchNotFoundException Is thrown when the watch could not be found in a mysql database,
     * eg. watch with the associated id does not exist.
     *
     * @throws MySqlRepositoryException May be thrown on a fatal error, such as connection to a database failed.
     */
    public function getWatchById(int $id): MySqlWatchDTO
    {
        try {
            return $this->createQueryBuilder('w')
                ->select("NEW App\\DTO\\MySqlWatchDTO(w.id, w.title, w.price, w.description)")
                ->where('w.id = :id')->setParameter('id', $id)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            throw new MySqlWatchNotFoundException($e->getMessage());
        } catch (\Exception $e) {
            throw new MySqlRepositoryException($e->getMessage());
        }
    }
}
