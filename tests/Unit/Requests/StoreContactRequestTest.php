<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->category = Category::first();
        $this->tag = Tag::first();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function test_contact_validation_passes_at_boundary_phone_length(): void
    {
        $data = [
            'first_name'  => '山田',
            'last_name'   => '太郎',
            'gender'      => 1,
            'email'       => 'test@example.com',
            'tel'         => '0312345678', 
            'address'     => '東京都渋谷区1-1',
            'detail'      => 'お問い合わせ内容です。',
            'category_id' => $this->category->id,
        ];

        $request = new StoreContactRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }
}