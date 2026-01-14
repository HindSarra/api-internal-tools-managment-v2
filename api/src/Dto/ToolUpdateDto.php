<?php
/**
 * DTO de mise à jour d’un Tool (CRUD).
 * Champs optionnels pour modification partielle.
 */

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ToolUpdateDto
{
    #[Assert\Length(min: 2, max: 100)]
    public ?string $name = null;

    #[Assert\Length(max: 255)]
    public ?string $description = null;

    #[Assert\Length(max: 100)]
    public ?string $vendor = null;

    #[Assert\Url]
    #[Assert\Length(max: 255)]
    public ?string $website_url = null;

    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/', message: 'Monthly cost must have max 2 decimals')]
    public $monthly_cost = null;

    #[Assert\Choice(choices: ['Engineering','Sales','Marketing','HR','Finance','Operations','Design'])]
    public ?string $owner_department = null;

    #[Assert\Choice(choices: ['active','deprecated','trial'])]
    public ?string $status = null;

    #[Assert\Type('integer')]
    #[Assert\GreaterThanOrEqual(0)]
    public $active_users_count = null;

    #[Assert\Type('integer')]
    public $category_id = null;
}
