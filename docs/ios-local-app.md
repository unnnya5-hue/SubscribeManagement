# iOS ローカル保存アプリ

Webアプリとは別に、Xcode / SwiftUI / SwiftData で作る iPhone/iPad アプリです。データはサーバーへ送らず、端末内に保存します。

## プロジェクト

```text
iOS/FitnessApp/FitnessApp.xcodeproj
```

Xcodeで開く場合:

```bash
open "iOS/FitnessApp/FitnessApp.xcodeproj"
```

## 採用技術

- SwiftUI: 画面
- SwiftData: ローカル保存
- Charts: 身体データグラフ
- TabView / NavigationStack: iOSらしい画面遷移

## 実装済みの最小版

- ホーム
  - 最新体重
  - 7日ボリューム
  - 累計ワークアウト
  - 登録種目
  - 今日の候補
  - 最近のワークアウト
- 記録
  - 種目選択
  - 重量、回数、RPE、休憩秒の入力
  - セット追加
  - ワークアウト保存
  - 履歴表示
- 身体
  - 体重、体脂肪率、メモの保存
  - 90日推移グラフ
  - 履歴表示
- 種目
  - 種目追加
  - 部位、標準重量、セット数、回数の設定
  - 削除
- 設定
  - ローカル保存の説明
  - データ件数
  - 用語説明
  - ローカルデータ削除

## データ保存

保存対象:

- `Movement`
- `WorkoutSession`
- `TrainingSet`
- `BodyEntry`

SwiftData の永続化ストアに保存されます。サーバー、MySQL、Laravel API は使いません。

## ビルド確認

この環境では `xcodebuild` のプロジェクトビルド時に Xcode コンポーネント認識の問題で、次のエラーが出ました。

```text
iOS 26.4 is not installed. Please download and install the platform from Xcode > Settings > Components.
```

ただし、iOS Simulator SDK を使った Swift ソースの型チェックは成功しています。

```bash
xcrun --sdk iphonesimulator swiftc -target arm64-apple-ios18.0-simulator -typecheck iOS/FitnessApp/FitnessApp/*.swift
```

Xcodeで開いたあと、必要に応じて `Settings > Components` から iOS Platform / Simulator を追加してください。

## 次にやること

次の順番で育てるのがおすすめです。

1. Xcodeでシミュレータ起動確認
2. ワークアウト記録画面の入力体験を磨く
3. 種目ごとの進捗グラフを追加
4. カレンダー表示を追加
5. iCloud同期を検討
6. HealthKit連携を検討
