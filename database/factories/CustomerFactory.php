<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_group_id' => CustomerGroup::query()->inRandomOrder()->value('id'),
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'telephone' => fake()->phoneNumber(),
            'password' => 'password',
            'status' => true,
        ];
    }

    /**
     * Create customer with an address.
     */
    public function withAddress(): static
    {
        return $this->afterCreating(function (Customer $customer): void {
            Address::factory()->create([
                'customer_id' => $customer->id,
                'firstname' => $customer->firstname,
                'lastname' => $customer->lastname,
            ]);
        });
    }
}
