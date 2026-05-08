# Xcode 実装方針

このアプリを Xcode で実装する場合、現在の Laravel Web アプリをそのまま移植するのではなく、iPhone/iPad 向けの SwiftUI アプリとして作り、Laravel はデータ保存用の API バックエンドとして使う構成がおすすめです。

## 結論

おすすめ構成:

```text
iPhone / iPad アプリ
  └─ Xcode + SwiftUI
       ├─ 画面表示
       ├─ 入力フォーム
       ├─ グラフ
       └─ ログイン状態管理

ロリポップサーバー
  └─ Laravel
       ├─ 認証API
       ├─ マシンAPI
       ├─ メニューAPI
       ├─ ワークアウトAPI
       ├─ 身体データAPI
       └─ MySQL
```

この形にすると、Web版とiOS版で同じデータを使えます。将来、Web版を残したままiPhoneアプリを追加できます。

## 選択肢

### 1. SwiftUI ネイティブアプリ + Laravel API

本命の方法です。

メリット:

- iPhoneアプリらしい操作感にできる
- Web版とデータを共有できる
- 将来の通知、Health連携、ウィジェットなどに広げやすい

デメリット:

- Laravel側に API を追加する必要がある
- ログイン方式をモバイル向けに整える必要がある

### 2. SwiftUI + SwiftData の完全ローカルアプリ

サーバーを使わず、iPhone内だけにデータを保存する方法です。

メリット:

- サーバー不要
- オフラインでも使いやすい
- 実装が比較的シンプル

デメリット:

- Web版とデータ共有できない
- 機種変更やバックアップを別途考える必要がある

### 3. WebView アプリ

Xcodeでアプリを作り、中身はロリポップ上のWebアプリを表示する方法です。

メリット:

- 一番早い
- Web版をほぼそのまま使える

デメリット:

- ネイティブアプリらしさは弱い
- App Store 審査では内容次第で不利になる場合がある
- 通知やHealth連携などの拡張はやりにくい

## 推奨ロードマップ

### Phase 1: Laravel を API 対応にする

追加するもの:

- `/api/login`
- `/api/logout`
- `/api/user`
- `/api/machines`
- `/api/menus`
- `/api/schedules`
- `/api/workouts`
- `/api/body-records`
- `/api/charts/*`

認証はモバイルアプリ向けにトークン方式へ寄せます。Laravel Sanctum を使うのが自然です。

### Phase 2: Xcode プロジェクトを作る

作成するもの:

- `FitnessApp.xcodeproj`
- SwiftUI アプリ本体
- API通信クラス
- ログイントークン保存
- 画面ごとの ViewModel

推奨フォルダ:

```text
iOS/FitnessApp/
├── FitnessApp.xcodeproj
├── FitnessApp/
│   ├── App/
│   ├── Models/
│   ├── Services/
│   ├── ViewModels/
│   ├── Views/
│   └── Utilities/
└── FitnessAppTests/
```

### Phase 3: 主要画面を順番に実装する

優先順:

1. ログイン
2. ダッシュボード
3. ワークアウト記録
4. 身体データ
5. マシン管理
6. メニュー管理
7. スケジュール
8. 分析グラフ
9. カレンダー

最初から全部作るより、ログインからワークアウト記録までを先に通すのが安全です。

## SwiftUI 側の画面構成

おすすめのタブ構成:

```text
ホーム
記録
身体
メニュー
設定
```

`マシン`、`スケジュール`、`分析`、`カレンダー` は、ホームやメニューから遷移する形でも十分です。

## iOS アプリで使う主な技術

- SwiftUI: 画面作成
- URLSession: Laravel API との通信
- Charts: グラフ表示
- Keychain: ログイントークン保存
- Observation / ObservableObject: 画面状態管理
- Codable: APIレスポンス変換

## API のレスポンス例

身体データ:

```json
{
  "id": 1,
  "recorded_date": "2026-05-09",
  "weight_kg": 65.2,
  "body_fat_pct": 18.4,
  "notes": "朝測定"
}
```

ワークアウト:

```json
{
  "id": 1,
  "performed_on": "2026-05-09",
  "menu": {
    "id": 3,
    "name": "胸の日"
  },
  "sets": [
    {
      "id": 10,
      "usage_name": "ベンチプレス",
      "set_number": 1,
      "weight_kg": 60,
      "reps": 8,
      "rpe": 8.5,
      "estimated_1rm": 76
    }
  ]
}
```

## 最初に作る最小版

Xcode版の最小版は次の範囲に絞るのがおすすめです。

- ログイン
- ダッシュボード
- 今日のメニュー表示
- ワークアウト記録
- 身体データ登録

この範囲が動けば、アプリとして毎日使える土台になります。

## 実装時の注意

- `.env` の情報を iOS アプリに入れない
- DBへ直接接続しない
- iOS アプリは必ず Laravel API 経由でデータを扱う
- API URL は開発用と本番用で切り替えられるようにする
- ログイントークンは UserDefaults ではなく Keychain に保存する

## 次にやること

次のどちらかを決めます。

1. Web版を残しながら、Xcode版を追加する
2. Web版は参考にして、Xcode版だけを新規に作る

おすすめは 1 です。既に Laravel 側にDB設計と画面があるので、それを API 化して iOS アプリから使う方が、あとでWeb版とiOS版を両方育てられます。
