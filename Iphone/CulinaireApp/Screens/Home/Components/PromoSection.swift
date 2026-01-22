import SwiftUI
struct PromoSection: View {
    var onPromoPress: () -> Void
    var body: some View {
        ScrollView(.horizontal, showsIndicators: false) {
            HStack(spacing: 12) {
                PromoCard(
                    colors: [Color.primaryBrand, Color.primaryDim],
                    title: "Diskon 30% Hari Ini!",
                    subtitle: "Untuk semua menu pilihan",
                    image: "https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=300",
                    onPress: onPromoPress
                )
                PromoCard(
                    colors: [Color.blackBrand, Color.black],
                    title: "Makan Gratis!",
                    subtitle: "Minimal belanja Rp 200rb",
                    image: nil,
                    onPress: onPromoPress
                )
            }
            .padding(.horizontal, 20)
        }
    }
}
struct PromoCard: View {
    let colors: [Color]
    let title: String
    let subtitle: String
    let image: String?
    let onPress: () -> Void
    var body: some View {
        Button(action: onPress) {
            ZStack(alignment: .leading) {
                LinearGradient(colors: colors, startPoint: .topLeading, endPoint: .bottomTrailing)
                HStack(spacing: 0) {
                    VStack(alignment: .leading, spacing: 4) {
                        Text(title)
                            .font(.system(size: 18, weight: .bold))
                            .foregroundColor(.white)
                        Text(subtitle)
                            .font(.system(size: 11))
                            .foregroundColor(.white.opacity(0.8))
                            .padding(.bottom, 8)
                        Text("Pesan Sekarang")
                            .font(.system(size: 11, weight: .semibold))
                            .foregroundColor(.blackBrand)
                            .padding(.horizontal, 14)
                            .padding(.vertical, 8)
                            .background(Color.white)
                            .cornerRadius(20)
                    }
                    .padding(16)
                    if let image = image, let url = URL(string: image) {
                        Spacer()
                        AsyncImage(url: url) { image in
                            image.resizable().aspectRatio(contentMode: .cover)
                        } placeholder: {
                            Color.gray.opacity(0.3)
                        }
                        .frame(width: 120)
                    } else {
                        Spacer()
                    }
                }
            }
            .frame(width: UIScreen.main.bounds.width * 0.75, height: 140)
            .cornerRadius(16)
            .clipped() 
        }
        .buttonStyle(PlainButtonStyle())
    }
}