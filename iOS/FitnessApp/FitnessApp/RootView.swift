import SwiftUI
import SwiftData

struct RootView: View {
    @Environment(\.modelContext) private var modelContext
    @Query(sort: \Movement.sortOrder) private var movements: [Movement]

    var body: some View {
        TabView {
            DashboardView()
                .tabItem { Label("ホーム", systemImage: "house.fill") }

            WorkoutLogView()
                .tabItem { Label("記録", systemImage: "figure.strengthtraining.traditional") }

            BodyLogView()
                .tabItem { Label("身体", systemImage: "chart.xyaxis.line") }

            MovementLibraryView()
                .tabItem { Label("種目", systemImage: "square.grid.2x2.fill") }

            SettingsView()
                .tabItem { Label("設定", systemImage: "gearshape.fill") }
        }
        .tint(.indigo)
        .task {
            seedMovementsIfNeeded()
        }
    }

    private func seedMovementsIfNeeded() {
        guard movements.isEmpty else { return }
        SeedData.movements.forEach { modelContext.insert($0) }
        try? modelContext.save()
    }
}
