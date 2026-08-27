<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function test_tag_belongs_to_many_contacts(): void
    {
        $category = Category::create(['content' => '商品のお届けについて']);
        $tag = Tag::create(['name' => '質問']);

        $contacts = Contact::factory()->count(2)->create([
            'category_id' => $category->id,
        ]);

        $tag->contacts()->sync($contacts->pluck('id'));

        $this->assertCount(2, $tag->fresh()->contacts);
    }

    /** @test */
    public function test_tag_has_zero_contacts(): void
    {
        $tag = Tag::create(['name' => '質問']);

        $this->assertCount(0, $tag->fresh()->contacts);
    }

    /** @test */
    public function test_tag_has_one_contact(): void
    {
        $category = Category::create(['content' => '商品のお届けについて']);
        $tag = Tag::create(['name' => '質問']);

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $tag->contacts()->sync([$contact->id]);

        $this->assertCount(1, $tag->fresh()->contacts);
    }
}