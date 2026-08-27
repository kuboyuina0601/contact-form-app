<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreTagRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreTagRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function test_tag_store_validation_passes(): void
    {
        $data = ['name' => '新規タグ'];

        $request = new StoreTagRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function test_tag_store_validation_fails_when_duplicate_name(): void
    {
        $data = ['name' => '質問'];

        $request = new StoreTagRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function test_tag_store_validation_passes_at_max_50_chars(): void
    {
        $data = ['name' => str_repeat('a', 50)];

        $request = new StoreTagRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function test_tag_store_validation_fails_exceeding_max_50_chars(): void
    {
        $data = ['name' => str_repeat('a', 51)];

        $request = new StoreTagRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }
}