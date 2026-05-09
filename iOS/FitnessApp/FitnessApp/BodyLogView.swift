import SwiftUI
import SwiftData
import Charts

private enum BodyChartMetric: String, CaseIterable, Identifiable {
    case weight = "体重"
    case bodyFat = "体脂肪率"

    var id: String { rawValue }
}

struct BodyLogView: View {
    @Environment(\.modelContext) private var modelContext
    @Query(sort: \BodyEntry.date, order: .reverse) private var entries: [BodyEntry]

    @State private var selectedMetric: BodyChartMetric = .weight
    @State private var date = Date()
    @State private var weight = 65.0
    @State private var bodyFat = 18.0
    @State private var usesBodyFat = true
    @State private var note = ""
    @State private var bodyEntryPendingDeletion: BodyEntry?
    @State private var isShowingDeleteConfirmation = false

    private var chartEntries: [BodyEntry] {
        Array(entries.prefix(90)).reversed()
    }

    private var bodyFatChartEntries: [BodyEntry] {
        chartEntries.filter { $0.bodyFat != nil }
    }

    private var weightDomain: ClosedRange<Double> {
        paddedDomain(for: chartEntries.map(\.weight), minimumPadding: 2)
    }

    private var bodyFatDomain: ClosedRange<Double> {
        paddedDomain(for: bodyFatChartEntries.compactMap(\.bodyFat), minimumPadding: 1)
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
                .safeAreaPadding(.bottom, 96)
            }
            .background(Color(.systemGroupedBackground))
            .navigationTitle("身体")
            .confirmationDialog(
                "この身体記録を削除しますか？",
                isPresented: $isShowingDeleteConfirmation,
                titleVisibility: .visible
            ) {
                Button("削除", role: .destructive) {
                    deletePendingBodyEntry()
                }
                Button("キャンセル", role: .cancel) {
                    bodyEntryPendingDeletion = nil
                }
            } message: {
                Text("削除した記録は元に戻せません。")
            }
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
            Picker("表示", selection: $selectedMetric) {
                ForEach(BodyChartMetric.allCases) { metric in
                    Text(metric.rawValue).tag(metric)
                }
            }
            .pickerStyle(.segmented)

            if chartEntries.isEmpty {
                EmptyStateView(symbol: "chart.xyaxis.line", title: "まだグラフはありません", message: "身体データを1件登録すると表示されます。")
                    .frame(minHeight: 200)
            } else {
                switch selectedMetric {
                case .weight:
                    weightChart
                case .bodyFat:
                    if bodyFatChartEntries.isEmpty {
                        EmptyStateView(symbol: "percent", title: "体脂肪率の記録がありません", message: "体脂肪率をONにして保存すると推移が表示されます。")
                            .frame(minHeight: 220)
                    } else {
                        bodyFatChart
                    }
                }
            }
        }
        .padding(18)
        .background(Color(.secondarySystemGroupedBackground), in: RoundedRectangle(cornerRadius: 24, style: .continuous))
    }

    private var weightChart: some View {
        Chart(chartEntries) { entry in
            LineMark(
                x: .value("日付", entry.date),
                y: .value("体重", entry.weight)
            )
            .foregroundStyle(.teal)
            .interpolationMethod(.catmullRom)
            PointMark(
                x: .value("日付", entry.date),
                y: .value("体重", entry.weight)
            )
            .foregroundStyle(.teal)
        }
        .frame(height: 220)
        .chartYScale(domain: weightDomain)
        .chartYAxisLabel("kg")
    }

    private var bodyFatChart: some View {
        Chart(bodyFatChartEntries) { entry in
            if let bodyFat = entry.bodyFat {
                LineMark(
                    x: .value("日付", entry.date),
                    y: .value("体脂肪率", bodyFat)
                )
                .foregroundStyle(.purple)
                .interpolationMethod(.catmullRom)
                PointMark(
                    x: .value("日付", entry.date),
                    y: .value("体脂肪率", bodyFat)
                )
                .foregroundStyle(.purple)
            }
        }
        .frame(height: 220)
        .chartYScale(domain: bodyFatDomain)
        .chartYAxisLabel("%")
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
            HStack {
                TextField("65.0", value: $weight, format: .number.precision(.fractionLength(0...1)))
                    .keyboardType(.decimalPad)
                    .textFieldStyle(.roundedBorder)
                    .multilineTextAlignment(.trailing)
                    .font(.title3.monospacedDigit())
                Text("kg")
                    .font(.headline)
                    .foregroundStyle(.secondary)
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
                    Button(role: .destructive) {
                        bodyEntryPendingDeletion = entry
                        isShowingDeleteConfirmation = true
                    } label: {
                        Image(systemName: "trash")
                            .font(.subheadline.weight(.semibold))
                    }
                    .buttonStyle(.borderless)
                    .accessibilityLabel("身体記録を削除")
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

    private func deletePendingBodyEntry() {
        guard let bodyEntryPendingDeletion else { return }
        modelContext.delete(bodyEntryPendingDeletion)
        try? modelContext.save()
        self.bodyEntryPendingDeletion = nil
    }

    private func paddedDomain(for values: [Double], minimumPadding: Double) -> ClosedRange<Double> {
        guard let minimum = values.min(), let maximum = values.max() else {
            return 0...100
        }

        let padding = max((maximum - minimum) * 0.2, minimumPadding)
        let lower = max(0, minimum - padding)
        let upper = maximum + padding

        return lower...upper
    }
}
