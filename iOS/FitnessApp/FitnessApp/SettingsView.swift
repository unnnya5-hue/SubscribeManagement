import SwiftUI
import SwiftData

struct SettingsView: View {
    @Environment(\.modelContext) private var modelContext
    @Query private var movements: [Movement]
    @Query private var workouts: [WorkoutSession]
    @Query private var bodyEntries: [BodyEntry]

    @State private var showingResetAlert = false

    var body: some View {
        NavigationStack {
            List {
                Section {
                    LabeledContent("保存方式", value: "iPhone内のみ")
                    LabeledContent("登録種目", value: "\(movements.count)")
                    LabeledContent("ワークアウト", value: "\(workouts.count)")
                    LabeledContent("身体データ", value: "\(bodyEntries.count)")
                } header: {
                    Text("データ")
                } footer: {
                    Text("このアプリはサーバーへ送信せず、端末内のSwiftDataに保存します。")
                }

                Section {
                    Label("RPEは主観的なきつさです。10に近いほど限界に近い状態です。", systemImage: "gauge.with.dots.needle.67percent")
                    Label("推定1RMは、1回だけ挙げられる最大重量の推定値です。", systemImage: "bolt.fill")
                    Label("ボリュームは、重量 x 回数で見たトレーニング量です。", systemImage: "chart.bar.fill")
                } header: {
                    Text("用語")
                }

                Section {
                    Button(role: .destructive) {
                        showingResetAlert = true
                    } label: {
                        Label("ローカルデータを削除", systemImage: "trash")
                    }
                }
            }
            .navigationTitle("設定")
            .alert("ローカルデータを削除しますか？", isPresented: $showingResetAlert) {
                Button("キャンセル", role: .cancel) {}
                Button("削除", role: .destructive) {
                    resetLocalData()
                }
            } message: {
                Text("ワークアウト、身体データ、登録種目をすべて削除します。この操作は元に戻せません。")
            }
        }
    }

    private func resetLocalData() {
        workouts.forEach(modelContext.delete)
        bodyEntries.forEach(modelContext.delete)
        movements.forEach(modelContext.delete)
        SeedData.movements.forEach { modelContext.insert($0) }
        try? modelContext.save()
    }
}
