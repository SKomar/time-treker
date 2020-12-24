<?php


namespace App\Repository;


use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function search(array $taskPayload)
    {
        return $this->createQueryBuilder('tasks')
            ->andWhere('tasks.date >= :val1 AND tasks.date <= :val2')
            ->setParameter('val1', $taskPayload['from'])
            ->setParameter('val2', $taskPayload['to'])
            ->orderBy('tasks.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}