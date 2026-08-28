<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateTagRequestTest extends TestCase
{
    use RefreshDatabase;

    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->tag = Tag::where('name', '質問')->first();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    private function createRequest(string $url, string $method, array $data, int $tagId): UpdateTagRequest
    {
        $request = UpdateTagRequest::create($url, $method, $data);

        $route = new Route($method, 'admin/tags/{tag}', []);
        $route->bind($request);
        $route->setParameter('tag', $tagId);

        $request->setRouteResolver(fn () => $route);
        $request->setContainer(app())->setRedirector(app('redirect'));

        return $request;
    }

    /** @test */
    public function test_tag_update_validation_allows_same_name(): void
    {
        $data = ['name' => '質問'];

        $request = $this->createRequest("/admin/tags/{$this->tag->id}", 'PUT', $data, $this->tag->id);

        // 例外が発生しなければバリデーション通過とみなす
        $this->expectNotToPerformAssertions();
        $request->validateResolved();
    }

    /** @test */
    public function test_tag_update_validation_fails_when_other_tag_name_exists(): void
    {
        $data = ['name' => '要望'];

        $request = $this->createRequest("/admin/tags/{$this->tag->id}", 'PUT', $data, $this->tag->id);

        try {
            $request->validateResolved();
            $this->fail('バリデーションが発生すべきです');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('name', $e->errors());
        }
    }

    /** @test */
    public function test_tag_update_validation_passes_at_max_50_chars(): void
    {
        $data = ['name' => str_repeat('a', 50)];

        $request = $this->createRequest("/admin/tags/{$this->tag->id}", 'PUT', $data, $this->tag->id);

        $this->expectNotToPerformAssertions();
        $request->validateResolved();
    }

    /** @test */
    public function test_tag_update_validation_fails_exceeding_max_50_chars(): void
    {
        $data = ['name' => str_repeat('a', 51)];

        $request = $this->createRequest("/admin/tags/{$this->tag->id}", 'PUT', $data, $this->tag->id);

        try {
            $request->validateResolved();
            $this->fail('51文字以上はバリデーションエラーが発生すべきです');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('name', $e->errors());
        }
    }
}
