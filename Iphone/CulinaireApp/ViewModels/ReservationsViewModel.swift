import Foundation
@MainActor
class ReservationsViewModel: ObservableObject {
    @Published var tables: [TableData] = []
    @Published var isLoading: Bool = true
    @Published var isSubmitting: Bool = false
    @Published var error: String?
    @Published var successMessage: String?
    struct TableData: Identifiable {
        let id: Int
        let number: Int
        let capacity: Int
        let shape: String
        let status: String
        let isPremium: Bool
    }
    func fetchTables() async {
        isLoading = true
        error = nil
        do {
            let response = try await TablesAPI.shared.getAll()
            self.tables = response.tables.map { table in
                TableData(
                    id: table.id,
                    number: table.number,
                    capacity: table.capacity,
                    shape: table.shape ?? "square",
                    status: table.status,
                    isPremium: table.isPremium ?? false
                )
            }
        } catch {
            self.error = error.localizedDescription
            self.tables = (1...20).map { i in
                TableData(
                    id: i,
                    number: i,
                    capacity: i <= 5 ? 4 : 2,
                    shape: i <= 2 ? "round" : "square",
                    status: [6, 12, 18].contains(i) ? "booked" : "available",
                    isPremium: i <= 5
                )
            }
        }
        isLoading = false
    }
    func createReservation(
        tableId: Int,
        name: String,
        phone: String,
        email: String,
        date: Date,
        time: Date,
        guests: Int
    ) async -> Bool {
        isSubmitting = true
        error = nil
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "yyyy-MM-dd"
        let timeFormatter = DateFormatter()
        timeFormatter.dateFormat = "HH:mm"
        let data: [String: Any] = [
            "table_id": tableId,
            "name": name,
            "phone": phone,
            "email": email,
            "reservation_date": dateFormatter.string(from: date),
            "reservation_time": timeFormatter.string(from: time),
            "guests": guests
        ]
        do {
            _ = try await ReservationAPI.shared.create(data: data)
            successMessage = "Reservasi berhasil dibuat!"
            isSubmitting = false
            return true
        } catch {
            self.error = error.localizedDescription
            isSubmitting = false
            return false
        }
    }
}