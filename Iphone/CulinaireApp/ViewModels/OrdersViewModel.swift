import Foundation
@MainActor
class OrdersViewModel: ObservableObject {
    @Published var orders: [OrderData] = []
    @Published var reservations: [ReservationData] = []
    @Published var isLoading: Bool = true
    @Published var error: String?
    struct OrderData: Identifiable {
        let id: Int
        let orderNumber: String
        let date: String
        let status: String
        let total: Double
        let itemsCount: Int
    }
    struct ReservationData: Identifiable {
        let id: Int
        let date: String
        let time: String
        let guests: Int
        let tableNumber: Int
        let status: String
        let depositAmount: Double
        let depositStatus: String
    }
    func fetchOrders() async {
        isLoading = true
        error = nil
        do {
            let response = try await OrderAPI.shared.getAll()
            self.orders = response.orders.map { order in
                OrderData(
                    id: order.id,
                    orderNumber: order.orderNumber ?? "ORD-\(order.id)",
                    date: order.createdAt ?? "-",
                    status: order.status,
                    total: order.total,
                    itemsCount: order.items?.count ?? 0
                )
            }
        } catch {
            self.error = error.localizedDescription
            self.orders = [
                OrderData(id: 1, orderNumber: "ORD-001", date: "22 Jan 2026", status: "Selesai", total: 350000, itemsCount: 3),
                OrderData(id: 2, orderNumber: "ORD-002", date: "20 Jan 2026", status: "Dibatalkan", total: 120000, itemsCount: 2)
            ]
        }
        isLoading = false
    }
    func fetchReservations() async {
        do {
            let response = try await ReservationAPI.shared.getAll()
            self.reservations = response.reservations.map { res in
                ReservationData(
                    id: res.id,
                    date: res.reservationDate ?? "-",
                    time: res.reservationTime ?? "-",
                    guests: res.guests ?? 2,
                    tableNumber: res.tableNumber ?? 0,
                    status: res.status,
                    depositAmount: res.depositAmount ?? 150000,
                    depositStatus: res.depositStatus ?? "pending"
                )
            }
        } catch {
            self.reservations = [
                ReservationData(id: 1, date: "25 Jan 2026", time: "19:00", guests: 2, tableNumber: 5, status: "Confirmed", depositAmount: 150000, depositStatus: "paid"),
            ]
        }
    }
    func fetchAll() async {
        await fetchOrders()
        await fetchReservations()
    }
}