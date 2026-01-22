import SwiftUI
struct PopularMenuCard: View {
    let menu: Menu
    let isBestSeller: Bool
    let onAdd: () -> Void
    var body: some View {
        VStack(alignment: .leading, spacing: 0) {
            ZStack(alignment: .topLeading) {
                if let urlString = menu.image_url, let url = URL(string: urlString) {
                    AsyncImage(url: url) { phase in
                        switch phase {
                        case .empty:
                            Color.gray.opacity(0.1)
                        case .success(let image):
                            image
                                .resizable()
                                .aspectRatio(contentMode: .fill)
                        case .failure:
                            Color.gray.opacity(0.1)
                                .overlay(Image(systemName: "fork.knife").foregroundColor(.gray))
                        @unknown default:
                            Color.gray.opacity(0.1)
                        }
                    }
                    .frame(height: 100)
                    .clipped()
                } else {
                    Color.gray.opacity(0.1)
                        .frame(height: 100)
                        .overlay(Image(systemName: "fork.knife").foregroundColor(.gray))
                }
                if isBestSeller {
                    Text("Best Seller")
                        .font(.system(size: 9, weight: .bold))
                        .foregroundColor(.white)
                        .padding(.horizontal, 8)
                        .padding(.vertical, 4)
                        .background(Color.primaryBrand)
                        .cornerRadius(4)
                        .padding(8)
                }
            }
            .frame(width: 160)
            VStack(alignment: .leading, spacing: 6) {
                Text(menu.name)
                    .font(.system(size: 13, weight: .bold))
                    .foregroundColor(.textPrimary)
                    .lineLimit(1)
                HStack(spacing: 4) {
                    Image(systemName: "star.fill")
                        .font(.system(size: 10))
                        .foregroundColor(.yellow)
                    Text("4.8")
                        .font(.system(size: 11, weight: .semibold))
                        .foregroundColor(.textPrimary)
                    Text("150+ pesanan")
                        .font(.system(size: 10))
                        .foregroundColor(.textSecondary)
                }
                HStack {
                    Text(menu.formattedPrice)
                        .font(.system(size: 14, weight: .bold))
                        .foregroundColor(.primaryBrand)
                    Spacer()
                    Button(action: onAdd) {
                        Image(systemName: "plus")
                            .font(.system(size: 12, weight: .bold))
                            .foregroundColor(.white)
                            .frame(width: 26, height: 26)
                            .background(Color(hex: "00BCD4")) 
                            .cornerRadius(8)
                    }
                }
            }
            .padding(10)
        }
        .frame(width: 160)
        .background(Color.white)
        .cornerRadius(12)
        .shadow(color: Color.black.opacity(0.1), radius: 4, x: 0, y: 2)
    }
}