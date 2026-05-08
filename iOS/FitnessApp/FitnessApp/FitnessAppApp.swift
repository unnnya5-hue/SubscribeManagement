import SwiftUI
import SwiftData

@main
struct FitnessAppApp: App {
    var sharedModelContainer: ModelContainer = {
        let schema = Schema([
            Movement.self,
            WorkoutSession.self,
            TrainingSet.self,
            BodyEntry.self,
        ])
        let configuration = ModelConfiguration(schema: schema, isStoredInMemoryOnly: false)

        do {
            return try ModelContainer(for: schema, configurations: [configuration])
        } catch {
            fatalError("Could not create SwiftData container: \(error)")
        }
    }()

    var body: some Scene {
        WindowGroup {
            RootView()
        }
        .modelContainer(sharedModelContainer)
    }
}
