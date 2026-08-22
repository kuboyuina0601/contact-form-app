<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()->id, // 既存のカテゴリからランダムに1つのIDを取得
            'first_name'  => fake()->firstName(),
            'last_name'   => fake()->lastName(),
            'gender'      => fake()->numberBetween(1, 3), // 1:男性 2:女性 3:その他
            'email'       => fake()->safeEmail(),
            'tel'         => fake()->numerify(str_repeat('#', rand(10, 11))),
            'address'     => fake()->address(),
            'building'    => fake()->secondaryAddress(),
            'detail'      => fake()->realText(50),
        ];
    }
}
