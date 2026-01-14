<?php

namespace App\Repository;

use App\Entity\Tool;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository dédié à l'entité Tool.
 *
 * Responsabilité :
 * - Construire les requêtes complexes (filtres, tri, pagination)
 * - Aucune logique HTTP ici (réservée aux Controllers)
 *
 * @extends ServiceEntityRepository<Tool>
 */
class ToolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tool::class);
    }

    /**
     * Recherche des outils avec filtres combinables, pagination et tri.
     *
     * Utilisé par :
     * - GET /api/tools
     *
     * @param array       $filters  Filtres business (department, status, cost, category)
     * @param int         $limit    Nombre de résultats par page
     * @param int         $offset   Décalage pour pagination
     * @param string|null $sort     Champ de tri demandé par l'utilisateur
     * @param string      $order    Sens du tri (asc|desc)
     *
     * @return array [Tool[] $tools, int $filteredCount]
     */
    public function search(
        array $filters,
        int $limit,
        int $offset,
        ?string $sort,
        string $order
    ): array {
        /*
         *
         * 1) Query principale (data)
         * Objectif :
         * - Appliquer les filtres business
         * - Charger la catégorie associée
         * - Gérer tri + pagination
         */
        $queryBuilder = $this->createQueryBuilder('tool')
            ->leftJoin('tool.category', 'category')
            ->addSelect('category');

        // Filtre par département propriétaire
        if (!empty($filters['department'])) {
            $queryBuilder
                ->andWhere('tool.ownerDepartment = :department')
                ->setParameter('department', $filters['department']);
        }

        // Filtre par statut (active | deprecated | trial)
        if (!empty($filters['status'])) {
            $queryBuilder
                ->andWhere('tool.status = :status')
                ->setParameter('status', $filters['status']);
        }

        // Filtre coût minimum
        if ($filters['min_cost'] !== null) {
            $queryBuilder
                ->andWhere('tool.monthlyCost >= :minCost')
                ->setParameter('minCost', $filters['min_cost']);
        }

        // Filtre coût maximum
        if ($filters['max_cost'] !== null) {
            $queryBuilder
                ->andWhere('tool.monthlyCost <= :maxCost')
                ->setParameter('maxCost', $filters['max_cost']);
        }

        // Filtre par catégorie (nom lisible côté API)
        if (!empty($filters['category'])) {
            $queryBuilder
                ->andWhere('category.name = :categoryName')
                ->setParameter('categoryName', $filters['category']);
        }

        /*
         * 2) Gestion du tri sécurisé
         *
         * Whitelist pour éviter :
         * - injection SQL
         * - tri sur champs inexistants
         */
        $allowedSortFields = [
            'name' => 'tool.name',
            'monthly_cost' => 'tool.monthlyCost',
            'created_at' => 'tool.createdAt',
        ];

        $sortKey = $sort ?? '';
        $sortField = $allowedSortFields[$sortKey] ?? 'tool.id';

        $sortOrder = strtolower($order) === 'asc' ? 'ASC' : 'DESC';

        $queryBuilder
            ->orderBy($sortField, $sortOrder)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        // Résultats paginés
        $tools = $queryBuilder->getQuery()->getResult();

        /*
         * 3) Requête de comptage filtré
         * Objectif :
         * - Obtenir le nombre total de résultats AVEC filtres
         * - Indispensable pour la pagination côté frontend
         */
        $countQueryBuilder = $this->createQueryBuilder('tool')
            ->select('COUNT(tool.id)')
            ->leftJoin('tool.category', 'category');

        // Répétition volontaire des filtres pour cohérence
        if (!empty($filters['department'])) {
            $countQueryBuilder
                ->andWhere('tool.ownerDepartment = :department')
                ->setParameter('department', $filters['department']);
        }

        if (!empty($filters['status'])) {
            $countQueryBuilder
                ->andWhere('tool.status = :status')
                ->setParameter('status', $filters['status']);
        }

        if ($filters['min_cost'] !== null) {
            $countQueryBuilder
                ->andWhere('tool.monthlyCost >= :minCost')
                ->setParameter('minCost', $filters['min_cost']);
        }

        if ($filters['max_cost'] !== null) {
            $countQueryBuilder
                ->andWhere('tool.monthlyCost <= :maxCost')
                ->setParameter('maxCost', $filters['max_cost']);
        }

        if (!empty($filters['category'])) {
            $countQueryBuilder
                ->andWhere('category.name = :categoryName')
                ->setParameter('categoryName', $filters['category']);
        }

        $filteredCount = (int) $countQueryBuilder
            ->getQuery()
            ->getSingleScalarResult();

        return [$tools, $filteredCount];
    }
}
