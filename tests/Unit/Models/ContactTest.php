<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
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
    public function test_contact_belongs_to_category_and_syncs_tags(): void
    {
        $category = Category::create(['content' => '商品のお届けについて']);

        $tag1 = Tag::create(['name' => '質問']);
        $tag2 = Tag::create(['name' => '要望']);

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $contact->tags()->attach([$tag1->id, $tag2->id]);

        $this->assertEquals($category->id, $contact->category->id);
        $this->assertInstanceOf(Category::class, $contact->category);
        $this->assertCount(2, $contact->fresh()->tags);
    }

    /** @test */
    public function test_contact_has_zero_tags(): void
    {
        $category = Category::create(['content' => '商品のお届けについて']);

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->assertCount(0, $contact->fresh()->tags);
    }

    /** @test */
    public function test_contact_has_one_tag(): void
    {
        $category = Category::create(['content' => '商品のお届けについて']);
        $tag = Tag::create(['name' => '質問']);

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $contact->tags()->attach($tag->id);

        $this->assertCount(1, $contact->fresh()->tags);
    }
}