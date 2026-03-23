<?php

namespace App\Domain\DTOs;

class TemplateDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly bool $isActive = true,
        public readonly string $scoringMode = 'scored',
        public readonly ?string $fuelType = null,
        public readonly float $price = 0,
        public readonly array $sections = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            isActive: $data['is_active'] ?? true,
            scoringMode: $data['scoring_mode'] ?? 'scored',
            fuelType: $data['fuel_type'] ?? null,
            price: (float) ($data['price'] ?? 0),
            sections: $data['sections'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->isActive,
            'scoring_mode' => $this->scoringMode,
            'fuel_type' => $this->fuelType,
            'price' => $this->price,
        ];
    }
}