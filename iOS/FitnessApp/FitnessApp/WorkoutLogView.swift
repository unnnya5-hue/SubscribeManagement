import SwiftUI
import SwiftData

private struct DraftSet: Identifiable {
    let id = UUID()
    var movementName: String
    var bodyPart: String
    var setNumber: Int
    var weight: Double
    var reps: Int
    var rpe: Double
    var restSeconds: Int

    var oneRepMax: Double {
        (weight * (1 + Double(reps) / 30)).rounded(toPlaces: 1)
    }
}

struct WorkoutLogView: View {
    @Environment(\.modelContext) private var modelContext
    @Query(sort: \Movement.sortOrder) private var movements: [Movement]
    @Query(sort: \WorkoutSession.date, order: .reverse) private var workouts: [WorkoutSession]

    @State private var selectedMovement: Movement?
    @State private var weight = 40.0
    @State private var reps = 10
    @State private var rpe = 8.0
    @State private var restSeconds = 90
    @State private var draftSets: [DraftSet] = []
    @State private var note = ""
    @State private var workoutPendingDeletion: WorkoutSession?
    @State private var isShowingDeleteConfirmation = false
    @State private var saveMessage: String?
    @FocusState private var isNoteFocused: Bool
    @FocusState private var isWeightFocused: Bool

    var body: some View {
        NavigationStack {
            ZStack(alignment: .top) {
                ScrollView {
                    VStack(spacing: 20) {
                        inputPanel
                        currentSession
                        history
                    }
                    .padding()
                    .safeAreaPadding(.top, saveMessage == nil ? 0 : 64)
                    .safeAreaPadding(.bottom, 24)
                }
                if let saveMessage {
                    SaveConfirmationBanner(message: saveMessage)
                        .transition(.move(edge: .top).combined(with: .opacity))
                }
            }
            .background(Color(.systemGroupedBackground))
            .navigationTitle("記録")
            .toolbar {
                if isNoteFocused || isWeightFocused {
                    ToolbarItemGroup(placement: .keyboard) {
                        Spacer()
                        Button("完了") {
                            isNoteFocused = false
                            isWeightFocused = false
                        }
                    }
                }
            }
            .onAppear {
                if selectedMovement == nil {
                    select(movements.first)
                }
            }
            .confirmationDialog(
                "このワークアウト記録を削除しますか？",
                isPresented: $isShowingDeleteConfirmation,
                titleVisibility: .visible
            ) {
                Button("削除", role: .destructive) {
                    deletePendingWorkout()
                }
                Button("キャンセル", role: .cancel) {
                    workoutPendingDeletion = nil
                }
            } message: {
                Text("削除した記録は元に戻せません。")
            }
        }
    }

    private var inputPanel: some View {
        VStack(alignment: .leading, spacing: 16) {
            SectionHeader("セットを追加", subtitle: "入力して追加、最後にまとめて保存します。")

            Picker("種目", selection: Binding(
                get: { selectedMovement?.persistentModelID },
                set: { id in select(movements.first { $0.persistentModelID == id }) }
            )) {
                ForEach(movements) { movement in
                    Text(movement.name).tag(Optional(movement.persistentModelID))
                }
            }
            .pickerStyle(.menu)

            VStack(spacing: 14) {
                HStack(spacing: 12) {
                    Text("重量")
                        .font(.headline)
                    Spacer()
                    TextField("40.0", value: $weight, format: .number.precision(.fractionLength(0...1)))
                        .keyboardType(.decimalPad)
                        .multilineTextAlignment(.trailing)
                        .font(.system(.title3, design: .rounded, weight: .semibold).monospacedDigit())
                        .focused($isWeightFocused)
                        .frame(width: 110)
                        .padding(.horizontal, 12)
                        .padding(.vertical, 10)
                        .background(Color(.tertiarySystemGroupedBackground), in: RoundedRectangle(cornerRadius: 12, style: .continuous))
                    Text("kg")
                        .font(.headline)
                        .foregroundStyle(.secondary)
                }
                Slider(value: $weight, in: 0...200, step: 2.5)

                Stepper("回数 \(reps)", value: $reps, in: 1...50)
                Stepper("RPE \(rpe.formatted(.number.precision(.fractionLength(1))))", value: $rpe, in: 1...10, step: 0.5)
                Stepper("休憩 \(restSeconds)秒", value: $restSeconds, in: 0...300, step: 15)
            }

            Button {
                addSet()
            } label: {
                Label("セットを追加", systemImage: "plus.circle.fill")
                    .frame(maxWidth: .infinity)
            }
            .buttonStyle(.borderedProminent)
            .controlSize(.large)
            .disabled(selectedMovement == nil)
        }
        .padding(18)
        .background(Color(.secondarySystemGroupedBackground), in: RoundedRectangle(cornerRadius: 24, style: .continuous))
    }

    private var currentSession: some View {
        VStack(alignment: .leading, spacing: 14) {
            SectionHeader("現在のセッション", subtitle: draftSets.isEmpty ? "まだセットがありません。" : "\(draftSets.count)セット入力中")

            if draftSets.isEmpty {
                EmptyStateView(symbol: "plus.rectangle.on.rectangle", title: "セットを追加", message: "今日のトレーニングを1セットずつ積み上げます。")
                    .frame(minHeight: 160)
            } else {
                ForEach(draftSets) { set in
                    HStack {
                        VStack(alignment: .leading, spacing: 4) {
                            Text(set.movementName)
                                .font(.headline)
                            Text("\(set.setNumber)セット目 / RPE \(set.rpe.formatted(.number.precision(.fractionLength(1)))) / 1RM \(set.oneRepMax.kgText)")
                                .font(.caption)
                                .foregroundStyle(.secondary)
                        }
                        Spacer()
                        Text("\(set.weight.kgText) x \(set.reps)")
                            .font(.subheadline.monospacedDigit())
                    }
                    .padding(14)
                    .background(.thinMaterial, in: RoundedRectangle(cornerRadius: 16, style: .continuous))
                }
                noteEditor
                Button {
                    saveWorkout()
                } label: {
                    Label("ワークアウトを保存", systemImage: "checkmark.circle.fill")
                        .frame(maxWidth: .infinity)
                }
                .buttonStyle(.borderedProminent)
                .controlSize(.large)
                .tint(.teal)
            }
        }
    }

    private var noteEditor: some View {
        VStack(alignment: .leading, spacing: 8) {
            Text("メモ")
                .font(.subheadline.weight(.semibold))
            ZStack(alignment: .topLeading) {
                RoundedRectangle(cornerRadius: 14, style: .continuous)
                    .fill(Color(.tertiarySystemGroupedBackground))
                if note.isEmpty {
                    Text("気づいたこと、痛み、フォームの感覚など")
                        .foregroundStyle(.secondary)
                        .padding(.horizontal, 12)
                        .padding(.vertical, 12)
                }
                TextEditor(text: $note)
                    .focused($isNoteFocused)
                    .frame(minHeight: 88)
                    .padding(8)
                    .scrollContentBackground(.hidden)
                    .background(Color.clear)
            }
            .overlay(
                RoundedRectangle(cornerRadius: 14, style: .continuous)
                    .stroke(isNoteFocused ? Color.indigo.opacity(0.8) : Color.secondary.opacity(0.18), lineWidth: 1)
            )
            Text(note.isEmpty ? "メモ未入力" : "\(note.count)文字入力中")
                .font(.caption)
                .foregroundStyle(.secondary)
        }
    }

    private var history: some View {
        VStack(alignment: .leading, spacing: 12) {
            SectionHeader("履歴")
            ForEach(workouts.prefix(8)) { workout in
                VStack(alignment: .leading, spacing: 6) {
                    HStack {
                        Text(workout.title)
                            .font(.headline)
                        Spacer()
                        Text(workout.totalVolume.kgText)
                            .font(.subheadline.monospacedDigit())
                            .foregroundStyle(.secondary)
                        Button(role: .destructive) {
                            workoutPendingDeletion = workout
                            isShowingDeleteConfirmation = true
                        } label: {
                            Image(systemName: "trash")
                                .font(.subheadline.weight(.semibold))
                        }
                        .buttonStyle(.borderless)
                        .accessibilityLabel("ワークアウト記録を削除")
                    }
                    Text("\(workout.date.shortJapaneseDate) / \(workout.sets.count)セット")
                        .font(.caption)
                        .foregroundStyle(.secondary)
                    if !workout.note.isEmpty {
                        Text(workout.note)
                            .font(.subheadline)
                            .foregroundStyle(.primary)
                            .lineLimit(3)
                            .padding(.top, 2)
                    }
                }
                .padding(14)
                .background(Color(.secondarySystemGroupedBackground), in: RoundedRectangle(cornerRadius: 16, style: .continuous))
            }
        }
    }

    private func select(_ movement: Movement?) {
        selectedMovement = movement
        guard let movement else { return }
        weight = movement.defaultWeight
        reps = movement.targetReps
    }

    private func addSet() {
        guard let movement = selectedMovement else { return }
        let nextNumber = draftSets.filter { $0.movementName == movement.name }.count + 1
        draftSets.append(DraftSet(
            movementName: movement.name,
            bodyPart: movement.bodyPart,
            setNumber: nextNumber,
            weight: weight,
            reps: reps,
            rpe: rpe,
            restSeconds: restSeconds
        ))
    }

    private func saveWorkout() {
        let title = draftSets.first?.movementName ?? "ワークアウト"
        let trimmedNote = note.trimmingCharacters(in: .whitespacesAndNewlines)
        let sets = draftSets.map {
            TrainingSet(
                movementName: $0.movementName,
                bodyPart: $0.bodyPart,
                setNumber: $0.setNumber,
                weight: $0.weight,
                reps: $0.reps,
                rpe: $0.rpe,
                restSeconds: $0.restSeconds
            )
        }
        modelContext.insert(WorkoutSession(title: title, note: trimmedNote, sets: sets))
        try? modelContext.save()
        draftSets = []
        note = ""
        isNoteFocused = false
        showSaved("ワークアウトを保存しました")
    }

    private func deletePendingWorkout() {
        guard let workoutPendingDeletion else { return }
        modelContext.delete(workoutPendingDeletion)
        try? modelContext.save()
        self.workoutPendingDeletion = nil
    }

    private func showSaved(_ message: String) {
        UINotificationFeedbackGenerator().notificationOccurred(.success)
        withAnimation(.spring(response: 0.32, dampingFraction: 0.82)) {
            saveMessage = message
        }
        Task { @MainActor in
            try? await Task.sleep(for: .seconds(2))
            withAnimation(.easeOut(duration: 0.22)) {
                saveMessage = nil
            }
        }
    }
}
