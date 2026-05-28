# サブスク管理くん

サブスクリプションの支払い日、月額換算、年額換算、カテゴリ別の支出をまとめて管理する iOS アプリです。

SwiftUI / SwiftData を中心に、WidgetKit、StoreKit 2、Google Mobile Ads SDK を組み合わせています。データは端末内に保存し、ログインなしで使える構成です。

## 主な機能

- サブスクの登録、編集、削除
- 月額換算、年額換算、次回支払日の自動計算
- カテゴリ別の支出集計
- 次の支払い一覧と通知
- ホーム画面ウィジェット
- JSON エクスポート / インポート
- プレミアム購入による広告非表示と追加機能解放
- サポートページ、プライバシーポリシー、app-ads.txt の公開

## ディレクトリ構成

- `iOS/SubKun/` - iOSアプリ本体
- `docs/` - GitHub Pages用のサポートページ、プライバシーポリシー、提出準備資料
- `docs/app-store-assets/` - App Store Connect用画像
- `docs/app-store-screenshots/` - App Store Connect用スクリーンショット

## ローカルビルド

Xcodeで開く場合:

```bash
open iOS/SubKun/SubKun.xcodeproj
```

コマンドラインでビルド確認する場合:

```bash
xcodebuild \
  -project iOS/SubKun/SubKun.xcodeproj \
  -scheme SubKun \
  -destination 'generic/platform=iOS' \
  -derivedDataPath /private/tmp/SubKunDerivedData \
  CODE_SIGNING_ALLOWED=NO \
  build
```

## App Store提出関連

- 提出時の入力内容: `docs/subkun-app-store-submission.md`
- リリース作業の流れ: `docs/subkun-release-flow.md`
- StoreKitテスト手順: `docs/subkun-storekit-testing.md`

## AdMob / app-ads.txt

AdMob確認用の `app-ads.txt` は以下の内容です。

```txt
google.com, pub-6961277874965643, DIRECT, f08c47fec0942fa0
```

GitHub Pagesのプロジェクトページでは `docs/app-ads.txt` が `https://unnnya5-hue.github.io/SubscribeManagement/app-ads.txt` に公開されます。

AdMobはApp Storeに設定したDeveloper Websiteのホスト名直下も確認するため、ユーザーサイト側の `https://unnnya5-hue.github.io/app-ads.txt` にも同じ内容を配置します。

## 公開ページ

- サポート: https://unnnya5-hue.github.io/SubscribeManagement/support.html
- プライバシーポリシー: https://unnnya5-hue.github.io/SubscribeManagement/privacy.html
- トップページ: https://unnnya5-hue.github.io/SubscribeManagement/
