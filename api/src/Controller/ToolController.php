<?php

namespace App\Controller;

use App\Repository\ToolRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * API Controller : Internal Tools
 *
 * Responsabilité :
 * - Gérer la couche HTTP (routes, query params, status codes)
 * - Appeler le Repository pour la recherche/accès DB
 * - Transformer les entités en JSON (DTO simple)
 */
final class ToolController extends AbstractController
{
    /**
     * GET /api/tools
     *
     * Liste des outils avec :
     * - Filtres combinables (department, status, min_cost, max_cost, category)
     * - Pagination (page, limit)
     * - Tri (sort=name|monthly_cost|created_at, order=asc|desc)
     */
    #[Route('/api/tools', name: 'api_tools_list', methods: ['GET'])]
    public function list(Request $request, ToolRepository $toolRepository): JsonResponse
    {
        // Pagination : valeurs par défaut et bornes de sécurité
        $pageNumber = max(1, (int) $request->query->get('page', 1));
        $itemsPerPage = min(100, max(1, (int) $request->query->get('limit', 20)));
        $offset = ($pageNumber - 1) * $itemsPerPage;

        // Filtres business (optionnels)
        $filters = [
            'department' => $request->query->get('department'),
            'status'     => $request->query->get('status'),
            'min_cost'   => $request->query->get('min_cost') !== null ? (float) $request->query->get('min_cost') : null,
            'max_cost'   => $request->query->get('max_cost') !== null ? (float) $request->query->get('max_cost') : null,
            'category'   => $request->query->get('category'),
        ];

        // Tri (whitelist gérée dans le Repository)
        $sortKey = $request->query->get('sort');           // name | monthly_cost | created_at
        $sortOrder = $request->query->get('order', 'desc'); // asc | desc

        // Recherche DB + count filtré
        [$tools, $filteredCount] = $toolRepository->search(
            $filters,
            $itemsPerPage,
            $offset,
            $sortKey,
            $sortOrder
        );

        // Total global (sans filtre)
        $totalCount = $toolRepository->count([]);

        // Transformation Entity -> JSON (réponse attendue par le sujet)
        $toolsData = array_map(static function ($tool) {
            return [
                'id' => $tool->getId(),
                'name' => $tool->getName(),
                'description' => $tool->getDescription(),
                'vendor' => $tool->getVendor(),
                'category' => $tool->getCategory()?->getName(),
                'monthly_cost' => (float) $tool->getMonthlyCost(),
                'owner_department' => $tool->getOwnerDepartment(),
                'status' => $tool->getStatus(),
                'website_url' => $tool->getWebsiteUrl(),
                'active_users_count' => $tool->getActiveUsersCount(),
                'created_at' => $tool->getCreatedAt()?->format(DATE_ATOM),
            ];
        }, $tools);

        // On n'affiche que les filtres réellement appliqués
        $filtersApplied = array_filter($filters, static fn ($value) => $value !== null && $value !== '');

        return new JsonResponse([
            'data' => $toolsData,
            'total' => $totalCount,
            'filtered' => $filteredCount,
            'filters_applied' => $filtersApplied,
            'page' => $pageNumber,
            'limit' => $itemsPerPage,
        ]);
    }

    /**
     * GET /api/tools/{id}
     *
     * Détail complet d'un outil.
     * - 404 si inexistant
     * - Calcul total_monthly_cost = monthly_cost * active_users_count
     */
    #[Route('/api/tools/{id}', name: 'api_tools_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, ToolRepository $toolRepository): JsonResponse
    {
        $tool = $toolRepository->find($id);

        if (!$tool) {
            return new JsonResponse([
                'error' => 'Tool not found',
                'message' => "Tool with ID $id does not exist",
            ], 404);
        }

        $monthlyCost = (float) $tool->getMonthlyCost();
        $activeUsersCount = (int) $tool->getActiveUsersCount();
        $totalMonthlyCost = $monthlyCost * $activeUsersCount;

        return new JsonResponse([
            'id' => $tool->getId(),
            'name' => $tool->getName(),
            'description' => $tool->getDescription(),
            'vendor' => $tool->getVendor(),
            'website_url' => $tool->getWebsiteUrl(),
            'category' => $tool->getCategory()?->getName(),
            'monthly_cost' => $monthlyCost,
            'owner_department' => $tool->getOwnerDepartment(),
            'status' => $tool->getStatus(),
            'active_users_count' => $activeUsersCount,
            'total_monthly_cost' => $totalMonthlyCost,
            'created_at' => $tool->getCreatedAt()?->format(DATE_ATOM),
            'updated_at' => $tool->getUpdatedAt()?->format(DATE_ATOM),
            'usage_metrics' => [
                'last_30_days' => [
                    // TODO: à brancher sur une vraie table de métriques si disponible
                    'total_sessions' => 127,
                    'avg_session_minutes' => 45,
                ],
            ],
        ]);
    }
}
