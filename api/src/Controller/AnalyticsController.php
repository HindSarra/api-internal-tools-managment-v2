<?php

namespace App\Controller;

use App\Repository\AnalyticsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AnalyticsController
{
    #[Route('/api/analytics/department-costs', name: 'analytics_department_costs', methods: ['GET'])]
    public function getDepartmentCosts(
        Request $request,
        AnalyticsRepository $analyticsRepository
    ): JsonResponse {
        $sortByParameter = (string) $request->query->get('sort_by', 'total_cost');
        $orderParameter = (string) $request->query->get('order', 'desc');

        $totalCompanyCost = $analyticsRepository->getTotalCompanyCostForActiveTools();
        $departmentRows = $analyticsRepository->getDepartmentCostAggregates(
            $sortByParameter,
            $orderParameter
        );

        if ($totalCompanyCost <= 0.0 && count($departmentRows) === 0) {
            return new JsonResponse([
                'data' => [],
                'message' => 'No analytics data available - ensure tools data exists',
                'summary' => [
                    'total_company_cost' => 0,
                ],
            ]);
        }

        $data = [];

        foreach ($departmentRows as $row) {
            $departmentName = (string) $row['department'];
            $totalDepartmentCost = round((float) $row['total_cost'], 2);
            $toolsCount = (int) $row['tools_count'];
            $totalUsers = (int) $row['total_users'];

            // average_cost_per_tool = total_cost / tools_count
            $averageCostPerTool = 0.00;
            if ($toolsCount > 0) {
                $averageCostPerTool = round($totalDepartmentCost / $toolsCount, 2);
            }

            // cost_percentage = (department.total_cost / company.total_cost) * 100
            $costPercentage = 0.0;
            if ($totalCompanyCost > 0.0) {
                $costPercentage = round(
                    ($totalDepartmentCost / $totalCompanyCost) * 100,
                    1
                );
            }

            $data[] = [
                'department' => $departmentName,
                'total_cost' => $totalDepartmentCost,
                'tools_count' => $toolsCount,
                'total_users' => $totalUsers,
                'average_cost_per_tool' => $averageCostPerTool,
                'cost_percentage' => $costPercentage,
            ];
        }

        // Determine the most expensive department (tie-breaker: alphabetical order)
        $mostExpensiveDepartment = null;

        if (count($data) > 0) {
            usort($data, static function (array $first, array $second): int {
                if ($first['total_cost'] === $second['total_cost']) {
                    return strcmp(
                        (string) $first['department'],
                        (string) $second['department']
                    );
                }

                return $second['total_cost'] <=> $first['total_cost'];
            });

            $mostExpensiveDepartment = (string) $data[0]['department'];
        }

        return new JsonResponse([
            'data' => $data,
            'summary' => [
                'total_company_cost' => round($totalCompanyCost, 2),
                'departments_count' => count($data),
                'most_expensive_department' => $mostExpensiveDepartment,
            ],
        ]);
    }
    #[Route('/api/analytics/tools-by-category', name: 'analytics_tools_by_category', methods: ['GET'])]
     public function getToolsByCategory(
    AnalyticsRepository $analyticsRepository
): JsonResponse {
    $totalCompanyCost = $analyticsRepository->getTotalCompanyCostForActiveTools();
    $categoryRows = $analyticsRepository->getCategoryAggregatesForActiveTools();

    if ($totalCompanyCost <= 0.0 && count($categoryRows) === 0) {
        return new JsonResponse([
            'data' => [],
            'message' => 'No analytics data available - ensure tools data exists',
            'insights' => [
                'most_expensive_category' => null,
                'most_efficient_category' => null,
            ],
        ]);
    }

    $data = [];

    foreach ($categoryRows as $row) {
        $categoryName = (string) $row['category_name'];
        $toolsCount = (int) $row['tools_count'];
        $totalCost = round((float) $row['total_cost'], 2);
        $totalUsers = (int) $row['total_users'];

        $percentageOfBudget = 0.0;
        if ($totalCompanyCost > 0.0) {
            $percentageOfBudget = round(($totalCost / $totalCompanyCost) * 100, 1);
        }

        $averageCostPerUser = null;
        if ($totalUsers > 0) {
            $averageCostPerUser = round($totalCost / $totalUsers, 2);
        }

        $data[] = [
            'category_name' => $categoryName,
            'tools_count' => $toolsCount,
            'total_cost' => $totalCost,
            'total_users' => $totalUsers,
            'percentage_of_budget' => $percentageOfBudget,
            'average_cost_per_user' => $averageCostPerUser,
        ];
    }

    // Insights
    $mostExpensiveCategory = null;
    if (count($data) > 0) {
        // Already ordered by total_cost DESC, but we compute safely
        $sortedByCost = $data;
        usort($sortedByCost, static fn (array $a, array $b): int => $b['total_cost'] <=> $a['total_cost']);
        $mostExpensiveCategory = (string) $sortedByCost[0]['category_name'];
    }

    // Most efficient = lowest average_cost_per_user (exclude categories with null)
    $categoriesWithUsers = array_values(array_filter(
        $data,
        static fn (array $item): bool => $item['average_cost_per_user'] !== null
    ));

    $mostEfficientCategory = null;
    if (count($categoriesWithUsers) > 0) {
        usort($categoriesWithUsers, static function (array $a, array $b): int {
            if ($a['average_cost_per_user'] === $b['average_cost_per_user']) {
                return strcmp((string) $a['category_name'], (string) $b['category_name']);
            }
            return $a['average_cost_per_user'] <=> $b['average_cost_per_user'];
        });
        $mostEfficientCategory = (string) $categoriesWithUsers[0]['category_name'];
    }

    return new JsonResponse([
        'data' => $data,
        'insights' => [
            'most_expensive_category' => $mostExpensiveCategory,
            'most_efficient_category' => $mostEfficientCategory,
        ],
    ]);
}

}
