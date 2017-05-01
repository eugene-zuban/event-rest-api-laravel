<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Event::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->company,
        'date' => $faker
          ->dateTimeBetween('now', '+1 year')
          ->format('Y-m-d H:i:s'),
        'impact' => $faker->randomNumber(),
        'instrument' => $faker->word,
        'actual' => $faker->randomFloat(6),
        'forecast' => $faker->randomFloat(6),
    ];
});
