<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Tag;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //問い合わせダミーデータを20件投入し、各問い合わせに既存のカテゴリからランダムに1~3つのIDを割り当てる
        Contact::factory()
            ->count(20)
            ->create()
            ->each(function ($contact) {
                // 既存のタグからランダムに1〜3件のIDを取得
                $tagIds = Tag::inRandomOrder()->take(rand(1, 3))->pluck('id');
                // 中間テーブルに紐付け
                $contact->tags()->attach($tagIds);
            });
    }    
}
