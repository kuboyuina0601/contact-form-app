<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $tags = Tag::all();

        $contacts = Contact::with(['category', 'tags'])
            ->search($request->all())
            ->paginate(7);

        return view('admin.index', compact('contacts', 'categories', 'tags'));
    }

    public function show(Contact $contact)
    {
        $contact->load(['category', 'tags']);

        return view('admin.show', compact('contact'));
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.index');
    }

    //エクスポート
    public function export(Request $request)
    {
        // 検索条件に一致するデータを全件取得
        $contacts = Contact::with(['category', 'tags'])
            ->search($request->all())
            ->get();

        $genderLabels = [1 => '男性', 2 => '女性', 3 => 'その他'];

        // レスポンスヘッダーの設定
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="contacts_' . date('Ymd_His') . '.csv"',
        ];

        // ストリーム出力でCSVを生成
        $callback = function () use ($contacts, $genderLabels) {
            $stream = fopen('php://output', 'w');

            // Excel等の文字化け防止用BOM付与
            fwrite($stream, "\xEF\xBB\xBF");

            // ヘッダー行の書き込み
            fputcsv($stream, ['お名前', '性別', 'メールアドレス', 'お問い合わせの種類', '詳細']);

            // データ行の書き込み
            foreach ($contacts as $contact) {
                fputcsv($stream, [
                    $contact->first_name . ' ' . $contact->last_name,
                    $genderLabels[$contact->gender] ?? '',
                    $contact->email,
                    $contact->category->content ?? '',
                    $contact->detail,
                ]);
            }

            fclose($stream);
        };

        return response()->stream($callback, 200, $headers);
    }
}