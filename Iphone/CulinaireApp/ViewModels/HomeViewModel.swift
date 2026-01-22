import Foundation
import Combine
class HomeViewModel: ObservableObject {
    @Published var popularMenus: [Menu] = []
    @Published var isLoadingMenus: Bool = true
    @Published var dashboardStats: DashboardStats = DashboardStats(points: 0, totalOrders: 0, totalReservations: 0, totalFavorites: 0)
    @Published var isRefreshing: Bool = false
    @Published var userName: String = "Tamu Culinaire"
    @Published var error: String?
    struct DashboardStats {
        var points: Int
        var totalOrders: Int
        var totalReservations: Int
        var totalFavorites: Int
    }
    func fetchData() {
        isLoadingMenus = true
        error = nil
        Task { @MainActor in
            do {
                let menuResponse = try await MenuAPI.shared.getAll()
                self.popularMenus = Array(menuResponse.menus.prefix(5)).map { apiMenu in
                    Menu(
                        id: apiMenu.id,
                        name: apiMenu.name,
                        description: apiMenu.description,
                        price: apiMenu.price,
                        image_url: apiMenu.imageUrl,
                        category_id: 1,
                        is_available: apiMenu.isAvailable ?? true,
                        stock: apiMenu.stock ?? 0
                    )
                }
            } catch {
                self.popularMenus = [
                    Menu(id: 1, name: "Wagyu Steak", description: nil, price: 150000, image_url: "https://images.unsplash.com/photo-1546241072-48010ad2862c?w=500", category_id: 1, is_available: true, stock: 10),
                    Menu(id: 2, name: "Salmon Grill", description: nil, price: 120000, image_url: "https://images.unsplash.com/photo-1519708227418-e8d316e890f6?w=500", category_id: 1, is_available: true, stock: 5),
                ]
            }
            do {
                let stats = try await DashboardAPI.shared.getStats()
                self.dashboardStats = DashboardStats(
                    points: stats.points,
                    totalOrders: stats.totalOrders,
                    totalReservations: stats.totalReservations,
                    totalFavorites: stats.totalFavorites
                )
            } catch {
                self.dashboardStats = DashboardStats(points: 0, totalOrders: 0, totalReservations: 0, totalFavorites: 0)
            }
            self.isLoadingMenus = false
            self.isRefreshing = false
        }
    }
    func refresh() {
        isRefreshing = true
        fetchData()
    }
}