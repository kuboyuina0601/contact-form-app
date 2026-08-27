# COACHTECH お問い合わせフォーム

## 概要
COACHTECH 確認テストで作成した成果物です。  
Laravelを用いて作成したお問い合わせ管理Webアプリケーションおよび公開APIサービスです。  
管理画面でのお問い合わせ・タグのCRUD操作に加え、外部連携用のAPIと単体・機能テストを実装しています。

## 開発環境URL
- ローカル開発環境: `http://localhost/`

## 作者
- 久保 唯菜

## 使用技術（環境・技術構成）
- OS: Linux (Docker環境)
- バックエンド: PHP 8.2 / Laravel 10.3
- データベース: MySQL 8.0 / SQLite (テスト環境)
- Webサーバー: Nginx
- フロントエンド: Vue.js, Tailwind CSS 3.4.0
- 開発ツール: Docker, Laravel Sail, phpMyAdmin, VS Code
- バージョン管理: Git, GitHub (Issue / PR)

## 学んだこと
- Issue駆動開発: 要件や作業を事前にGitHub Issueとして細分化し、ブランチ作成からPR作成や課題管理までの一連の開発フローを学びました。
- 中間テーブルの活用: お問い合わせとタグの多対多関係を実現するため、中間テーブルの作成やマイグレーション設定、Eloquentモデル間でのリレーション定義の方法を学びました。
- APIと例外処理: 適切なステータスコードの返却や、存在しないリソースへのアクセスに対するカスタム404レスポンスの実装方法を学びました。

## 詰まったポイントと解決方法
- フォームリクエストの単体テスト実行時、exists ルール検証でデータベースのテーブルが見つからずエラーが発生しました。
単体テストクラスに use RefreshDatabase; とシーダー実行を追加し、テスト実行時にSQLiteのDBが自動初期化・構築されるよう修正しました。

## 開発の工夫
- 全体の挙動を検証するFeatureテストと、バリデーションルール単体を検証するUnitテストを切り分けて作成し、テストの実行速度と保守性を高めました。

## 実装したAPIのエンドポイント一覧
| メソッド | パス | 概要 |
| :--- | :--- | :--- |
| `GET` | `/api/v1/contacts` | お問い合わせ一覧取得（検索・ページネーション対応） |
| `POST` | `/api/v1/contacts` | お問い合わせ新規登録 |
| `GET` | `/api/v1/contacts/{id}` | お問い合わせ詳細取得 |
| `PUT` / `PATCH` | `/api/v1/contacts/{id}` | お問い合わせ情報更新 |
| `DELETE` | `/api/v1/contacts/{id}` | お問い合わせ削除 |

## 環境構築手順
動作確認の際は、以下の手順でローカル環境の構築を行ってください。

1. Laravel 10.x プロジェクトの新規作成
```bash
docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$(pwd):/var/www/html" \
  -w /var/www/html \
  -e COMPOSER_CACHE_DIR=/tmp/composer_cache \
  laravelsail/php82-composer:latest \
  composer create-project laravel/laravel:^10.0 contact-form-app
```

2. プロジェクトディレクトリへの移動と Sail のインストール
```bash
 cd contact-form-app

  # Laravel Sail のインストール
  docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$(pwd):/var/www/html" \
  -w /var/www/html \
  -e COMPOSER_CACHE_DIR=/tmp/composer_cache \
  laravelsail/php82-composer:latest \
  composer require laravel/sail --dev

  # MySQL構成でSail設定をパブリッシュ
  docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$(pwd):/var/www/html" \
  -w /var/www/html \
  -e COMPOSER_CACHE_DIR=/tmp/composer_cache \
  laravelsail/php82-composer:latest \
  php artisan sail:install --with=mysql

  ※Apple Silicon（M1/M2/M3）Macをお使いの場合:
  compose.yaml 内の mysql サービスに以下を追加してください。
  mysql:
  image: 'mysql/mysql-server:8.0'
  platform: 'linux/amd64'
```
3. 環境変数（.env）の調整
```bash
  .env ファイルを開き、データベース接続情報が以下になっているか確認・修正します。
  DB_CONNECTION=mysql
  DB_HOST=mysql
  DB_PORT=3306
  DB_DATABASE=laravel
  DB_USERNAME=sail
  DB_PASSWORD=password
```
4. フロントエンド環境のセットアップ（Tailwind CSS & Alpine.js）
```bash
  # Sailコンテナの起動
  ./vendor/bin/sail up -d

  # 依存パッケージのインストール
  sail npm install
　sail npm install -D tailwindcss@^3.4.0 postcss autoprefixer
　sail npm install alpinejs

　# Tailwind設定ファイルの生成
　sail npx tailwindcss init -p

　tailwind.config.js の content 配列に以下を設定します。
　content: [
  "./resources/**/*.blade.php",
  "./resources/**/*.js",
  "./resources/**/*.vue",
  ],
```

5. phpMyAdmin の追加設定
```bash
　compose.yaml の services セクション内に以下を追加します。
　phpmyadmin:
  　image: 'phpmyadmin:latest'
  　ports:
    　- '${FORWARD_PHPMYADMIN_PORT:-8080}:80'
  　environment:
    　PMA_HOST: mysql
    　PMA_USER: '${DB_USERNAME}'
    　PMA_PASSWORD: '${DB_PASSWORD}'
  　networks:
    　- sail
  　depends_on:
    　- mysql
```
6. アプリキー生成と DB マイグレーション
```bash
　sail artisan key:generate
　sail artisan migrate:fresh --seed
```
7. 動作確認・サーバー起動
```bash
　# Vite開発サーバーの起動
　sail npm run dev
　# テストの実行
　sail artisan test
```
　Webサイト: http://localhost/
　phpMyAdmin: http://localhost:8080/

## データベース設計（ER図）
```mermaid
erDiagram
    categories ||--o{ contacts : "1つのカテゴリは複数のお問い合わせを持つ"
    contacts ||--o{ contact_tag : "1つのお問い合わせは複数のタグ中間レコードを持つ"
    tags ||--o{ contact_tag : "1つのタグは複数のタグ中間レコードを持つ"

    categories {
        bigint id PK "カテゴリID"
        string content "カテゴリ内容"
    }
    contacts {
        bigint id PK "お問い合わせID"
        bigint category_id FK "カテゴリID"
        string first_name "姓"
        string last_name "名"
        integer gender "性別"
        string email "メールアドレス"
        string tel "電話番号"
        string address "住所"
        string building "建物名"
        string detail "お問い合わせ内容"
    }
    tags {
        bigint id PK "タグID"
        string name "タグ名"
    }
    contact_tag {
        bigint id PK "ID"
        bigint contact_id FK "お問い合わせID"
        bigint tag_id FK "タグID"
    }