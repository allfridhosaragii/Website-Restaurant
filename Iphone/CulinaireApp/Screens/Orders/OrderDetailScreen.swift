import SwiftUI
struct OrderDetailScreen: View {
    let order: MockOrder
    @Environment(\.presentationMode) var presentationMode
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
                    Text("Detail Pesanan")
                        .font(.system(size: 18, weight: .bold))
                        .foregroundColor(.blackBrand)
                    Spacer()
                    Color.clear.frame(width: 40)
                }
                .padding()
                .background(Color.white)
                ScrollView {
                    VStack(spacing: 24) {
                        VStack(spacing: 12) {
                            Text("#\(order.id)")
                                .font(.system(size: 24, weight: .bold))
                                .foregroundColor(.textPrimary)
                            HStack {
                                Text(order.status)
                                    .font(.system(size: 14, weight: .bold))
                                    .foregroundColor(.white)
                                    .padding(.horizontal, 16)
                                    .padding(.vertical, 6)
                                    .background(order.status == "Selesai" ? Color.green : Color.red)
                                    .cornerRadius(20)
                            }
                            Text(order.date)
                                .font(.system(size: 14))
                                .foregroundColor(.textSecondary)
                        }
                        .padding()
                        .frame(maxWidth: .infinity)
                        .background(Color.white)
                        .cornerRadius(16)
                        VStack(alignment: .leading, spacing: 16) {
                            Text("Item Pesanan")
                                .font(.headline)
                            VStack(spacing: 0) {
                                MockItemRow(name: "Wagyu Steak", qty: 2, price: 300000)
                                Divider().padding(.vertical, 12)
                                MockItemRow(name: "Lemon Tea", qty: 1, price: 50000)
                            }
                        }
                        .padding()
                        .background(Color.white)
                        .cornerRadius(16)
                        VStack(alignment: .leading, spacing: 12) {
                            Text("Ringkasan Pembayaran")
                                .font(.headline)
                            HStack {
                                Text("Subtotal")
                                    .foregroundColor(.gray)
                                Spacer()
                                Text("Rp 350.000")
                            }
                            HStack {
                                Text("Pajak (10%)")
                                    .foregroundColor(.gray)
                                Spacer()
                                Text("Rp 35.000")
                            }
                            Divider()
                            HStack {
                                Text("Total")
                                    .fontWeight(.bold)
                                Spacer()
                                Text("Rp 385.000")
                                    .fontWeight(.bold)
                                    .foregroundColor(.primaryBrand)
                            }
                        }
                        .padding()
                        .background(Color.white)
                        .cornerRadius(16)
                    }
                    .padding(20)
                }
            }
        }
        .navigationBarHidden(true)
    }
}
struct MockItemRow: View {
    let name: String
    let qty: Int
    let price: Int
    var body: some View {
        HStack {
            VStack(alignment: .leading) {
                Text(name)
                    .font(.system(size: 16, weight: .medium))
                Text("x\(qty)")
                    .font(.system(size: 14))
                    .foregroundColor(.gray)
            }
            Spacer()
            Text("Rp \(price)")
                .font(.system(size: 16, weight: .semibold))
        }
    }
}