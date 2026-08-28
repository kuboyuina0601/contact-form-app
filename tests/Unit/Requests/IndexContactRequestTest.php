<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->category = Category::first();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function test_index_contact_validation_passes_with_valid_filters(): void
    {
        $data = [
            'keyword' => 'テスト',
            'gender' => 1,
            'category_id' => $this->category->id,
            'date' => '2026-01-01',
        ];

        $request = new IndexContactRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function test_index_contact_validation_fails_with_invalid_gender_or_category(): void
    {
        $data = [
            'gender' => 99,
            'category_id' => 99999,
        ];

        $request = new IndexContactRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function test_index_contact_validation_passes_at_keyword_max_255_chars(): void
    {
        $data = ['keyword' => str_repeat('a', 255)];

        $request = new IndexContactRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function test_index_contact_validation_fails_exceeding_keyword_max_255_chars(): void
    {
        $data = ['keyword' => str_repeat('a', 256)];

        $request = new IndexContactRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('keyword', $validator->errors()->toArray());
    }
}
