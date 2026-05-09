import SwiftUI

struct MetricCard: View {
    let title: String
    let value: String
    let footnote: String
    let symbol: String
    var tint: Color = .indigo

    var body: some View {
        VStack(alignment: .leading, spacing: 12) {
            HStack {
                Image(systemName: symbol)
                    .font(.headline)
                    .foregroundStyle(tint)
                Spacer()
            }
            Text(value)
                .font(.system(.title2, design: .rounded, weight: .bold))
                .monospacedDigit()
            VStack(alignment: .leading, spacing: 2) {
                Text(title)
                    .font(.subheadline.weight(.semibold))
                Text(footnote)
                    .font(.caption)
                    .foregroundStyle(.secondary)
            }
        }
        .frame(maxWidth: .infinity, alignment: .leading)
        .padding(16)
        .background(.thinMaterial, in: RoundedRectangle(cornerRadius: 18, style: .continuous))
    }
}

struct EmptyStateView: View {
    let symbol: String
    let title: String
    let message: String

    var body: some View {
        ContentUnavailableView {
            Label(title, systemImage: symbol)
        } description: {
            Text(message)
        }
    }
}

struct SectionHeader: View {
    let title: String
    let subtitle: String?

    init(_ title: String, subtitle: String? = nil) {
        self.title = title
        self.subtitle = subtitle
    }

    var body: some View {
        VStack(alignment: .leading, spacing: 4) {
            Text(title)
                .font(.title3.bold())
            if let subtitle {
                Text(subtitle)
                    .font(.subheadline)
                    .foregroundStyle(.secondary)
            }
        }
        .frame(maxWidth: .infinity, alignment: .leading)
    }
}

struct SaveConfirmationBanner: View {
    let message: String

    var body: some View {
        HStack(spacing: 10) {
            Image(systemName: "checkmark.circle.fill")
                .font(.title3)
                .foregroundStyle(.white)
            Text(message)
                .font(.subheadline.weight(.semibold))
                .foregroundStyle(.white)
            Spacer(minLength: 0)
        }
        .padding(.horizontal, 16)
        .padding(.vertical, 14)
        .background(.teal.gradient, in: Capsule())
        .shadow(color: .teal.opacity(0.28), radius: 18, x: 0, y: 10)
        .padding(.horizontal)
        .padding(.top, 8)
    }
}

extension Date {
    var shortJapaneseDate: String {
        formatted(.dateTime.month(.defaultDigits).day().weekday(.abbreviated).locale(Locale(identifier: "ja_JP")))
    }
}

extension Double {
    var kgText: String {
        formatted(.number.precision(.fractionLength(0...1))) + "kg"
    }
}
