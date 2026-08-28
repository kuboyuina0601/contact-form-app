<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /** @test */
    public function can_get_contacts_list(): void
    {
        $response = $this->getJson('/api/v1/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    /** @test */
    public function can_get_contact_detail(): void
    {
        $contact = Contact::first();

        $response = $this->getJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $contact->id);
    }

    /** @test */
    public function returns_404_if_contact_not_found(): void
    {
        $response = $this->getJson('/api/v1/contacts/99999');

        $response->assertStatus(404)
            ->assertJson(['error' => 'お問い合わせが見つかりませんでした。']);
    }

    /** @test */
    public function can_create_contact(): void
    {
        $category = Category::first();
        $tag = Tag::first();

        $data = [
            'first_name' => 'テスト',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'api_spec_test@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区1-1-1',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'detail' => '新規お問い合わせテスト',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->postJson('/api/v1/contacts', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.email', 'api_spec_test@example.com');

        $this->assertDatabaseHas('contacts', ['email' => 'api_spec_test@example.com']);
    }

    /** @test */
    public function store_validation_error_returns_422(): void
    {
        $response = $this->postJson('/api/v1/contacts', []);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['first_name', 'last_name', 'gender', 'email', 'tel', 'address', 'category_id', 'detail']);
    }

    /** @test */
    public function can_update_contact(): void
    {
        $contact = Contact::first();
        $category = Category::first();
        $tag = Tag::first();

        $updateData = [
            'first_name' => '更新名',
            'last_name' => '更新姓',
            'gender' => 2,
            'email' => 'api_update_spec@example.com',
            'tel' => '08098765432',
            'address' => '大阪府大阪市1-1-1',
            'category_id' => $category->id,
            'detail' => '更新問い合わせテスト',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->putJson("/api/v1/contacts/{$contact->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.email', 'api_update_spec@example.com');

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'email' => 'api_update_spec@example.com',
        ]);
    }

    /** @test */
    public function can_delete_contact(): void
    {
        $contact = Contact::latest('id')->first();

        $response = $this->deleteJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
