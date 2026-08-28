<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::create(['content' => '商品のお届けについて']);
    }

    /** @test */
    public function test_admin_can_search_and_paginate_contacts(): void
    {
        Contact::factory()->count(15)->create([
            'category_id' => $this->category->id,
            'first_name' => '検索テスト名',
        ]);

        $response = $this->actingAs($this->user)->get('/admin?keyword=検索テスト');

        $response->assertStatus(200);
        $response->assertSee('検索テスト名');
    }

    /** @test */
    public function test_admin_can_view_contact_detail(): void
    {
        $contact = Contact::factory()->create([
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)->get("/admin/contacts/{$contact->id}");

        $response->assertStatus(200);
        $response->assertSee($contact->email);
    }

    /** @test */
    public function test_admin_can_delete_contact(): void
    {
        $contact = Contact::factory()->create([
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)->delete("/admin/contacts/{$contact->id}");

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
        $response->assertRedirect('/admin');
    }

    /** @test */
    public function test_authenticated_user_can_download_csv_with_filter_conditions(): void
    {
        Contact::factory()->create([
            'category_id' => $this->category->id,
            'first_name' => 'エクスポート対象者',
            'gender' => 1,
        ]);

        $queryParams = [
            'keyword' => 'エクスポート',
            'gender' => 1,
            'category_id' => $this->category->id,
        ];

        $response = $this->actingAs($this->user)->get('/contacts/export?'.http_build_query($queryParams));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('エクスポート対象者', $response->streamedContent());
    }

    /** @test */
    public function test_guest_cannot_download_csv(): void
    {
        $response = $this->get('/contacts/export');

        $response->assertRedirect('/login');
    }
}
