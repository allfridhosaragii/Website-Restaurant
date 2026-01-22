import SwiftUI
struct MenuDetailScreen: View {
    let menu: Menu
    @Environment(\.presentationMode) var presentationMode
    @State private var quantity: Int = 1
    @State private var isFavorite: Bool = false
    @State private var isAddingToCart: Bool = false
    var body: some View {
        ZStack {
            Color.blackBrand.ignoresSafeArea()
            ScrollView(showsIndicators: false) {
                VStack(spacing: 0) {
                    Color.clear.frame(height: UIScreen.main.bounds.height * 0.4)
                    VStack(alignment: .leading, spacing: 0) {
                        Capsule()
                            .fill(Color(hex: "333333"))
                            .frame(width: 40, height: 4)
                            .padding(.top, 24)
                            .frame(maxWidth: .infinity)
                        VStack(alignment: .leading, spacing: 8) {
                            Text(menu.category_id == 1 ? "MAIN COURSE" : "SIGNATURE") 
                                .font(.system(size: 12, weight: .bold))
                                .tracking(2)
                                .foregroundColor(.primaryBrand)
                                .padding(.top, 24)
                            Text(menu.name)
                                .font(.brandDisplay(36))
                                .foregroundColor(.white)
                                .lineLimit(2)
                            Text(menu.formattedPrice)
                                .font(.system(size: 24, weight: .light))
                                .foregroundColor(Color(hex: "CCCCCC"))
                                .padding(.bottom, 24)
                            Rectangle()
                                .fill(Color(hex: "222222"))
                                .frame(height: 1)
                                .padding(.bottom, 24)
                            Text("DESCRIPTION")
                                .font(.system(size: 12, weight: .bold))
                                .tracking(1.5)
                                .foregroundColor(Color(hex: "666666"))
                                .padding(.bottom, 8)
                            Text(menu.description ?? "A masterpiece of culinary art, prepared with passion and the finest ingredients to delight your senses.")
                                .font(.system(size: 16, weight: .light))
                                .foregroundColor(Color(hex: "999999"))
                                .lineSpacing(6)
                                .fixedSize(horizontal: false, vertical: true)
                        }
                        .padding(.horizontal, 24)
                        Spacer(minLength: 150) 
                    }
                    .background(Color.blackBrand)
                    .clipShape(RoundedCorner(radius: 32, corners: [.topLeft, .topRight]))
                }
            }
            .coordinateSpace(name: "scroll")
            GeometryReader { geo in
                let offset = geo.frame(in: .global).minY
                ZStack {
                    if let urlString = menu.image_url, let url = URL(string: urlString) {
                        AsyncImage(url: url) { phase in
                            switch phase {
                            case .success(let image):
                                image.resizable().aspectRatio(contentMode: .fill)
                            default:
                                Color.gray
                            }
                        }
                    } else {
                        Color.gray
                    }
                    LinearGradient(
                        colors: [.black.opacity(0.4), .clear, .blackBrand.opacity(0.8), .blackBrand],
                        startPoint: .top,
                        endPoint: .bottom
                    )
                }
                .frame(width: UIScreen.main.bounds.width, height: UIScreen.main.bounds.height * 0.5 + (offset > 0 ? offset : 0))
                .offset(y: offset > 0 ? -offset : 0)
                .ignoresSafeArea()
            }
            .zIndex(-1)
            VStack {
                HStack {
                    Button(action: { presentationMode.wrappedValue.dismiss() }) {
                        Image(systemName: "arrow.backward")
                            .font(.system(size: 20, weight: .bold))
                            .foregroundColor(.white)
                            .frame(width: 44, height: 44)
                            .background(Color.black.opacity(0.3))
                            .clipShape(Circle())
                    }
                    Spacer()
                    Button(action: { isFavorite.toggle() }) {
                        Image(systemName: isFavorite ? "heart.fill" : "heart")
                            .font(.system(size: 20, weight: .bold))
                            .foregroundColor(isFavorite ? .red : .white)
                            .frame(width: 44, height: 44)
                            .background(Color.black.opacity(0.3))
                            .clipShape(Circle())
                    }
                }
                .padding(.top, 50) 
                .padding(.horizontal, 20)
                Spacer()
            }
            VStack {
                Spacer()
                HStack(spacing: 20) {
                    HStack(spacing: 12) {
                        Button(action: { if quantity > 1 { quantity -= 1 } }) {
                            Image(systemName: "minus.circle")
                                .font(.system(size: 28))
                                .foregroundColor(.white)
                        }
                        Text("\(quantity)")
                            .font(.system(size: 18, weight: .bold))
                            .foregroundColor(.white)
                            .frame(minWidth: 20)
                        Button(action: { if quantity < 20 { quantity += 1 } }) {
                            Image(systemName: "plus.circle")
                                .font(.system(size: 28))
                                .foregroundColor(.white)
                        }
                    }
                    Button(action: handleAddToCart) {
                        HStack {
                            if isAddingToCart {
                                ProgressView().tint(.black)
                            } else {
                                Text("Add to Cart")
                                Text("•")
                                Text(formatPrice(menu.price * Double(quantity)))
                            }
                        }
                        .font(.system(size: 16, weight: .bold))
                        .foregroundColor(.blackBrand)
                        .frame(maxWidth: .infinity)
                        .frame(height: 50)
                        .background(Color.primaryBrand)
                        .cornerRadius(25)
                    }
                    .disabled(isAddingToCart)
                }
                .padding(20)
                .background(
                    BlurView(style: .systemUltraThinMaterialDark)
                        .clipShape(RoundedRectangle(cornerRadius: 30))
                        .overlay(RoundedRectangle(cornerRadius: 30).stroke(Color(hex: "333333"), lineWidth: 1))
                )
                .padding(.horizontal, 20)
                .padding(.bottom, 30)
            }
        }
        .navigationBarHidden(true)
    }
    func formatPrice(_ price: Double) -> String {
        let formatter = NumberFormatter()
        formatter.numberStyle = .currency
        formatter.locale = Locale(identifier: "id_ID")
        formatter.currencySymbol = "Rp "
        formatter.maximumFractionDigits = 0
        return formatter.string(from: NSNumber(value: price)) ?? "Rp \(Int(price))"
    }
    func handleAddToCart() {
        isAddingToCart = true
        DispatchQueue.main.asyncAfter(deadline: .now() + 1.0) {
            isAddingToCart = false
            presentationMode.wrappedValue.dismiss()
        }
    }
}
struct BlurView: UIViewRepresentable {
    let style: UIBlurEffect.Style
    func makeUIView(context: Context) -> UIVisualEffectView {
        return UIVisualEffectView(effect: UIBlurEffect(style: style))
    }
    func updateUIView(_ uiView: UIVisualEffectView, context: Context) {}
}