import SwiftUI
struct StatsRow: View {
    var body: some View {
        HStack(spacing: 8) {
            StatCard(
                icon: "star.fill",
                iconColor: Color(hex: "4CAF50"),
                label: "Rating",
                value: "4.9",
                bgColor: Color(hex: "E8F5E9")
            )
            StatCard(
                icon: "flame.fill",
                iconColor: Color(hex: "FF9800"),
                label: "Hari Ini",
                value: "342+",
                bgColor: Color(hex: "FFF3E0")
            )
            StatCard(
                icon: "heart.fill",
                iconColor: Color(hex: "E91E63"),
                label: "Favorit",
                value: "Best",
                bgColor: Color(hex: "FCE4EC")
            )
        }
    }
}
struct StatCard: View {
    let icon: String
    let iconColor: Color
    let label: String
    let value: String
    let bgColor: Color
    var body: some View {
        VStack(spacing: 4) {
            Image(systemName: icon)
                .font(.system(size: 20))
                .foregroundColor(iconColor)
            Text(label)
                .font(.system(size: 11))
                .foregroundColor(.textSecondary) 
            Text(value)
                .font(.system(size: 18, weight: .bold))
                .foregroundColor(.textPrimary)
        }
        .frame(maxWidth: .infinity)
        .padding(14)
        .background(bgColor)
        .cornerRadius(12)
    }
}