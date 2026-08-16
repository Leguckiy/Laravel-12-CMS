<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Country;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'company' => fake()->optional()->company(),
            'address_1' => fake()->streetAddress(),
            'address_2' => fake()->optional()->secondaryAddress(),
            'city' => fake()->city(),
            'postcode' => fake()->postcode(),
            'country_id' => Country::query()->where('status', true)->inRandomOrder()->value('id'),
            'default' => true,
        ];
    }
}
