# Git 運用手順書

この手順書は、筋トレ管理アプリを安全に変更しながら履歴を残すためのものです。Git に詳しくなくても迷わないよう、日常的に使う操作だけに絞っています。

## まず覚える考え方

Git は「変更の履歴を保存する道具」です。基本の流れは次の3つです。

1. 変更する
2. 変更内容を確認する
3. コミットとして保存する

コミットは「ここまでの作業を保存したスナップショット」です。あとで「あの時点に何を変更したか」を確認できます。

## 現在の構成

- リポジトリ場所: `/Users/kairi/Documents/New project/FitnessApplication`
- メインブランチ: `main`
- 初期コミット: `dfe8e08 Initial fitness app`

`.env`、`vendor/`、`database/database.sqlite`、テストキャッシュは Git 管理から除外しています。パスワードやローカルDBを誤って保存しないためです。

## 日常の基本手順

作業前にアプリフォルダへ移動します。

```bash
cd "/Users/kairi/Documents/New project/FitnessApplication"
```

変更状態を確認します。

```bash
git status
```

変更したファイルの差分を確認します。

```bash
git diff
```

保存したい変更をステージします。

```bash
git add .
```

コミットします。

```bash
git commit -m "変更内容を短く書く"
```

例:

```bash
git commit -m "Add workout glossary"
```

## コミット前の確認

コミット前は、できれば次を実行します。

```bash
php artisan test
php artisan view:cache
```

`php artisan test` はアプリの基本的な動作確認です。`php artisan view:cache` は画面テンプレートに文法ミスがないかの確認にも使えます。

確認後、キャッシュを消したい場合は次を実行します。

```bash
php artisan optimize:clear
```

## よく使う確認コマンド

現在の変更状況を見る:

```bash
git status --short
```

直近のコミットを見る:

```bash
git log --oneline -5
```

あるファイルの変更内容を見る:

```bash
git diff ファイル名
```

例:

```bash
git diff routes/web.php
```

## ブランチ運用

普段の小さな修正は `main` に直接コミットしても構いません。大きめの機能追加をするときはブランチを作ると安全です。

ブランチを作って移動:

```bash
git switch -c feature/body-record-export
```

現在のブランチを確認:

```bash
git branch --show-current
```

`main` に戻る:

```bash
git switch main
```

ブランチの変更を `main` に取り込む:

```bash
git switch main
git merge feature/body-record-export
```

## コミットメッセージの書き方

短く「何をしたか」がわかる文にします。英語でも日本語でも大丈夫です。

良い例:

- `Add body record chart`
- `Fix workout set validation`
- `マシン削除時のチェックを追加`

避けたい例:

- `修正`
- `いろいろ変更`
- `test`

## 取り消しの基本

まだコミットしていない変更を確認する:

```bash
git status
git diff
```

ステージだけ取り消す:

```bash
git restore --staged ファイル名
```

ファイルの変更そのものを取り消す:

```bash
git restore ファイル名
```

注意: `git restore ファイル名` はそのファイルの未コミット変更を消します。必要な内容が残っていないか確認してから使ってください。

## 絶対に気をつけること

次のファイルは Git に入れません。

- `.env`
- `vendor/`
- `database/database.sqlite`
- `.phpunit.result.cache`
- `storage/logs` のログファイル

`.env` にはデータベースのパスワードやアプリキーが入ります。公開リポジトリに入ると危険です。

## GitHub などに公開する場合

まだリモートリポジトリを作っていない場合は、GitHub などで空のリポジトリを作ります。その後、表示された URL を使って次を実行します。

```bash
git remote add origin リポジトリURL
git push -u origin main
```

例:

```bash
git remote add origin git@github.com:username/fitness-application.git
git push -u origin main
```

公開前に、必ず次を確認してください。

```bash
git status --short
git log --oneline -3
git ls-files .env
```

`git ls-files .env` で何も表示されなければ、`.env` は Git に入っていません。

## 困ったとき

まずは次の2つを見ます。

```bash
git status
git log --oneline -5
```

状況がわからないときは、無理に取り消し系コマンドを使わず、`git status` の結果を確認してから進めます。特に `git reset --hard` は未保存の変更をまとめて消すので、通常運用では使わないでください。
