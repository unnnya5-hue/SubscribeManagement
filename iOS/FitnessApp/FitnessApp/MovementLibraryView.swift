import SwiftUI
import SwiftData

struct MovementLibraryView: View {
    @Environment(\.modelContext) private var modelContext
    @Query(sort: \Movement.sortOrder) private var movements: [Movement]

    @State private var name = ""
    @State private var bodyPart = "胸"
    @State private var defaultWeight = 40.0
    @State private var targetSets = 3
    @State private var targetReps = 10

    private let bodyParts = ["胸", "背中", "脚", "肩", "腕", "体幹", "有酸素", "その他"]

    var body: some View {
        NavigationStack {
            List {
                Section {
                    addForm
                } header: {
                    Text("種目を追加")
                }

                Section {
                    ForEach(movements) { movement in
                        VStack(alignment: .leading, spacing: 6) {
                            HStack {
                                Text(movement.name)
                                    .font(.headline)
                                Spacer()
                                Text(movement.defaultWeight.kgText)
                                    .font(.subheadline.monospacedDigit())
                                    .foregroundStyle(.secondary)
                            }
                            Text("\(movement.bodyPart) / \(movement.targetSets)セット x \(movement.targetReps)回")
                                .font(.caption)
                                .foregroundStyle(.secondary)
                        }
                        .padding(.vertical, 6)
                    }
                    .onDelete(perform: delete)
                } header: {
                    Text("登録済み")
                } footer: {
                    Text("左へスワイプすると削除できます。")
                }
            }
            .navigationTitle("種目")
        }
    }

    private var addForm: some View {
        VStack(spacing: 14) {
            TextField("種目名", text: $name)
                .textInputAutocapitalization(.never)
            Picker("部位", selection: $bodyPart) {
                ForEach(bodyParts, id: \.self) { part in
                    Text(part).tag(part)
                }
            }
            HStack {
                Text("標準重量")
                Spacer()
                Text(defaultWeight.kgText).monospacedDigit().foregroundStyle(.secondary)
            }
            Slider(value: $defaultWeight, in: 0...200, step: 2.5)
            Stepper("セット数 \(targetSets)", value: $targetSets, in: 1...10)
            Stepper("回数 \(targetReps)", value: $targetReps, in: 1...50)
            Button {
                addMovement()
            } label: {
                Label("種目を追加", systemImage: "plus.circle.fill")
                    .frame(maxWidth: .infinity)
            }
            .buttonStyle(.borderedProminent)
            .disabled(name.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty)
        }
        .padding(.vertical, 6)
    }

    private func addMovement() {
        let trimmedName = name.trimmingCharacters(in: .whitespacesAndNewlines)
        guard !trimmedName.isEmpty else { return }
        modelContext.insert(Movement(
            name: trimmedName,
            bodyPart: bodyPart,
            defaultWeight: defaultWeight,
            targetSets: targetSets,
            targetReps: targetReps,
            sortOrder: movements.count
        ))
        try? modelContext.save()
        name = ""
    }

    private func delete(at offsets: IndexSet) {
        for index in offsets {
            modelContext.delete(movements[index])
        }
        try? modelContext.save()
    }
}
