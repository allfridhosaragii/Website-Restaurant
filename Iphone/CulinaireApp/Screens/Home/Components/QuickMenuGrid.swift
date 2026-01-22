import SwiftUI
struct QuickMenuGrid: View {
    var onMenuPress: () -> Void
    var onReservationPress: () -> Void
    struct QuickItem: Identifiable {
        let id = UUID()
        let label: String
        let icon: String 
        let color: Color
        let action: () -> Void
    }
    var items: [QuickItem] {
        [
            QuickItem(label: "Pesan Menu", icon: "fork.knife", color: .primaryBrand, action: onMenuPress),
            QuickItem(label: "Reservasi", icon: "calendar.badge.clock", color: .accentColor, action: onReservationPress), 
            QuickItem(label: "Promo", icon: "percent", color: Color(hex: "9C27B0"), action: onMenuPress),
            QuickItem(label: "Voucher", icon: "gift", color: Color.gold, action: {})
        ]
    }
    var body: some View {
        HStack(alignment: .top, spacing: 0) {
            ForEach(items.indices, id: \.self) { index in
                let item = items[index]
                Button(action: item.action) {
                    VStack(spacing: 8) {
                        ZStack {
                            if item.label == "Reservasi" {
                                Color(hex: "FF8C42") 
                            } else {
                                item.color
                            }
                        }
                        .frame(width: 52, height: 52)
                        .cornerRadius(14)
                        .overlay(
                            Image(systemName: item.icon)
                                .font(.system(size: 24))
                                .foregroundColor(.white)
                        )
                        Text(item.label)
                            .font(.system(size: 11))
                            .foregroundColor(.textPrimary)
                            .multilineTextAlignment(.center)
                    }
                    .frame(maxWidth: .infinity)
                }
            }
        }
    }
}