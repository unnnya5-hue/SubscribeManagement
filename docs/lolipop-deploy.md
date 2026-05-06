# ロリポップ！配備手順書

この手順書は、ローカルで構築中の筋トレ管理アプリをロリポップ！ハイスピードプランへ公開するためのものです。

## 前提

ハイスピードプランでは、MySQL、SSH、LiteSpeed が利用できます。Laravel は `public` ディレクトリを公開フォルダにする構成が基本です。

参考:

- ロリポップ！ MySQL: https://support.lolipop.jp/hc/ja/articles/360049130733
- ロリポップ！ SSH: https://lolipop.jp/manual/user/ssh/
- ロリポップ！ 公開フォルダ: https://support.lolipop.jp/hc/ja/articles/360048391094
- ロリポップ！ サーバー仕様: https://lolipop.jp/service/server-spec/

## 全体の流れ

1. ロリポップ側で PHP、MySQL、SSH、独自SSL を準備する
2. 独自ドメインまたはサブドメインの公開フォルダを Laravel の `public` に向ける
3. アプリをサーバーへアップロードする
4. サーバー上で `.env` を作成する
5. Composer、マイグレーション、キャッシュ作成を実行する
6. ブラウザで表示確認する

## 1. ロリポップ側の準備

ユーザー専用ページで次を設定します。

- PHP: 8.3 または 8.4
- MySQL: 新規作成
- SSH: 有効化
- 独自SSL: 有効化

MySQL 作成後、次の情報を控えます。

- データベース名
- ユーザー名
- パスワード
- データベースホスト名

## 2. 公開フォルダを決める

おすすめ構成:

```text
fitness-app/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          ← ドメインの公開フォルダにする
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
└── artisan
```

独自ドメインまたはサブドメインの公開フォルダは、可能であれば次のように設定します。

```text
fitness-app/public
```

これにより、`app`、`config`、`.env` などの Laravel 本体ファイルが直接Web公開されにくくなります。

もし管理画面で `fitness-app/public` のような階層指定ができない場合は、先に止まってください。`public` の中身だけを公開フォルダへ出す代替構成に変更できますが、`public/index.php` のパス修正が必要になります。

## 3. ローカル側の確認

ローカルで次を実行します。

```bash
cd "/Users/kairi/Documents/New project/FitnessApplication"
git status --short
php artisan test
php artisan view:cache
php artisan optimize:clear
```

`git status --short` に意図しない変更が出ていないか確認します。

## 4. アップロード方法

SSH/SFTP を使う方法がおすすめです。ロリポップの SSH 情報はユーザー専用ページで確認します。

アップロード対象:

- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `public/`
- `resources/`
- `routes/`
- `storage/`
- `tests/` は本番不要ですが、置いても大きな問題はありません
- `.env.example`
- `artisan`
- `composer.json`
- `composer.lock`
- `README.md`
- `docs/`

アップロードしないもの:

- `.env`
- `.git/`
- `vendor/` （サーバーで Composer を実行する場合）
- `node_modules/`
- `database/database.sqlite`
- `.phpunit.result.cache`

SFTPソフトを使う場合は、ローカルのプロジェクト内容をサーバーの `fitness-app/` フォルダへアップロードします。

`rsync` が使える場合の例:

```bash
rsync -avz --delete \
  --exclude=".git" \
  --exclude=".env" \
  --exclude="vendor" \
  --exclude="node_modules" \
  --exclude="database/database.sqlite" \
  --exclude=".phpunit.result.cache" \
  -e "ssh -p SSHポート番号" \
  "/Users/kairi/Documents/New project/FitnessApplication/" \
  "SSHユーザー名@SSHホスト名:~/fitness-app/"
```

`SSHポート番号`、`SSHユーザー名`、`SSHホスト名` はロリポップの SSH 設定画面の値に置き換えます。

## 5. サーバー上で `.env` を作る

SSHでサーバーにログインし、アプリフォルダへ移動します。

```bash
cd ~/fitness-app
cp .env.example .env
```

`.env` を編集します。

```bash
vi .env
```

最低限、次を本番用に変更します。

```env
APP_NAME="筋トレ管理"
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Tokyo
APP_URL=https://あなたのドメイン

APP_LOCALE=ja
APP_FALLBACK_LOCALE=ja
APP_FAKER_LOCALE=ja_JP

DB_CONNECTION=mysql
DB_HOST=ロリポップのDBホスト名
DB_PORT=3306
DB_DATABASE=ロリポップのDB名
DB_USERNAME=ロリポップのDBユーザー名
DB_PASSWORD=ロリポップのDBパスワード

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

## 6. サーバー上で初期設定を実行

サーバー上で Composer が使える場合:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan migrate --seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

権限エラーが出る場合は、次を試します。

```bash
chmod -R 775 storage bootstrap/cache
```

## 7. Composer がサーバーで使えない場合

ローカルで本番用 vendor を作ってから、`vendor/` もアップロードします。

```bash
cd "/Users/kairi/Documents/New project/FitnessApplication"
composer install --no-dev --optimize-autoloader
```

その後、SFTP で `vendor/` もサーバーへアップロードします。

サーバー側では次を実行します。

```bash
cd ~/fitness-app
php artisan key:generate --force
php artisan migrate --seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 8. 表示確認

ブラウザで次を確認します。

- `https://あなたのドメイン/login`
- ユーザー登録
- ログイン
- マシン登録
- 身体データ登録

問題なければ公開完了です。

## 9. よくあるエラー

### 500 エラー

確認すること:

- `.env` が存在するか
- `APP_KEY` が入っているか
- `APP_DEBUG=false` にしているか
- `storage` と `bootstrap/cache` に書き込み権限があるか
- `vendor/` が存在するか

一時的に原因を確認する場合だけ、`.env` を次のように変更します。

```env
APP_DEBUG=true
```

原因確認後は必ず `false` に戻します。

### データベース接続エラー

確認すること:

- `DB_HOST`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- MySQL が作成済みか

### 404 エラー

確認すること:

- ドメインの公開フォルダが `fitness-app/public` を向いているか
- `public/index.php` が存在するか
- `.htaccess` がアップロードされているか

### CSS が崩れる

このアプリは Tailwind / Alpine.js / Chart.js を CDN から読み込んでいます。ブラウザが外部 CDN にアクセスできるか確認してください。

## 10. 更新時の流れ

ローカルで修正したあと:

```bash
git status --short
php artisan test
git add .
git commit -m "変更内容"
```

サーバーへアップロード後:

```bash
cd ~/fitness-app
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

画面だけの変更なら `composer install` と `migrate` は不要なこともありますが、迷ったら実行して大丈夫です。
