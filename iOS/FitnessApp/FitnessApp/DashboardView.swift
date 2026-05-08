import SwiftUI
import SwiftData

struct DashboardView: View {
    @Query(sort: \WorkoutSession.date, order: .reverse) private var workouts: [WorkoutSession]
    @Query(sort: \BodyEntry.date, order: .reverse) private var bodyEntries: [BodyEntry]
    @Query(sort: \Movement.sortOrder) private var movements: [Movement]

    private var latestWeight: String {
        bodyEntries.first?.weight.kgText ?? "--"
    }

    private var weeklyVolume: Double {
        let weekAgo = Calendar.current.date(byAdding: .day, value: -7, to: .now) ?? .now
        return workouts
            .filter { $0.date >= weekAgo }
            .reduce(0) { $0 + $1.totalVolume }
    }

    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 20) {
                    hero
                    metrics
                    todayPlan
                    recentWorkouts
                }
                .padding()
            }
            .background(Color(.systemGroupedBackground))
            .navigationTitle("ホーム")
        }
    }

    private var hero: some View {
        VStack(alignment: .leading, spacing: 12) {
            Text(Date.now.shortJapaneseDate)
                .font(.subheadline.weight(.semibold))
                .foregroundStyle(.secondary)
            Text("今日の記録を、軽く整える。")
                .font(.system(.largeTitle, design: .rounded, weight: .bold))
                .frame(maxWidth: .infinity, alignment: .leading)
            Text("端末内だけに保存される、個人用の筋トレログです。")
                .font(.subheadline)
                .foregroundStyle(.secondary)
        }
        .padding(22)
        .background(
            LinearGradient(
                colors: [Color.indigo.opacity(0.22), Color.teal.opacity(0.16), Color(.secondarySystemGroupedBackground)],
                startPoint: .topLeading,
                endPoint: .bottomTrailing
            ),
            in: RoundedRectangle(cornerRadius: 28, style: .continuous)
        )
    }

    private var metrics: some View {
        LazyVGrid(columns: [GridItem(.flexible()), GridItem(.flexible())], spacing: 12) {
            MetricCard(title: "最新体重", value: latestWeight, footnote: "身体タブで記録", symbol: "scalemass.fill", tint: .teal)
            MetricCard(title: "7日ボリューム", value: weeklyVolume.kgText, footnote: "重量 x 回数", symbol: "chart.bar.fill", tint: .orange)
            MetricCard(title: "累計ワークアウト", value: "\(workouts.count)", footnote: "保存済みセッション", symbol: "calendar.badge.checkmark", tint: .indigo)
            MetricCard(title: "登録種目", value: "\(movements.count)", footnote: "種目タブで編集", symbol: "dumbbell.fill", tint: .pink)
        }
    }

    private var todayPlan: some View {
        VStack(alignment: .leading, spacing: 12) {
            SectionHeader("今日の候補", subtitle: "よく使う種目からすぐ始められます。")
            ForEach(movements.prefix(4)) { movement in
                HStack(spacing: 12) {
                    Image(systemName: "circle.hexagongrid.fill")
                        .foregroundStyle(.indigo)
                    VStack(alignment: .leading, spacing: 2) {
                        Text(movement.name)
                            .font(.headline)
                        Text("\(movement.bodyPart) / \(movement.targetSets)セット x \(movement.targetReps)回")
                            .font(.caption)
                            .foregroundStyle(.secondary)
                    }
                    Spacer()
                    Text(movement.defaultWeight.kgText)
                        .font(.subheadline.monospacedDigit())
                        .foregroundStyle(.secondary)
                }
                .padding(14)
                .background(Color(.secondarySystemGroupedBackground), in: RoundedRectangle(cornerRadius: 16, style: .continuous))
            }
        }
    }

    private var recentWorkouts: some View {
        VStack(alignment: .leading, spacing: 12) {
            SectionHeader("最近のワークアウト")
            if workouts.isEmpty {
                EmptyStateView(symbol: "figure.strengthtraining.traditional", title: "まだ記録がありません", message: "記録タブから最初のセットを保存しましょう。")
                    .frame(minHeight: 180)
            } else {
                ForEach(workouts.prefix(5)) { workout in
                    HStack {
                        VStack(alignment: .leading, spacing: 4) {
                            Text(workout.title)
                                .font(.headline)
                            Text("\(workout.date.shortJapaneseDate) / \(workout.primaryBodyParts)")
                                .font(.caption)
                                .foregroundStyle(.secondary)
                        }
                        Spacer()
                        Text(workout.totalVolume.kgText)
                            .font(.subheadline.monospacedDigit())
                    }
                    .padding(14)
                    .background(Color(.secondarySystemGroupedBackground), in: RoundedRectangle(cornerRadius: 16, style: .continuous))
                }
            }
        }
    }
}
