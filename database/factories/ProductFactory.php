<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class ProductFactory extends Factory
{
    public function definition()
    {
        return [
            "title" => $this->faker->sentence(4),
            "category_id" => $this->faker->numberBetween($min=1, $max=4),
            "price" => $this->faker->numberBetween($min=10000, $max=70000),
            "description" => $this->faker->text(200),
            "image" => $this->faker->imageUrl($width = 640, $height = 480),
            "rate" => $this->faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 9),
            "count" => $this->faker->numberBetween($min=10, $max=200),
        ];
    }
}
