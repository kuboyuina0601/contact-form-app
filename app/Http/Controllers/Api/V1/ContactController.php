<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    /**
     * AP01: お問い合わせ一覧取得
     */
    public function index(IndexContactRequest $request): AnonymousResourceCollection
    {
        $perPage = $request->input('per_page', 20);

        $contacts = Contact::with(['category', 'tags'])
            ->search($request->validated())
            ->latest()
            ->paginate($perPage);

        return ContactResource::collection($contacts);
    }

    /**
     * AP03: お問い合わせ登録
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $contact = Contact::create($validated);

        if (!empty($validated['tag_ids'])) {
            $contact->tags()->attach($validated['tag_ids']);
        }

        $contact->load(['category', 'tags']);

        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * AP02: お問い合わせ詳細取得
     */
    public function show(Contact $contact): ContactResource
    {
        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }

    /**
     * AP04: お問い合わせ更新
     */
    public function update(UpdateContactRequest $request, Contact $contact): ContactResource
    {
        $validated = $request->validated();

        $contact->update($validated);

        if (array_key_exists('tag_ids', $validated)) {
            $contact->tags()->sync($validated['tag_ids'] ?? []);
        }

        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }

    /**
     * AP05: お問い合わせ削除
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json(null, 204);
    }
}