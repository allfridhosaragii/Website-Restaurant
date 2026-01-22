import SwiftUI
struct FloatingCartButton: View {
    let itemCount: Int
    let totalPrice: Double
    let action: () -> Void
    var body: some View {
        if itemCount > 0 {
            Button(action: action) {
                HStack(spacing: 12) {
                    ZStack {
                        Circle()
                            .fill(Color.white.opacity(0.2))
                            .frame(width: 36, height: 36)
                        Text("\(itemCount)")
                            .font(.system(size: 14, weight: .bold))
                            .foregroundColor(.white)
                    }
                    VStack(alignment: .leading, spacing: 0) {
                        Text("Total")
                            .font(.system(size: 10))
                            .foregroundColor(.white.opacity(0.8))
                        Text(formatPrice(totalPrice))
                            .font(.system(size: 14, weight: .bold))
                            .foregroundColor(.white)
                    }
                    Spacer()
                    Image(systemName: "basket.fill")
                        .font(.system(size: 20))
                        .foregroundColor(.white)
                }
                .padding(.horizontal, 16)
                .padding(.vertical, 12)
                .background(Color.primaryBrand)
                .cornerRadius(30)
                .shadow(color: Color.primaryBrand.opacity(0.4), radius: 8, x: 0, y: 4)
            }
            .padding(.horizontal, 20)
            .padding(.bottom, 100) 
            .transition(.scale.combined(with: .opacity))
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