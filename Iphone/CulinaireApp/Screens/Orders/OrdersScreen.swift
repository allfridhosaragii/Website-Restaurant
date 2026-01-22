import SwiftUI
struct OrdersScreen: View {
    @Environment(\.presentationMode) var presentationMode
    @State private var selectedTab: Int = 0 
    @State private var orders: [MockOrder] = []
    @State private var reservations: [MockReservation] = []
    var body: some View {
        ZStack {
            Color(hex: "F9F9F9").ignoresSafeArea()
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
                    Text("Histori")
                        .font(.system(size: 18, weight: .bold))
                        .foregroundColor(.blackBrand)
                    Spacer()
                    Color.clear.frame(width: 40)
                }
                .padding()
                .background(Color.white)
                HStack(spacing: 0) {
                    TabButton(title: "Pesanan Menu", isSelected: selectedTab == 0) {
                        withAnimation { selectedTab = 0 }
                    }
                    TabButton(title: "Reservasi", isSelected: selectedTab == 1) {
                        withAnimation { selectedTab = 1 }
                    }
                }
                .padding(4)
                .background(Color(hex: "F5F5F5"))
                .cornerRadius(12)
                .padding()
                ScrollView {
                    VStack(spacing: 16) {
                        if selectedTab == 0 {
                            if orders.isEmpty {
                                EmptyStateView(tab: "Pesanan Menu")
                            } else {
                                ForEach(orders) { order in
                                    NavigationLink(destination: OrderDetailScreen(order: order)) {
                                        OrderCard(order: order)
                                    }
                                    .buttonStyle(PlainButtonStyle())
                                }
                            }
                        } else {
                            if reservations.isEmpty {
                                EmptyStateView(tab: "Reservasi")
                            } else {
                                ForEach(reservations) { reservation in
                                    ReservationCard(reservation: reservation)
                                }
                            }
                        }
                    }
                    .padding(20)
                }
            }
        }
        .navigationBarHidden(true)
        .onAppear(perform: loadData)
    }
    func loadData() {
        orders = [
            MockOrder(id: "ORD-001", date: "22 Jan 2026, 19:00", status: "Selesai", total: 350000, items: 3),
            MockOrder(id: "ORD-002", date: "20 Jan 2026, 12:30", status: "Dibatalkan", total: 120000, items: 2)
        ]
        reservations = [
            MockReservation(id: "RSV-999", date: "25 Jan 2026", time: "19:00", guests: 2, table: 5, status: "Confirmed", deposit: 150000),
            MockReservation(id: "RSV-888", date: "10 Jan 2026", time: "20:00", guests: 4, table: 22, status: "Completed", deposit: 150000)
        ]
    }
}
struct TabButton: View {
    let title: String
    let isSelected: Bool
    let action: () -> Void
    var body: some View {
        Button(action: action) {
            Text(title)
                .font(.system(size: 14, weight: isSelected ? .bold : .medium))
                .foregroundColor(isSelected ? .primaryBrand : .gray)
                .frame(maxWidth: .infinity)
                .padding(.vertical, 10)
                .background(isSelected ? Color.white : Color.clear)
                .cornerRadius(8)
                .shadow(color: isSelected ? Color.black.opacity(0.1) : .clear, radius: 2)
        }
    }
}
struct EmptyStateView: View {
    let tab: String
    var body: some View {
        VStack(spacing: 16) {
            Image(systemName: "doc.text.magnifyingglass")
                .font(.system(size: 50))
                .foregroundColor(.gray)
            Text("Belum ada \(tab)")
                .foregroundColor(.gray)
        }
        .padding(.top, 50)
    }
}
struct OrderCard: View {
    let order: MockOrder
    var statusColor: Color {
        switch order.status {
            case "Selesai": return .green
            case "Dibatalkan": return .red
            default: return .orange
        }
    }
    var body: some View {
        VStack(spacing: 12) {
            HStack {
                VStack(alignment: .leading) {
                    Text("#\(order.id)")
                        .font(.system(size: 16, weight: .bold))
                        .foregroundColor(.textPrimary)
                    Text(order.date)
                        .font(.system(size: 12))
                        .foregroundColor(.textSecondary)
                }
                Spacer()
                Text(order.status)
                    .font(.system(size: 11, weight: .bold))
                    .foregroundColor(statusColor)
                    .padding(.horizontal, 10)
                    .padding(.vertical, 4)
                    .background(statusColor.opacity(0.1))
                    .cornerRadius(20)
            }
            Divider()
            HStack {
                Text("Total: Rp \(order.total)")
                    .font(.system(size: 14, weight: .bold))
                    .foregroundColor(.primaryBrand)
                Spacer()
                Text("\(order.items) Items")
                    .font(.system(size: 12))
                    .foregroundColor(.textSecondary)
            }
        }
        .padding(16)
        .background(Color.white)
        .cornerRadius(12)
        .shadow(color: Color.black.opacity(0.05), radius: 4)
    }
}
struct ReservationCard: View {
    let reservation: MockReservation
    var body: some View {
        VStack(spacing: 12) {
            HStack {
                Image(systemName: "calendar")
                    .foregroundColor(.primaryBrand)
                VStack(alignment: .leading) {
                    Text(reservation.id)
                        .font(.system(size: 16, weight: .bold))
                    Text(reservation.date)
                        .font(.system(size: 12))
                        .foregroundColor(.gray)
                }
                Spacer()
                Text(reservation.status)
                    .font(.system(size: 11, weight: .bold))
                    .foregroundColor(.blue)
                    .padding(.horizontal, 10)
                    .padding(.vertical, 4)
                    .background(Color.blue.opacity(0.1))
                    .cornerRadius(20)
            }
            Divider()
            HStack(spacing: 16) {
                LabelIcon(icon: "clock", text: reservation.time)
                LabelIcon(icon: "person.2", text: "\(reservation.guests) Orang")
                LabelIcon(icon: "tablecells", text: "Meja \(reservation.table)")
            }
            HStack {
                Text("Deposit Paid")
                    .font(.caption)
                    .fontWeight(.bold)
                    .foregroundColor(.gray)
                Spacer()
                Text("Rp \(reservation.deposit)")
                    .font(.caption)
                    .fontWeight(.bold)
                    .foregroundColor(.green)
            }
            .padding(8)
            .background(Color.gray.opacity(0.05))
            .cornerRadius(8)
        }
        .padding(16)
        .background(Color.white)
        .cornerRadius(12)
        .shadow(color: Color.black.opacity(0.05), radius: 4)
    }
}
struct LabelIcon: View {
    let icon: String
    let text: String
    var body: some View {
        HStack(spacing: 4) {
            Image(systemName: icon)
                .font(.system(size: 12))
                .foregroundColor(.gray)
            Text(text)
                .font(.system(size: 12))
                .foregroundColor(.black)
        }
    }
}
struct MockOrder: Identifiable {
    let id: String
    let date: String
    let status: String
    let total: Int
    let items: Int
}
struct MockReservation: Identifiable {
    let id: String
    let date: String
    let time: String
    let guests: Int
    let table: Int
    let status: String
    let deposit: Int
}