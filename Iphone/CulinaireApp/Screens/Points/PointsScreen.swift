import SwiftUI
struct PointsScreen: View {
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
                    Text("Loyalty Points")
                        .font(.system(size: 18, weight: .bold))
                        .foregroundColor(.blackBrand)
                    Spacer()
                    Color.clear.frame(width: 40)
                }
                .padding()
                ScrollView {
                    VStack(spacing: 24) {
                        ZStack {
                            RoundedRectangle(cornerRadius: 24)
                                .fill(LinearGradient(colors: [.primaryBrand, Color(hex: "4A0A1C")], startPoint: .topLeading, endPoint: .bottomTrailing))
                                .frame(height: 200)
                                .shadow(color: .primaryBrand.opacity(0.4), radius: 10, y: 5)
                            Circle().fill(Color.white.opacity(0.05)).frame(width: 300).offset(x: 100, y: -50)
                            Circle().fill(Color.white.opacity(0.05)).frame(width: 200).offset(x: -100, y: 80)
                            VStack(alignment: .leading) {
                                HStack {
                                    Image(systemName: "crown.fill")
                                        .foregroundColor(.gold)
                                    Text("Culinaire Platinum")
                                        .font(.system(size: 16, weight: .bold))
                                        .foregroundColor(.white)
                                    Spacer()
                                    Text("MEMBER")
                                        .font(.system(size: 12, weight: .bold))
                                        .foregroundColor(.white.opacity(0.7))
                                        .padding(.horizontal, 8)
                                        .padding(.vertical, 4)
                                        .overlay(RoundedRectangle(cornerRadius: 4).stroke(Color.white.opacity(0.5), lineWidth: 1))
                                }
                                Spacer()
                                Text("1,250")
                                    .font(.system(size: 48, weight: .bold))
                                    .foregroundColor(.white)
                                Text("Total Poin Anda")
                                    .font(.system(size: 14))
                                    .foregroundColor(.white.opacity(0.8))
                                Spacer()
                                Text("Exp: 31 Dec 2026")
                                    .font(.system(size: 12))
                                    .foregroundColor(.white.opacity(0.6))
                            }
                            .padding(24)
                        }
                        VStack(alignment: .leading, spacing: 16) {
                            Text("Riwayat Poin")
                                .font(.headline)
                            VStack(spacing: 0) {
                                PointHistoryRow(title: "Pemesanan Wagyu Steak", date: "22 Jan 2026", points: "+150", isGain: true)
                                Divider().padding(.leading, 16)
                                PointHistoryRow(title: "Penukaran Voucher", date: "10 Jan 2026", points: "-500", isGain: false)
                                Divider().padding(.leading, 16)
                                PointHistoryRow(title: "Bonus Ulang Tahun", date: "01 Jan 2026", points: "+200", isGain: true)
                            }
                            .background(Color.white)
                            .cornerRadius(16)
                        }
                    }
                    .padding(20)
                }
            }
        }
        .navigationBarHidden(true)
    }
}
struct PointHistoryRow: View {
    let title: String
    let date: String
    let points: String
    let isGain: Bool
    var body: some View {
        HStack {
            Circle()
                .fill(isGain ? Color.green.opacity(0.1) : Color.red.opacity(0.1))
                .frame(width: 40, height: 40)
                .overlay(
                    Image(systemName: isGain ? "arrow.down.left" : "arrow.up.right")
                        .foregroundColor(isGain ? .green : .red)
                        .font(.system(size: 14, weight: .bold))
                )
            VStack(alignment: .leading, spacing: 2) {
                Text(title)
                    .font(.system(size: 14, weight: .medium))
                    .foregroundColor(.textPrimary)
                Text(date)
                    .font(.system(size: 12))
                    .foregroundColor(.textSecondary)
            }
            Spacer()
            Text(points)
                .font(.system(size: 16, weight: .bold))
                .foregroundColor(isGain ? .green : .textPrimary)
        }
        .padding(16)
    }
}