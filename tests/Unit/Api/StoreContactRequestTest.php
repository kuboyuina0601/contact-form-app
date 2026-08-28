<?php

namespace Tests\Unit\Api;

use App\Http\Requests\Api\V1\StoreContactRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    private StoreContactRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->request = new StoreContactRequest;
    }

    /** @test */
    public function required_fields_validation(): void
    {
        $validator = Validator::make([], $this->request->rules());

        $this->assertFalse($validator->passes());
        $errors = $validator->errors()->toArray();

        $this->assertArrayHasKey('first_name', $errors);
        $this->assertArrayHasKey('last_name', $errors);
        $this->assertArrayHasKey('gender', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('tel', $errors);
        $this->assertArrayHasKey('address', $errors);
        $this->assertArrayHasKey('category_id', $errors);
        $this->assertArrayHasKey('detail', $errors);
    }

    /** @test */
    public function tel_format_validation(): void
    {
        $invalidData = ['tel' => '090-1234-5678'];
        $validator = Validator::make($invalidData, ['tel' => $this->request->rules()['tel']]);
        $this->assertFalse($validator->passes());

        $validData = ['tel' => '09012345678'];
        $validator = Validator::make($validData, ['tel' => $this->request->rules()['tel']]);
        $this->assertTrue($validator->passes());
    }
}
