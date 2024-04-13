<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class UserFactory
 *
 * This class is responsible for creating instances of the User model.
 * It extends the Laravel's Factory class.
 *
 * @package Database\Factories
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * This method is responsible for defining the default state of the User model.
     * It uses the Faker library to generate fake data for the 'name' and 'email' fields,
     * and sets a default password for the 'password' field.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => app('hash')->make('password'),
        ];
    }
}