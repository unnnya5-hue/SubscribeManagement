# サブスク管理くん リリースまでの流れ

作成日: 2026-05-24

この資料は、`iOS/SubKun` を App Store へ出すまでの作業順を、現在の実装状態に合わせて整理したものです。既存の汎用資料は `docs/ios-app-release-codex-guide.md` にあります。このファイルは「サブスク管理くん専用」の進行表です。

## 現在の実装状態

| 項目 | 現在値 / 状態 |
| --- | --- |
| アプリ名 | サブスク管理くん |
| Xcode project | `iOS/SubKun/SubKun.xcodeproj` |
| Bundle ID | `com.kairi.SubKun` |
| Widget Bundle ID | `com.kairi.SubKun.WidgetExtension` |
| Version / Build | `1.0` / `1` |
| Deployment Target | iOS 17.0+ |
| 課金Product ID | `com.kairi.SubKun.premium` |
| 課金の現実装 | 買い切りプレミアム向けの StoreKit 2 実装 |
| 広告SDK | Google Mobile Ads SDK 12.14.0 |
| 広告ID | 現在は Google のテストID |
| AdMob App ID | 現在はテスト用 `ca-app-pub-3940256099942544~1458002511` |
| App Group | `group.com.kairi.SubKun` |
| iCloud/CloudKit | Entitlementsあり。ただし SwiftData 永続化はCloudKit無効化済み |
| Debug広告確認 | 設定タブ > 広告テスト |

## 最短ルート

1. リリース方針を確定する
2. Apple Developer / App Store Connect のアプリ枠を作る
3. 本番用の広告IDと課金商品を作る
4. アプリ側の提出前TODOを片付ける
5. App Store掲載情報とプライバシー回答を作る
6. 実機 / TestFlightで確認する
7. ArchiveしてApp Store Connectへアップロードする
8. ビルドを選び、審査へ提出する
9. 承認後に手動または自動でリリースする

## 1. リリース方針を確定する

まずここを決めます。

- 初回リリースは日本だけか、全世界配信か
- プレミアムは買い切りか、月額/年額サブスクか
- iCloud同期を初回から正式機能にするか、後続アップデートに回すか
- 広告はバナー + インタースティシャルで進めるか
- App Open広告は初回では使うか、後続に回すか

現状のコードは `com.kairi.SubKun.premium` を買い切りのプレミアムとして扱っています。月額/年額サブスクにしたい場合は、App Store Connectの商品種別だけでなく、アプリ側の購入・復元・有効期限判定も調整します。

## 2. Apple Developer / App Store Connect

Apple Developer側で以下を確認します。

- App ID: `com.kairi.SubKun`
- Extension App ID: `com.kairi.SubKun.WidgetExtension`
- App Groups: `group.com.kairi.SubKun`
- iCloud Containers: `iCloud.com.kairi.SubKun`

初回リリースでiCloud同期を出さないなら、iCloud Entitlementと設定画面の「iCloud同期」を外す判断もできます。残す場合はApple Developer側でCloudKitコンテナを正しく有効化します。

App Store Connectでは新規アプリを作成します。

- プラットフォーム: iOS
- 名前: サブスク管理くん
- プライマリ言語: 日本語
- Bundle ID: `com.kairi.SubKun`
- SKU: 例 `subkun-ios`
- 価格: 無料
- カテゴリ: ファイナンス、ユーティリティ、ライフスタイルあたりから最終判断

## 3. 課金設定

App Store Connectで In-App Purchase を作成します。

買い切りプレミアムで進める場合:

- 種別: Non-Consumable
- Product ID: `com.kairi.SubKun.premium`
- Reference Name: `SubKun Premium`
- 表示名: `プレミアム`
- 説明: `広告非表示、データ入出力、Largeウィジェットを有効化します。`
- 価格: 初回は低めに設定して検証しやすくする

月額/年額サブスクへ変更する場合:

- Auto-Renewable Subscriptionとして商品を作る
- Product IDを別にする
- アプリ側で有効期限、更新、解約後の状態を扱う
- ストア文言を「買い切り」から「サブスクリプション」に変更する

## 4. AdMob設定

現在の広告IDはすべてテスト用です。公開前に必ず本番IDへ差し替えます。

現在のテストID:

- App ID: `ca-app-pub-3940256099942544~1458002511`
- Banner: `ca-app-pub-3940256099942544/2934735716`
- App Open: `ca-app-pub-3940256099942544/5575463023`
- Interstitial: `ca-app-pub-3940256099942544/4411468910`

公開前TODO:

- AdMobでiOSアプリを登録
- 本番App IDを `SubKun/Info.plist` の `GADApplicationIdentifier` に設定
- 本番広告ユニットIDを `AppSettings` 初期値へ設定
- `SKAdNetworkItems` を `Info.plist` に追加
- 必要なら `app-ads.txt` を公開
- 実機をテストデバイス登録して、本番ID + Test modeで確認
- Debug専用の広告テスト欄がReleaseに出ないことをArchiveで確認

テストIDのまま公開しないでください。Googleのデモ広告ユニットは開発用で、公開前に自分の広告ユニットIDへ置き換える必要があります。

## 5. アプリ側の提出前TODO

優先度高:

- App Iconを追加する
- `SettingsView` のサポートURL / プライバシーポリシーURLをGitHub Pagesの本番URLにする
- 本番 AdMob IDへ差し替える
- `Info.plist` に `SKAdNetworkItems` を追加する
- 課金商品をApp Store Connectに作成し、Sandboxで購入/復元を確認する
- App Store用スクリーンショットを作る
- Appプライバシー回答案を確定する
- 実機で通知、購入、広告、ウィジェットを確認する

優先度中:

- iCloud同期を正式リリースするか、初回は非表示にするか決める
- `Version` / `Build` の運用ルールを決める
- 初回起動時のサンプルデータを残すか決める
- ダークモード、文字サイズ大、オフライン状態で見た目を確認する

## 6. サポートURL / プライバシーポリシーURL

App Store提出には、最低限以下が必要です。

- サポートURL
- プライバシーポリシーURL
- 問い合わせメール

このリポジトリでは以下をGitHub Pages向けに用意しています。

- サポートURL: `https://unnnya5-hue.github.io/SubscribeManagement/support.html`
- プライバシーポリシーURL: `https://unnnya5-hue.github.io/SubscribeManagement/privacy.html`
- トップページ: `https://unnnya5-hue.github.io/SubscribeManagement/`

GitHub Pages、独自ドメイン、既存サイトのどれでも構いません。AdMobを使う場合はプライバシーポリシーに広告SDK、識別子、利用状況データ、第三者提供の考え方を明記します。

## 7. App Store Connect入力物

作るもの:

- アプリ説明文
- サブタイトル
- キーワード
- プロモーション用テキスト
- スクリーンショット
- 年齢レーティング回答
- Appプライバシー回答
- 輸出コンプライアンス回答
- App Review連絡先
- App Reviewメモ

スクリーンショット候補:

1. ホーム: 固定費サマリと次の支払い
2. 登録画面: サブスクを簡単に追加
3. 集計画面: カテゴリ別の支出把握
4. 詳細画面: 支払い記録や解約リンク
5. プレミアム: 広告非表示とデータ入出力

## 8. Appプライバシー回答の方向性

AdMobを入れるため、アプリ本体が自前サーバーを持っていなくても「サードパーティSDKによるデータ収集」を前提に回答します。

候補になりやすいデータ:

- ID: デバイスID
- 使用状況データ: 製品の操作、広告データ
- 診断: クラッシュデータ、パフォーマンスデータ
- 位置情報: おおよその場所

用途候補:

- サードパーティ広告
- アナリティクス
- アプリの機能

トラッキング:

- 現在の実装にはATTリクエストがあります
- パーソナライズ広告やIDFA利用を行うなら、App Store Connectの回答とAdMob設定を合わせる
- 初回リリースで非パーソナライズ寄りにするなら、その方針に合わせて実装と回答を整理する

## 9. TestFlight確認

Archiveをアップロードしたら、まずTestFlightで確認します。

見るポイント:

- 初回起動からオンボーディング完了まで
- サブスク登録、編集、削除
- 集計表示
- プレミアム購入、復元
- 非プレミアム時の広告表示
- プレミアム時の広告非表示
- 通知許可と通知設定
- ウィジェット表示
- オフライン状態での起動
- 広告が読み込めない場合でも画面が止まらないこと

## 10. Archive / Upload

Xcodeで行います。

1. `Any iOS Device (arm64)` を選択
2. `Product > Clean Build Folder`
3. `Product > Archive`
4. OrganizerでArchiveを選択
5. `Distribute App`
6. `App Store Connect`
7. `Upload`
8. App Store Connectで処理完了を待つ
9. バージョン画面でビルドを選択

アップロード後、App Store Connectにビルドが出るまで時間がかかることがあります。

## 11. 審査提出

提出前に確認します。

- App Store掲載情報が埋まっている
- スクリーンショットが規格に合っている
- Appプライバシー回答が完了している
- 年齢レーティングが完了している
- 輸出コンプライアンス回答が完了している
- In-App Purchaseも審査対象に含めている
- 審査用メモに広告、プレミアム、ログイン不要を簡潔に記載している

審査用メモ例:

```text
ログイン不要で利用できます。
プレミアム購入は広告非表示、データ入出力、Largeウィジェットの有効化に使用します。
広告はGoogle Mobile Ads SDKを使用しています。
```

## 12. リリース設定

初回は「手動リリース」を推奨します。審査通過後に最後の確認ができます。

- 自動リリース: 承認後に自動で公開
- 手動リリース: 承認後、App Store Connectで自分が公開ボタンを押す

初回は手動リリースにして、公開前にサポートURL、プライバシーポリシー、AdMob、課金商品の状態をもう一度見ます。

## 13. 公開後

公開後に見るもの:

- App Storeページが開けるか
- アプリをApp Storeからインストールできるか
- 本番広告が配信されるか
- IAPが本番環境で表示されるか
- AdMobポリシーセンターに警告がないか
- app-ads.txt警告が出ていないか
- クラッシュや低評価レビューが出ていないか

公開直後は広告やIAPの反映に時間がかかることがあります。表示されない場合でも、まずはクラッシュしていないか、IDが本番用になっているか、AdMob/App Store Connect側でアプリが承認済みかを確認します。

## この資料を見ながら次にやる作業

最初に進めるなら、この順番がおすすめです。

1. App Icon作成
2. サポートページ / プライバシーポリシー作成
3. App Store Connect入力文案作成
4. Appプライバシー回答案作成
5. AdMob本番ID作成後の差し替え
6. StoreKit Sandbox購入テスト
7. TestFlight用Archive

## 公式リファレンス

- Apple: Add a new app  
  https://developer.apple.com/help/app-store-connect/create-an-app-record/add-a-new-app
- Apple: Upload builds  
  https://developer.apple.com/help/app-store-connect/manage-builds/upload-builds
- Apple: TestFlight overview  
  https://developer.apple.com/help/app-store-connect/test-a-beta-version/testflight-overview/
- Apple: Manage app privacy  
  https://developer.apple.com/help/app-store-connect/manage-app-information/manage-app-privacy
- Apple: Screenshot specifications  
  https://developer.apple.com/help/app-store-connect/reference/app-information/screenshot-specifications
- Apple: Create consumable or non-consumable In-App Purchases  
  https://developer.apple.com/help/app-store-connect/manage-in-app-purchases/create-consumable-or-non-consumable-in-app-purchases
- Apple: Select an App Store version release option  
  https://developer.apple.com/help/app-store-connect/manage-your-apps-availability/select-an-app-store-version-release-option
- Google: Set up Google Mobile Ads SDK for iOS  
  https://developers.google.com/admob/ios/quick-start
- Google: Enable test ads for iOS  
  https://developers.google.com/admob/ios/test-ads
