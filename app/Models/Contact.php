<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'first_name',
        'last_name',
        'gender',
        'email',
        'tel',
        'address',
        'building',
        'detail',
    ];

    // Bladeで $contact->first_name が呼ばれた時に「姓 名」の順で返す
    public function getFirstNameAttribute()
    {
        $lastName = $this->attributes['last_name'] ?? '';
        $firstName = $this->attributes['first_name'] ?? '';

        return $lastName.' '.$firstName;
    }

    // Bladeで $contact->last_name が呼ばれた時に空文字を返す（重複表示を防ぐ）
    public function getLastNameAttribute()
    {
        return '';
    }

    public function scopeSearch(Builder $query, array $params): Builder
    {
        // キーワード検索（姓・名・メール）
        if (! empty($params['keyword'])) {
            $keyword = $params['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        // 性別検索（0以外）
        if (! empty($params['gender']) && $params['gender'] !== '0') {
            $query->where('gender', $params['gender']);
        }

        // カテゴリ検索
        if (! empty($params['category_id'])) {
            $query->where('category_id', $params['category_id']);
        }

        // 日付検索
        if (! empty($params['date'])) {
            $query->whereDate('created_at', $params['date']);
        }

        return $query;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
