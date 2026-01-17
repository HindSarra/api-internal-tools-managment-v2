<?php

namespace App\Repository;

use App\Entity\Tool;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class AnalyticsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tool::class);
    }

    /**
     * Returns the total monthly cost of all active tools in the company.
     */
    public function getTotalCompanyCostForActiveTools(): float
    {
        $queryBuilder = $this->createQueryBuilder('tool');

        $queryBuilder->select('COALESCE(SUM(tool.monthlyCost), 0)');
        $queryBuilder->andWhere('tool.status = :status');
        $queryBuilder->setParameter('status', 'active');

        $scalarResult = $queryBuilder->getQuery()->getSingleScalarResult();

        return (float) $scalarResult;
    }

    /**
     * Aggregates active tools costs by department.
     *
     * @return array<int, array{
     *   department: string,
     *   total_cost: mixed,
     *   tools_count: mixed,
     *   total_users: mixed
     * }>
     */
    public function getDepartmentCostAggregates(
        string $sortByParameter,
        string $orderParameter
    ): array {
        // Allowed sorting fields to avoid SQL injection
        $allowedSortFields = ['department', 'total_cost', 'tools_count', 'total_users'];
        $safeSortField = in_array($sortByParameter, $allowedSortFields, true)
            ? $sortByParameter
            : 'total_cost';

        $safeOrder = strtolower($orderParameter) === 'asc' ? 'ASC' : 'DESC';

        $queryBuilder = $this->createQueryBuilder('tool');

        $queryBuilder->select('tool.ownerDepartment AS department');
        $queryBuilder->addSelect('COALESCE(SUM(tool.monthlyCost), 0) AS total_cost');
        $queryBuilder->addSelect('COUNT(tool.id) AS tools_count');
        $queryBuilder->addSelect('COALESCE(SUM(tool.activeUsersCount), 0) AS total_users');

        // Global analytics rule: only active tools
        $queryBuilder->andWhere('tool.status = :status');
        $queryBuilder->setParameter('status', 'active');

        $queryBuilder->groupBy('tool.ownerDepartment');
        $queryBuilder->orderBy($safeSortField, $safeOrder);

        /** @var array<int, array{department: string, total_cost: mixed, tools_count: mixed, total_users: mixed}> $rows */
        $rows = $queryBuilder->getQuery()->getArrayResult();

        return $rows;
    }
}
