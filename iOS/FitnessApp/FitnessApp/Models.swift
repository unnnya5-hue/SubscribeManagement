import Foundation
import SwiftData

@Model
final class Movement {
    var name: String
    var bodyPart: String
    var defaultWeight: Double
    var targetSets: Int
    var targetReps: Int
    var sortOrder: Int
    var createdAt: Date

    init(
        name: String,
        bodyPart: String,
        defaultWeight: Double = 20,
        targetSets: Int = 3,
        targetReps: Int = 10,
        sortOrder: Int = 0
    ) {
        self.name = name
        self.bodyPart = bodyPart
        self.defaultWeight = defaultWeight
        self.targetSets = targetSets
        self.targetReps = targetReps
        self.sortOrder = sortOrder
        self.createdAt = .now
    }
}

@Model
final class WorkoutSession {
    var title: String
    var date: Date
    var note: String
    @Relationship(deleteRule: .cascade) var sets: [TrainingSet]

    init(title: String, date: Date = .now, note: String = "", sets: [TrainingSet] = []) {
        self.title = title
        self.date = date
        self.note = note
        self.sets = sets
    }
}

@Model
final class TrainingSet {
    var movementName: String
    var bodyPart: String
    var setNumber: Int
    var weight: Double
    var reps: Int
    var rpe: Double
    var restSeconds: Int

    init(
        movementName: String,
        bodyPart: String,
        setNumber: Int,
        weight: Double,
        reps: Int,
        rpe: Double = 8,
        restSeconds: Int = 90
    ) {
        self.movementName = movementName
        self.bodyPart = bodyPart
        self.setNumber = setNumber
        self.weight = weight
        self.reps = reps
        self.rpe = rpe
        self.restSeconds = restSeconds
    }
}

@Model
final class BodyEntry {
    var date: Date
    var weight: Double
    var bodyFat: Double?
    var note: String

    init(date: Date = .now, weight: Double, bodyFat: Double? = nil, note: String = "") {
        self.date = date
        self.weight = weight
        self.bodyFat = bodyFat
        self.note = note
    }
}

extension TrainingSet {
    var estimatedOneRepMax: Double {
        guard reps > 0 else { return weight }
        return (weight * (1 + Double(reps) / 30)).rounded(toPlaces: 1)
    }

    var volume: Double {
        weight * Double(reps)
    }
}

extension WorkoutSession {
    var totalVolume: Double {
        sets.reduce(0) { $0 + $1.volume }
    }

    var primaryBodyParts: String {
        let parts = Array(Set(sets.map(\.bodyPart))).sorted()
        return parts.isEmpty ? "未分類" : parts.joined(separator: " / ")
    }
}

extension Double {
    func rounded(toPlaces places: Int) -> Double {
        let divisor = pow(10.0, Double(places))
        return (self * divisor).rounded() / divisor
    }
}

enum SeedData {
    static let movements: [Movement] = [
        Movement(name: "ベンチプレス", bodyPart: "胸", defaultWeight: 60, targetSets: 3, targetReps: 8, sortOrder: 0),
        Movement(name: "スクワット", bodyPart: "脚", defaultWeight: 80, targetSets: 3, targetReps: 8, sortOrder: 1),
        Movement(name: "デッドリフト", bodyPart: "背中", defaultWeight: 90, targetSets: 3, targetReps: 5, sortOrder: 2),
        Movement(name: "ラットプルダウン", bodyPart: "背中", defaultWeight: 45, targetSets: 3, targetReps: 10, sortOrder: 3),
        Movement(name: "ショルダープレス", bodyPart: "肩", defaultWeight: 30, targetSets: 3, targetReps: 10, sortOrder: 4),
        Movement(name: "ダンベルカール", bodyPart: "腕", defaultWeight: 12, targetSets: 3, targetReps: 12, sortOrder: 5),
        Movement(name: "レッグプレス", bodyPart: "脚", defaultWeight: 120, targetSets: 3, targetReps: 10, sortOrder: 6),
        Movement(name: "プランク", bodyPart: "体幹", defaultWeight: 0, targetSets: 3, targetReps: 1, sortOrder: 7),
    ]
}
