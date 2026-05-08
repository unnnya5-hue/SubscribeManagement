import SwiftUI
import SwiftData
import Charts

struct BodyLogView: View {
    @Environment(\.modelContext) private var modelContext
    @Query(sort: \BodyEntry.date, order: .reverse) private var entries: [BodyEntry]

    @State private var date = Date()
    @State private var weight = 65.0
    @State private var bodyFat = 18.0
    @State private var usesBodyFat = true
    @State private var note = ""

    private var chartEntries: [BodyEntry] {
        Array(entries.prefix(90)).reversed()
    }

    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 20) {
                    summary
                    chart
                    form
                    history
                }
                .padding()
            }
            .background(Color(.systemGroupedBackground))
            .navigationTitle("身体")
        }
    }

    private var summary: some View {
        LazyVGrid(columns: [GridItem(.flexible()), GridItem(.flexible())], spacing: 12) {
            MetricCard(title: "最新体重", value: entries.first?.weight.kgText ?? "--", footnote: "直近の記録", symbol: "scalemass.fill", tint: .teal)
            MetricCard(title: "体脂肪率", value: entries.first?.bodyFat.map { $0.formatted(.number.precision(.fractionLength(1))) + "%" } ?? "--", footnote: "任意入力", symbol: "percent", tint: .purple)
        }
    }

    private var chart: some View {
        VStack(alignment: .leading, spacing: 12) {
            SectionHeader("90日推移")
            if chartEntries.isEmpty {
                EmptyStateView(symbol: "chart.xyaxis.line", title: "まだグラフはありません", message: "身体データを1件登録すると表示されます。")
                    .frame(minHeight: 200)
            } else {
                Chart(chartEntries) { entry in
                    LineMark(
                        x: .value("日付", entry.date),
                        y: .value("体重", entry.weight)
                    )
                    .foregroundStyle(.teal)
                    PointMark(
                        x: .value("日付", entry.date),
                        y: .value("体重", entry.weight)
                    )
                    .foregroundStyle(.teal)
                }
                .frame(height: 220)
                .chartYAxisLabel("kg")
            }
        }
        .padding(18)
        .background(Color(.secondarySystemGroupedBackground), in: RoundedRectangle(cornerRadius: 24, style: .continuous))
    }

    private var form: some View {
        VStack(alignment: .leading, spacing: 16) {
            SectionHeader("記録を追加")
            DatePicker("日付", selection: $date, displayedComponents: .date)
            HStack {
                Text("体重")
                Spacer()
                Text(weight.kgText).monospacedDigit().foregroundStyle(.secondary)
            }
            Slider(value: $weight, in: 30...140, step: 0.1)
            Toggle("体脂肪率を入力", isOn: $usesBodyFat)
            if usesBodyFat {
                Stepper("体脂肪率 \(bodyFat.formatted(.number.precision(.fractionLength(1))))%", value: $bodyFat, in: 3...60, step: 0.1)
            }
            TextField("メモ", text: $note, axis: .vertical)
                .textFieldStyle(.roundedBorder)
            Button {
                addEntry()
            } label: {
                Label("身体データを保存", systemImage: "checkmark.circle.fill")
                    .frame(maxWidth: .infinity)
            }
            .buttonStyle(.borderedProminent)
            .controlSize(.large)
            .tint(.teal)
        }
        .padding(18)
        .background(Color(.secondarySystemGroupedBackground), in: RoundedRectangle(cornerRadius: 24, style: .continuous))
    }

    private var history: some View {
        VStack(alignment: .leading, spacing: 12) {
            SectionHeader("履歴")
            ForEach(entries.prefix(12)) { entry in
                HStack {
                    VStack(alignment: .leading, spacing: 4) {
                        Text(entry.date.shortJapaneseDate)
                            .font(.headline)
                        if !entry.note.isEmpty {
                            Text(entry.note)
                                .font(.caption)
                                .foregroundStyle(.secondary)
                        }
                    }
                    Spacer()
                    VStack(alignment: .trailing, spacing: 4) {
                        Text(entry.weight.kgText)
                            .font(.subheadline.monospacedDigit())
                        Text(entry.bodyFat.map { $0.formatted(.number.precision(.fractionLength(1))) + "%" } ?? "体脂肪率なし")
                            .font(.caption)
                            .foregroundStyle(.secondary)
                    }
                }
                .padding(14)
                .background(Color(.secondarySystemGroupedBackground), in: RoundedRectangle(cornerRadius: 16, style: .continuous))
            }
        }
    }

    private func addEntry() {
        modelContext.insert(BodyEntry(date: date, weight: weight, bodyFat: usesBodyFat ? bodyFat : nil, note: note))
        try? modelContext.save()
        note = ""
    }
}
