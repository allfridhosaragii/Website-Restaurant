import Foundation
@MainActor
class FavoritesViewModel: ObservableObject {
    @Published var favorites: [FavoriteItem] = []
    @Published var isLoading: Bool = true
    @Published var error: String?
    struct FavoriteItem: Identifiable {
        let id: Int
        let menu: Menu
    }
    func fetchFavorites() async {
        isLoading = true
        error = nil
        do {
            let response = try await FavoritesAPI.shared.getAll()
            self.favorites = response.favorites.compactMap { fav in
                guard let apiMenu = fav.menu else { return nil }
                return FavoriteItem(
                    id: fav.id,
                    menu: Menu(
                        id: apiMenu.id,
                        name: apiMenu.name,
                        description: apiMenu.description,
                        price: apiMenu.price,
                        image_url: apiMenu.imageUrl,
                        category_id: 1,
                        is_available: apiMenu.isAvailable ?? true,
                        stock: apiMenu.stock ?? 0
                    )
                )
            }
        } catch {
            self.error = error.localizedDescription
            self.favorites = [
                FavoriteItem(id: 1, menu: Menu(id: 1, name: "Wagyu Steak", description: nil, price: 150000, image_url: "https://images.unsplash.com/photo-1546241072-48010ad2862c?w=500", category_id: 1, is_available: true, stock: 10)),
            ]
        }
        isLoading = false
    }
    func toggleFavorite(menuId: Int) async {
        do {
            _ = try await FavoritesAPI.shared.toggle(menuId: menuId)
            await fetchFavorites()
        } catch {
            self.error = error.localizedDescription
        }
    }
}