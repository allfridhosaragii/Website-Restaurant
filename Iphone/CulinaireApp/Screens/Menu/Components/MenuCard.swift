import SwiftUI
struct MenuCard: View {
    let menu: Menu
    let onAdd: () -> Void
    let onTap: () -> Void
    var body: some View {
        Button(action: onTap) {
            VStack(alignment: .leading, spacing: 0) {
                ZStack {
                    if let urlString = menu.image_url, let url = URL(string: urlString) {
                        AsyncImage(url: url) { phase in
                            switch phase {
                            case .empty:
                                Color(hex: "F3F4F6")
                            case .success(let image):
                                image
                                    .resizable()
                                    .aspectRatio(contentMode: .fill)
                            case .failure:
                                Color(hex: "F3F4F6")
                                    .overlay(Image(systemName: "fork.knife").foregroundColor(.gray))
                            @unknown default:
                                Color(hex: "F3F4F6")
                            }
                        }
                    } else {
                        Color(hex: "F3F4F6")
                            .overlay(Image(systemName: "fork.knife").foregroundColor(.gray))
                    }
                }
                .frame(height: 120)
                .frame(maxWidth: .infinity)
                .background(Color(hex: "F3F4F6"))
                .clipped()
                VStack(alignment: .leading, spacing: 6) {
                    Text(menu.name)
                        .font(.system(size: 14, weight: .bold))
                        .foregroundColor(.textPrimary)
                        .lineLimit(2)
                        .frame(minHeight: 40, alignment: .topLeading)
                    HStack(spacing: 4) {
                        Image(systemName: "star.fill")
                            .font(.system(size: 10))
                            .foregroundColor(.yellow)
                        Text("4.8")
                            .font(.system(size: 11, weight: .semibold))
                            .foregroundColor(Color(hex: "666666"))
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
                                .frame(width: 28, height: 28)
                                .background(Color.primaryBrand)
                                .cornerRadius(8)
                        }
                    }
                }
                .padding(12)
            }
            .background(Color.white)
            .cornerRadius(16)
            .shadow(color: Color.black.opacity(0.1), radius: 5, x: 0, y: 2)
        }
        .buttonStyle(PlainButtonStyle())
    }
}