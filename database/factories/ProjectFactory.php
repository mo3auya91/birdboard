<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $item = [
            'owner_id' => function () {
                return User::factory()->create()->id;
            },
            'notes' => $this->faker->sentence,
        ];
        foreach (LaravelLocalization::getSupportedLocales() as $key => $locale) {
            $item['title'][$key] = $this->faker->sentence(4);
            $item['description'][$key] = $this->faker->sentence(4);
        }
        return $item;
    }
}
