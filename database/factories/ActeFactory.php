<?php

namespace Database\Factories;

use App\Models\Acte;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Acte>
 */
class ActeFactory extends Factory
{
    protected $model = Acte::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'code' => 'ACT-'.fake()->unique()->numerify('####'),
            'nom' => fake()->words(3, true),
            'categorie' => fake()->randomElement(['consultation', 'chirurgie', 'imagerie', 'biologie', 'soins', 'exploration', 'maternite']),
            'tarif_normal' => fake()->randomElement([2000, 5000, 10000, 15000, 25000, 50000]),
            'tarif_amo' => fake()->randomElement([1500, 4000, 8000, 12000, null]),
            'tarif_specifique' => null,
            'part_medecin_pourcentage' => 50.00,
            'part_clinique_pourcentage' => 50.00,
            'statut' => true,
            'description' => fake()->sentence(),
        ];
    }
}
