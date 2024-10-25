<?php

namespace BenBjurstrom\Plink\Database\Factories;

use BenBjurstrom\Plink\Enums\PlinkStatus;
use BenBjurstrom\Plink\Models\Plink;
use BenBjurstrom\Plink\Tests\Support\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlinkFactory extends Factory
{
    protected $model = Plink::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => PlinkStatus::ACTIVE,
            'ip_address' => fake()->ipv4(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PlinkStatus::EXPIRED,
            'created_at' => now()->subMinutes(6),
        ]);
    }

    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PlinkStatus::USED,
        ]);
    }

    public function superseded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PlinkStatus::SUPERSEDED,
        ]);
    }
}
