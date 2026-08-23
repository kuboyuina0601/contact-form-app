<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;

class TagController extends Controller
{
    //タグ新規追加処理
    public function store(StoreTagRequest $request)
    {
        Tag::create($request->validated());

        return redirect()->route('admin.index');
    }
    //タグ編集画面表示
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }
    //タグ更新処理
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        return redirect()->route('admin.index');
    }
    //タグ削除処理
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('admin.index');
    }
}
