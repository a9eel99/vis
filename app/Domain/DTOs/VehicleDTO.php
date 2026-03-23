<?php

namespace App\Domain\DTOs;

class VehicleDTO
{
    public function __construct(
        public readonly string $make,
        public readonly string $model,
        public readonly int $year,
        public readonly ?string $vin = null,
        public readonly ?string $licensePlate = null,
        public readonly ?string $color = null,
        public readonly ?int $mileage = null,
        public readonly ?string $fuelType = null,
        public readonly ?string $transmission = null,
        public readonly ?string $ownerName = null,
        public readonly ?string $ownerPhone = null,
        public readonly ?string $ownerEmail = null,
        public readonly ?string $notes = null,
        public readonly ?string $image = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            make: $data['make'],
            model: $data['model'],
            year: (int) $data['year'],
            vin: $data['vin'] ?? null,
            licensePlate: $data['license_plate'] ?? null,
            color: $data['color'] ?? null,
            mileage: isset($data['mileage']) ? (int) $data['mileage'] : null,
            fuelType: $data['fuel_type'] ?? null,
            transmission: $data['transmission'] ?? null,
            ownerName: $data['owner_name'] ?? null,
            ownerPhone: $data['owner_phone'] ?? null,
            ownerEmail: $data['owner_email'] ?? null,
            notes: $data['notes'] ?? null,
            image: $data['image'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'vin' => $this->vin,
            'license_plate' => $this->licensePlate,
            'color' => $this->color,
            'mileage' => $this->mileage,
            'fuel_type' => $this->fuelType,
            'transmission' => $this->transmission,
            'owner_name' => $this->ownerName,
            'owner_phone' => $this->ownerPhone,
            'owner_email' => $this->ownerEmail,
            'notes' => $this->notes,
            'image' => $this->image,
        ], fn($value) => $value !== null);
    }
}
