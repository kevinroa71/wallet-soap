<?php

namespace App\Repository;

use App\Entity\Pagos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pagos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagos[]    findAll()
 * @method Pagos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagos::class);
    }

    public function save(Pagos $pago)
    {
        $this->_em->persist($pago);
        $this->_em->flush();
    }

    public function findOneByTokenAndSession($token, $session): ?Pagos
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.token = :token AND p.session = :session AND p.status = 0')
            ->setParameter('token', $token)
            ->setParameter('session', $session)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
