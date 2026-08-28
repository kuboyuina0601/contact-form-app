<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. ループ外で1回だけ全データ（ID）を取得（クエリ発行は2回のみ）
        $categoryIds = Category::pluck('id');
        $tags = Tag::all();

        // 2. 問い合わせダミーデータを20件作成
        Contact::factory()
            ->count(20)
            ->state(fn () => [
                'category_id' => $categoryIds->random(),
            ])
            ->create()
            ->each(function ($contact) use ($tags) {
                // メモリ上の $tags から抽出して attach（DBへの検索クエリは発生しない）
                $contact->tags()->attach(
                    $tags->random(rand(1, 3))->pluck('id')
                );
            });
    }
}
