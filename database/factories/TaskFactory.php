<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'task_name' => $this->faker->sentence(rand(1,4)),
            'is_done' => $this->faker->boolean(0),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
