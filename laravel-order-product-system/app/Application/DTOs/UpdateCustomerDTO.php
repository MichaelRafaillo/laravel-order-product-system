<?php

namespace App\Application\DTOs;

class UpdateCustomerDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $address = null,
        public readonly ?bool $isActive = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            isActive: $data['is_active'] ?? null
        );
    }

    public function toArray(): array
    {
        $array = [];
        
        if ($this->name !== null) {
            $array['name'] = $this->name;
        }
        
        if ($this->email !== null) {
            $array['email'] = $this->email;
        }
        
        if ($this->phone !== null) {
            $array['phone'] = $this->phone;
        }
        
        if ($this->address !== null) {
            $array['address'] = $this->address;
        }
        
        if ($this->isActive !== null) {
            $array['is_active'] = $this->isActive;
        }
        
        return $array;
    }

    public function hasChanges(): bool
    {
        return !empty($this->toArray());
    }
}
