<?php

namespace Tests\Feature;

use App\Http\Controllers\ContactController;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = Category::create(['content' => '商品のお届けについて']);
        $this->tag = Tag::create(['name' => '質問']);
    }

    /** @test */
    public function test_guest_can_access_contact_form_page_with_categories_and_tags(): void
    {
        $response = $this->get(action([ContactController::class, 'index']));

        $response->assertStatus(200);
        $response->assertViewHas(['categories', 'tags']);
    }

    /** @test */
    public function test_contact_confirm_displays_inputs_when_validation_passes(): void
    {
        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区1-1',
            'building' => 'テストビル101',
            'category_id' => $this->category->id,
            'detail' => 'お問い合わせテスト本文',
        ];

        $response = $this->post(action([ContactController::class, 'confirm']), $data);

        $response->assertStatus(200);
        $response->assertSee('山田');
        $response->assertSee('yamada@example.com');
    }

    /** @test */
    public function test_contact_confirm_redirects_with_errors_when_validation_fails(): void
    {
        $data = [
            'first_name' => '',
            'email' => 'invalid-email',
        ];

        $response = $this->post(action([ContactController::class, 'confirm']), $data);

        $response->assertSessionHasErrors(['first_name', 'email']);
    }

    /** @test */
    public function test_contact_store_saves_data_and_tags_then_redirects_to_thanks(): void
    {
        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区1-1',
            'category_id' => $this->category->id,
            'detail' => 'お問い合わせテスト本文',
            'tag_ids' => [$this->tag->id],
        ];

        $response = $this->post(action([ContactController::class, 'store']), $data);

        $this->assertDatabaseHas('contacts', ['email' => 'yamada@example.com']);

        $contact = Contact::where('email', 'yamada@example.com')->first();

        // 中間テーブルへの保存確認
        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $this->tag->id,
        ]);

        $response->assertRedirect(route('contact.thanks'));
    }
}
