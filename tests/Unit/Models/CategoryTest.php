<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
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
    public function test_category_has_many_contacts(): void
    {
        $category = Category::create(['content' => '商品のお届けについて']);

        Contact::factory()->count(2)->create([
            'category_id' => $category->id,
        ]);

        $this->assertCount(2, $category->contacts);
    }

    /** @test */
    public function test_category_has_zero_contacts(): void
    {
        $category = Category::create(['content' => '商品のお届けについて']);

        $this->assertCount(0, $category->contacts);
    }

    /** @test */
    public function test_category_has_one_contact(): void
    {
        $category = Category::create(['content' => '商品のお届けについて']);

        Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->assertCount(1, $category->contacts);
    }
}