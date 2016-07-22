<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use DoctrineExtensions\Query\Mysql\Acos;
use DoctrineExtensions\Query\Mysql\Cos;
use DoctrineExtensions\Query\Mysql\Sin;
use DoctrineExtensions\Query\Mysql\Radians;

/**
 * BlogPostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BookingRepository extends EntityRepository
{
    public function findMyBooking($user,$query)
    {
        $expr = new Expr();
        $qb = $this->createQueryBuilder('booking')
            ->where($expr->eq('booking.user', ':user'))
            ->setParameter('user', $user);
        return $qb;

    }
  }
