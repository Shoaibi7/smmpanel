<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => '12345678',
            'role' => 'admin',
        ]);

        // $categories = [
        //     ['name' => 'Instagram', 'slug' => 'instagram', 'icon' => 'fab fa-instagram'],
        //     ['name' => 'TikTok', 'slug' => 'tiktok', 'icon' => 'fab fa-tiktok'],
        //     ['name' => 'Facebook', 'slug' => 'facebook', 'icon' => 'fab fa-facebook'],
        //     ['name' => 'YouTube', 'slug' => 'youtube', 'icon' => 'fab fa-youtube'],
        // ];
        //
        // foreach ($categories as $cat) {
        //     $category = \App\Models\Category::create($cat);
        //
        //     if ($cat['name'] === 'Instagram') {
        //         \App\Models\Service::create([
        //             'category_id' => $category->id,
        //             'name' => 'Instagram Followers [Real] [Fast]',
        //             'description' => 'High quality real followers. Speed: 10k-50k per day.',
        //             'price_per_k' => 0.85,
        //             'min_qty' => 100,
        //             'max_qty' => 100000,
        //         ]);
        //         \App\Models\Service::create([
        //             'category_id' => $category->id,
        //             'name' => 'Instagram Likes [Instant] [HQ]',
        //             'description' => 'Real likes from active accounts.',
        //             'price_per_k' => 0.15,
        //             'min_qty' => 50,
        //             'max_qty' => 50000,
        //         ]);
        //     }
        //
        //     if ($cat['name'] === 'TikTok') {
        //         \App\Models\Service::create([
        //             'category_id' => $category->id,
        //             'name' => 'TikTok Views [Viral] [Super Fast]',
        //             'description' => 'Instant views for your tiktok videos.',
        //             'price_per_k' => 0.001,
        //             'min_qty' => 1000,
        //             'max_qty' => 10000000,
        //         ]);
        //     }
        // }
        //
        // \App\Models\Post::create([
        //     'title' => 'Top 5 Strategies to Grow Your Instagram in 2026',
        //     'slug' => 'grow-instagram-2026',
        //     'excerpt' => 'Discover the latest algorithms and engagement tactics for Instagram growth.',
        //     'body' => 'Instagram is constantly changing...',
        //     'is_published' => true,
        // ]);
        //
        // \App\Models\Post::create([
        //     'title' => 'Why SMM Panels are Essential for Small Businesses',
        //     'slug' => 'smm-panels-small-business',
        //     'excerpt' => 'Learn how social media marketing panels can save time and money.',
        //     'body' => 'Marketing on social media can be expensive...',
        //     'is_published' => true,
        // ]);
    }
}
