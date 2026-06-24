<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Widget;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $meteo = Service::create(['name' => 'Meteo', 'is_public' => true]);

        $meteo->widgets()->create([

            'name' => 'city_temperature',
            'description' => 'Affiche la température d\'une ville',
        ])->parameters()->createMany([
            ['name' => 'city', 'type' => 'string'],
        ]);

        $meteo->widgets()->create([
            'name' => 'weather_prediction',
            'description' => 'Prévisions météo d\'une ville',
        ])->parameters()->createMany([
            ['name' => 'city', 'type' => 'string'],
        ]);

        // Service Reddit
        $reddit = Service::create(['name' => 'Reddit', 'is_public' => true]);

        $reddit->widgets()->create([
            'name' => 'reddit_posts',
            'description' => 'Derniers posts d\'un subreddit',
        ])->parameters()->createMany([
            ['name' => 'subreddit', 'type' => 'string'],
            ['name' => 'number', 'type' => 'integer'],
        ]);

        // Service YouTube
        $youtube = Service::create(['name' => 'YouTube', 'is_public' => false]);

        $youtube->widgets()->create([
            'name' => 'video_views',
            'description' => 'Nombre de vues d\'une vidéo',
        ])->parameters()->createMany([
            ['name' => 'video_id', 'type' => 'string'],
        ]);

        // Service RSS
        $rss = Service::create(['name' => 'RSS', 'is_public' => true]);

        $rss->widgets()->create([
            'name' => 'rss_feed',
            'description' => 'Derniers articles d\'un flux RSS',
        ])->parameters()->createMany([
            ['name' => 'url', 'type' => 'string'],
            ['name' => 'number', 'type' => 'integer'],
        ]);
    }
}
