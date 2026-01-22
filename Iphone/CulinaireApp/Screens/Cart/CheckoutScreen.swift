import SwiftUI
struct CheckoutScreen: View {
    @Environment(\.presentationMode) var presentationMode
    @State private var isProcessing: Bool = false
    @State private var showSuccess: Bool = false
    let total = 450000.0 
    var body: some View {
        ZStack {
            Color.backgroundBrand.ignoresSafeArea()
            VStack(spacing: 0) {
                HStack {
                    Button(action: { presentationMode.wrappedValue.dismiss() }) {
                        Image(systemName: "arrow.backward")
                            .font(.system(size: 20, weight: .bold))
                            .foregroundColor(.blackBrand)
                            .padding(8)
                            .background(Color.white)
                            .clipShape(Circle())
                    }
                    Spacer()
                    Text("Checkout")
                        .font(.system(size: 18, weight: .bold))
                        .foregroundColor(.blackBrand)
                    Spacer()
                    Color.clear.frame(width: 40) 
                }
                .padding()
                ScrollView {
                    VStack(spacing: 24) {
                        VStack(alignment: .leading, spacing: 16) {
                            Text("Ringkasan Pesanan")
                                .font(.headline)
                            VStack(spacing: 12) {
                                OrderRow(name: "Wagyu Steak", qty: 2, price: 300000)
                                OrderRow(name: "Caesar Salad", qty: 1, price: 75000)
                                OrderRow(name: "Lemon Tea", qty: 3, price: 75000)
                            }
                            Divider()
                            HStack {
                                Text("Total Tagihan")
                                    .font(.headline)
                                Spacer()
                                Text("Rp 450.000")
                                    .font(.title3)
                                    .fontWeight(.bold)
                                    .foregroundColor(.primaryBrand)
                            }
                        }
                        .padding()
                        .background(Color.white)
                        .cornerRadius(16)
                        .shadow(color: Color.black.opacity(0.05), radius: 5)
                        VStack(alignment: .leading, spacing: 16) {
                            Text("Metode Pembayaran")
                                .font(.headline)
                            PaymentOption(icon: "creditcard.fill", title: "Kartu Kredit/Debit", selected: true)
                            PaymentOption(icon: "banknote.fill", title: "Transfer Bank / VA", selected: false)
                            PaymentOption(icon: "qrcode", title: "QRIS", selected: false)
                        }
                        .padding()
                        .background(Color.white)
                        .cornerRadius(16)
                        .shadow(color: Color.black.opacity(0.05), radius: 5)
                    }
                    .padding(20)
                }
                VStack {
                    Button(action: processPayment) {
                        HStack {
                            if isProcessing {
                                ProgressView().tint(.white)
                            } else {
                                Image(systemName: "lock.fill")
                                Text("Bayar Sekarang • Rp 450.000")
                            }
                        }
                        .font(.system(size: 16, weight: .bold))
                        .foregroundColor(.white)
                        .frame(maxWidth: .infinity)
                        .frame(height: 56)
                        .background(Color.primaryBrand)
                        .cornerRadius(16)
                    }
                    .disabled(isProcessing)
                }
                .padding(20)
                .background(Color.white)
                .shadow(color: Color.black.opacity(0.05), radius: -5)
            }
            if showSuccess {
                SuccessOverlay {
                    presentationMode.wrappedValue.dismiss()
                }
            }
        }
        .navigationBarHidden(true)
    }
    func processPayment() {
        isProcessing = true
        DispatchQueue.main.asyncAfter(deadline: .now() + 2.0) {
            isProcessing = false
            withAnimation {
                showSuccess = true
            }
        }
    }
}
struct OrderRow: View {
    let name: String
    let qty: Int
    let price: Double
    var body: some View {
        HStack {
            Text("\(qty)x")
                .font(.system(size: 14, weight: .bold))
                .foregroundColor(.primaryBrand)
                .frame(width: 30)
            Text(name)
                .font(.system(size: 14))
                .foregroundColor(.textPrimary)
            Spacer()
            Text("Rp \(Int(price))")
                .font(.system(size: 14, weight: .semibold))
        }
    }
}
struct PaymentOption: View {
    let icon: String
    let title: String
    let selected: Bool
    var body: some View {
        HStack {
            Image(systemName: icon)
                .font(.system(size: 20))
                .foregroundColor(selected ? .primaryBrand : .gray)
                .frame(width: 30)
            Text(title)
                .font(.system(size: 14, weight: .medium))
                .foregroundColor(selected ? .black : .gray)
            Spacer()
            if selected {
                Image(systemName: "checkmark.circle.fill")
                    .foregroundColor(.primaryBrand)
            } else {
                Image(systemName: "circle")
                    .foregroundColor(.gray)
            }
        }
        .padding()
        .background(selected ? Color.primaryBrand.opacity(0.05) : Color.white)
        .cornerRadius(12)
        .overlay(RoundedRectangle(cornerRadius: 12).stroke(selected ? Color.primaryBrand : Color.gray.opacity(0.3), lineWidth: 1))
    }
}