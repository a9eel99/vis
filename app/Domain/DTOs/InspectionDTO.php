<?php

namespace App\Domain\DTOs;

class InspectionDTO
{
    public function __construct(
        public readonly string $vehicleId,
        public readonly string $templateId,
        public readonly ?string $inspectorId = null,
        public readonly ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            vehicleId: $data['vehicle_id'],
            templateId: $data['template_id'],
            inspectorId: $data['inspector_id'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'vehicle_id' => $this->vehicleId,
            'template_id' => $this->templateId,
            'inspector_id' => $this->inspectorId,
            'notes' => $this->notes,
        ], fn($value) => $value !== null);
    }
}
