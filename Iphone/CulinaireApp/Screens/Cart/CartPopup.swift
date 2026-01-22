import SwiftUI
struct CartPopup: View {
    @Binding var isPresented: Bool
    @ObservedObject var viewModel: MenuViewModel 
    var onCheckout: () -> Void
    @State private var dragOffset: CGFloat = 0
    var body: some View {
        ZStack(alignment: .bottom) {
            if isPresented {
                Color.black.opacity(0.5)
                    .ignoresSafeArea()
                    .onTapGesture {
                        withAnimation { isPresented = false }
                    }
                    .transition(.opacity)
                VStack(spacing: 0) {
                    VStack {
                        Capsule()
                            .fill(Color(hex: "D1D5DB"))
                            .frame(width: 40, height: 4)
                            .padding(.top, 12)
                            .padding(.bottom, 16)
                        HStack {
                            Text("Keranjang (\(viewModel.cartCount) item)")
                                .font(.system(size: 20, weight: .bold))
                                .foregroundColor(.textPrimary)
                            Spacer()
                            Button(action: { withAnimation { isPresented = false } }) {
                                Image(systemName: "xmark.circle.fill")
                                    .font(.system(size: 24))
                                    .foregroundColor(Color(hex: "E5E7EB"))
                            }
                        }
                        .padding(.horizontal, 20)
                        .padding(.bottom, 16)
                        Divider()
                    }
                    .background(Color.white)
                    if viewModel.cartItems.isEmpty {
                        VStack(spacing: 16) {
                            Text("🛒")
                                .font(.system(size: 60))
                            Text("Keranjang masih kosong")
                                .font(.system(size: 16))
                                .foregroundColor(.textSecondary)
                        }
                        .frame(maxWidth: .infinity, maxHeight: .infinity)
                        .padding(.vertical, 40)
                    } else {
                        ScrollView {
                            VStack(spacing: 16) {
                                ForEach(viewModel.cartItems) { item in
                                    CartItemRow(item: item, onRemove: {
                                    })
                                }
                            }
                            .padding(20)
                        }
                    }
                    if !viewModel.cartItems.isEmpty {
                        VStack {
                            HStack {
                                Text("Total")
                                    .font(.system(size: 16))
                                    .foregroundColor(.textSecondary)
                                Spacer()
                                Text(formatPrice(viewModel.cartTotal))
                                    .font(.system(size: 24, weight: .bold))
                                    .foregroundColor(.textPrimary)
                            }
                            .padding(.bottom, 16)
                            Button(action: onCheckout) {
                                HStack {
                                    Image(systemName: "cart")
                                    Text("Checkout Sekarang")
                                }
                                .font(.system(size: 16, weight: .bold))
                                .foregroundColor(.white)
                                .frame(maxWidth: .infinity)
                                .frame(height: 56)
                                .background(LinearGradient(colors: [.primaryBrand, Color(hex: "6B0F2A")], startPoint: .leading, endPoint: .trailing))
                                .cornerRadius(16)
                            }
                        }
                        .padding(20)
                        .background(Color.white)
                        .shadow(color: Color.black.opacity(0.05), radius: -4, y: 0)
                    }
                }
                .frame(maxHeight: UIScreen.main.bounds.height * 0.8)
                .background(Color.white)
                .clipShape(RoundedCorner(radius: 24, corners: [.topLeft, .topRight]))
                .transition(.move(edge: .bottom))
                .offset(y: dragOffset)
                .gesture(
                    DragGesture()
                        .onChanged { value in
                            if value.translation.height > 0 {
                                dragOffset = value.translation.height
                            }
                        }
                        .onEnded { value in
                            if value.translation.height > 100 {
                                withAnimation { isPresented = false }
                            }
                            withAnimation { dragOffset = 0 }
                        }
                )
            }
        }
    }
    func formatPrice(_ price: Double) -> String {
        let formatter = NumberFormatter()
        formatter.numberStyle = .currency
        formatter.locale = Locale(identifier: "id_ID")
        formatter.currencySymbol = "Rp "
        formatter.maximumFractionDigits = 0
        return formatter.string(from: NSNumber(value: price)) ?? "Rp \(Int(price))"
    }
}
struct CartItemRow: View {
    let item: Menu
    let onRemove: () -> Void
    var body: some View {
        HStack(spacing: 12) {
            AsyncImage(url: URL(string: item.image_url ?? "")) { image in
                image.resizable().aspectRatio(contentMode: .cover)
            } placeholder: {
                Color(hex: "E5E7EB")
            }
            .frame(width: 70, height: 70)
            .cornerRadius(10)
            VStack(alignment: .leading, spacing: 4) {
                Text(item.name)
                    .font(.system(size: 15, weight: .semibold))
                    .foregroundColor(.textPrimary)
                    .lineLimit(1)
                Text(formatPrice(item.price))
                    .font(.system(size: 14, weight: .bold))
                    .foregroundColor(.primaryBrand)
            }
            Spacer()
            HStack(spacing: 0) {
                Button("-") {}.frame(width: 30, height: 30).background(Color.primaryBrand).foregroundColor(.white).cornerRadius(6)
                Text("1").font(.system(size: 14, weight: .bold)).frame(width: 30)
                Button("+") {}.frame(width: 30, height: 30).background(Color.primaryBrand).foregroundColor(.white).cornerRadius(6)
            }
            Button(action: onRemove) {
                Image(systemName: "trash")
                    .foregroundColor(.red)
                    .padding(8)
            }
        }
        .padding(12)
        .background(Color(hex: "F9FAFB"))
        .cornerRadius(12)
    }
    func formatPrice(_ price: Double) -> String {
        let formatter = NumberFormatter()
        formatter.numberStyle = .currency
        formatter.locale = Locale(identifier: "id_ID")
        formatter.currencySymbol = "Rp "
        formatter.maximumFractionDigits = 0
        return formatter.string(from: NSNumber(value: price)) ?? "Rp \(Int(price))"
    }
}