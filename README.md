# 筋トレ管理アプリ

要件定義書「筋トレ管理アプリ 要件定義書 v1.0」をもとにした Laravel 11 製の Web アプリです。ロリポップ！ハイスピードプランでの運用を想定し、Node ビルド必須にせず、Blade + CDN Tailwind + Alpine.js + Chart.js で構成しています。

## 実装済み

- ユーザー登録、ログイン、ログアウト、プロフィール編集
- マシン管理、プリセット選択、マシンごとの種目管理
- メニュー管理、種目/セット/回数/重量設定、難易度別の重量提案
- 曜日別スケジュール、パターン有効化
- ワークアウト開始、セット記録、前回記録参照、推定1RM
- 身体データ記録、同日重複防止、90日グラフ
- 分析グラフ、月間カレンダー、ダッシュボード

## ローカル起動

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

ローカルで SQLite を使う場合は `.env` を次のように変更します。

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

## Git 運用

Git の基本運用は [Git 運用手順書](docs/git-operation.md) にまとめています。

## ロリポップ配備

ロリポップ！ハイスピードプランへの公開手順は [ロリポップ！配備手順書](docs/lolipop-deploy.md) にまとめています。

## ロリポップ！ハイスピードプラン配備メモ

1. ユーザー専用ページで対象ドメインの PHP を 8.3 または 8.4 に設定します。
2. MySQL データベースを作成し、`.env` の `DB_*` にホスト名、DB名、ユーザー名、パスワードを設定します。
3. サーバーへプロジェクトをアップロードします。
4. ドメインの公開フォルダは Laravel の `public` ディレクトリを向けます。
5. SSH が使える場合はサーバー側で以下を実行します。

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

SSH で Composer を使わない場合は、ローカルで `composer install --no-dev --optimize-autoloader` 済みの `vendor` を含めてアップロードします。

## 注意

- 現在のローカル PHP 8.5 では、Laravel 11 の vendor 側設定から `PDO::MYSQL_ATTR_SSL_CA` の deprecation が表示される場合があります。ロリポップ！公式情報上の PHP 8.3/8.4 運用では実害のない警告です。
- Tailwind は CDN 版を使っています。本格的に公開する段階で表示速度を詰めるなら、Vite ビルド構成に戻す余地があります。
- データ共有、CSVインポート/エクスポート、通知、Health連携は未実装の拡張候補です。
