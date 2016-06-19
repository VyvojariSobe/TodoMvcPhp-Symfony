<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ToDo;

/**
 * Class ToDoRepository.
 *
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class ToDoRepository extends \Doctrine\ORM\EntityRepository
{
    const FILTER_ALL = 'all';
    const FILTER_ACTIVE = 'active';
    const FILTER_COMPLETED = 'completed';

    /**
     * @param string $filter
     *
     * @return ToDo[]
     */
    public function getAllToDos($filter = self::FILTER_ALL)
    {
        if (!array_key_exists($filter, $this->getPossibleFilters())) {
            throw new \InvalidArgumentException(sprintf('Filter "%s" is not implemented.', $filter));
        }

        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.isDone', 'asc')
            ->addOrderBy('t.createdAt', 'desc');

        if ($filter == self::FILTER_ACTIVE) {
            $qb->where('t.isDone = :isDone')
                ->setParameter('isDone', false);
        }

        if ($filter == self::FILTER_COMPLETED) {
            $qb->where('t.isDone = :isDone')
                ->setParameter('isDone', true);
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @param $isDone
     *
     * @return mixed
     */
    public function countToDos($isDone)
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->select($qb->expr()->count('t.id'))
            ->where('t.isDone = :isDone')
            ->setParameter('isDone', $isDone)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return mixed
     */
    public function activeToDosCounter()
    {
        return $this->countToDos(false);
    }

    /**
     * @return mixed
     */
    public function countCompletedToDos()
    {
        return $this->countToDos(true);
    }

    /**
     * @return array
     */
    public function getPossibleFilters()
    {
        return [
            self::FILTER_ALL       => 'All',
            self::FILTER_ACTIVE    => 'Active',
            self::FILTER_COMPLETED => 'Completed',
        ];
    }
}
