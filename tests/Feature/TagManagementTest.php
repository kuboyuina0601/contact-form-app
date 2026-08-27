<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function test_admin_can_crud_tags(): void
    {
        // 1. 作成
        $response = $this->actingAs($this->user)->post(route('admin.tags.store'), [
            'name' => '新規タグ',
        ]);
        $this->assertDatabaseHas('tags', ['name' => '新規タグ']);

        $newTag = Tag::where('name', '新規タグ')->first();

        // 2. 編集画面表示 (edit)
        $response = $this->actingAs($this->user)->get(route('admin.tags.edit', $newTag->id));
        $response->assertStatus(200);

        // 3. 更新 (update)
        $response = $this->actingAs($this->user)->put(route('admin.tags.update', $newTag->id), [
            'name' => '更新タグ',
        ]);
        $this->assertDatabaseHas('tags', ['id' => $newTag->id, 'name' => '更新タグ']);

        // 4. 削除 (destroy)
        $response = $this->actingAs($this->user)->delete(route('admin.tags.destroy', $newTag->id));
        $this->assertDatabaseMissing('tags', ['id' => $newTag->id]);
    }
}