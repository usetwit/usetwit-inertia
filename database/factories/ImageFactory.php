<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition(): array
    {
        $imageableTypes = $Image->validImageables;
        $imageableType = Arr::random($imageableTypes);
        $extension = $this->faker->fileExtension();

        return [
            'filename' => $this->faker->unique()->md5.'.'.$extension,
            'hash' => $this->faker->sha256,
            'extension' => $extension,
            'mime_type' => $this->faker->mimeType(),
            'alt_text' => $this->faker->optional()->words(3, true),
            'comments' => $this->faker->optional()->text,
            'size' => $this->faker->numberBetween(1000, 5000000),
            'width' => $this->faker->numberBetween(50, 250),
            'height' => $this->faker->numberBetween(50, 250),
            'default_image' => false,
            'imageable_type' => $imageableType,
            'imageable_id' => $imageableType::factory(),
            'user_id' => User::factory(),
            'deleted_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
