<?php
/**
 * DTO de création d’un Tool (CRUD).
 * Gère les données entrantes et leur validation.
 */


namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ToolCreateDto
{
    #[Assert\NotBlank(message: 'Name is required')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Name must be at least 2 characters', maxMessage: 'Name must be at most 100 characters')]
    public ?string $name = null;

    #[Assert\Length(max: 100, maxMessage: 'Vendor must be at most 100 characters')]
    #[Assert\NotBlank(message: 'Vendor is required')]
    public ?string $vendor = null;

    #[Assert\Length(max: 255)]
    public ?string $description = null;

    #[Assert\Url(message: 'Website URL must be a valid URL')]
    #[Assert\Length(max: 255)]
    public ?string $website_url = null;

    // Decimal (2 max) : on valide avec Regex simple
    #[Assert\NotNull(message: 'Monthly cost is required')]
    #[Assert\GreaterThanOrEqual(0, message: 'Monthly cost must be >= 0')]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/', message: 'Monthly cost must have max 2 decimals')]
    public $monthly_cost = null;

    #[Assert\NotBlank(message: 'Owner department is required')]
    #[Assert\Choice(
        choices: ['Engineering','Sales','Marketing','HR','Finance','Operations','Design'],
        message: 'Owner department must be one of: Engineering, Sales, Marketing, HR, Finance, Operations, Design'
    )]
    public ?string $owner_department = null;

    #[Assert\NotNull(message: 'Category ID is required')]
    #[Assert\Type(type: 'integer', message: 'Category ID must be an integer')]
    public $category_id = null;
}
