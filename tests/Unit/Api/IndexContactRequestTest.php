<?php

namespace Tests\Unit\Api;

use App\Http\Requests\Api\V1\IndexContactRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    private IndexContactRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->request = new IndexContactRequest;
    }

    /** @test */
    public function valid_data_passes_validation(): void
    {
        $data = [
            'keyword' => 'テスト',
            'gender' => 1,
            'category_id' => 1,
            'date' => '2026-01-01',
            'page' => 1,
            'per_page' => 20,
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function invalid_gender_fails_validation(): void
    {
        $data = ['gender' => 0];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('gender', $validator->errors()->toArray());
    }
}
