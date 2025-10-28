<?php

namespace Database\Factories;

use App\Models\RequestLegalLetter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestLegalLetter>
 */
class RequestLegalLetterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'name' => $this->faker->name(),
            'nik' => $this->faker->numerify('################'), // 16 digit NIK
            'description' => $this->faker->paragraph(3),
            'ktp_image_path' => 'documents/ktp/sample-ktp.jpg',
            'kk_image_path' => 'documents/kk/sample-kk.jpg',
            'status' => $this->faker->randomElement(['Waiting', 'Pending', 'Processing', 'Completed']),
            'requested_by' => User::factory(),
            'assigned_company_id' => null,
            'legal_letter_id' => null,
        ];
    }

    /**
     * Indicate that the request is waiting.
     */
    public function waiting(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Waiting',
        ]);
    }

    /**
     * Indicate that the request is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Pending',
        ]);
    }

    /**
     * Indicate that the request is processing.
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Processing',
            'assigned_company_id' => 1, // Default to first company
        ]);
    }

    /**
     * Indicate that the request is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Completed',
            'assigned_company_id' => 1, // Default to first company
        ]);
    }
}
