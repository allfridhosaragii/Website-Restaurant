import Foundation
import Combine
class MenuViewModel: ObservableObject {
    @Published var menus: [Menu] = []
    @Published var filteredMenus: [Menu] = []
    @Published var categories: [String] = ["Semua"]
    @Published var isLoading: Bool = true
    @Published var searchText: String = ""
    @Published var selectedCategory: String = "Semua"
    @Published var error: String?
    @Published var cartItems: [CartItem] = []
    private var cancellables = Set<AnyCancellable>()
    init() {
        $searchText
            .debounce(for: .milliseconds(300), scheduler: RunLoop.main)
            .sink { [weak self] _ in
                self?.filterMenus()
            }
            .store(in: &cancellables)
    }
    func fetchMenus() {
        isLoading = true
        error = nil
        Task { @MainActor in
            do {
                let response = try await MenuAPI.shared.getAll()
                self.menus = response.menus.map { apiMenu in
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
                if let cats = response.categories {
                    self.categories = ["Semua"] + cats.map { $0.name }
                }
                self.filterMenus()
                self.isLoading = false
            } catch {
                self.error = error.localizedDescription
                self.isLoading = false
                self.loadMockData()
            }
        }
    }
    private func loadMockData() {
        menus = [
            Menu(id: 1, name: "Wagyu Steak", description: nil, price: 150000, image_url: "https://images.unsplash.com/photo-1546241072-48010ad2862c?w=500", category_id: 1, is_available: true, stock: 10),
            Menu(id: 2, name: "Salmon Grill", description: nil, price: 120000, image_url: "https://images.unsplash.com/photo-1519708227418-e8d316e890f6?w=500", category_id: 1, is_available: true, stock: 5),
            Menu(id: 3, name: "Pasta Carbonara", description: nil, price: 85000, image_url: "https://images.unsplash.com/photo-1612874742237-6526221588e3?w=500", category_id: 2, is_available: true, stock: 20),
            Menu(id: 4, name: "Nasi Goreng Spesial", description: nil, price: 45000, image_url: "https://images.unsplash.com/photo-1512058564366-18510be2db19?w=500", category_id: 2, is_available: true, stock: 15),
            Menu(id: 5, name: "Es Teh Manis", description: nil, price: 10000, image_url: nil, category_id: 3, is_available: true, stock: 100)
        ]
        filterMenus()
    }
    func filterMenus() {
        var result = menus
        if selectedCategory != "Semua" {
            result = result.filter { menu in
                true 
            }
        }
        if !searchText.isEmpty {
            result = result.filter { $0.name.localizedCaseInsensitiveContains(searchText) }
        }
        filteredMenus = result
    }
    func selectCategory(_ category: String) {
        selectedCategory = category
        filterMenus()
    }
    func addToCart(_ menu: Menu) {
        if let index = cartItems.firstIndex(where: { $0.menuId == menu.id }) {
            cartItems[index].quantity += 1
        } else {
            cartItems.append(CartItem(id: cartItems.count + 1, menuId: menu.id, menu: menu, quantity: 1))
        }
    }
    func updateCartQuantity(itemId: Int, quantity: Int) {
        if let index = cartItems.firstIndex(where: { $0.id == itemId }) {
            if quantity <= 0 {
                cartItems.remove(at: index)
            } else {
                cartItems[index].quantity = quantity
            }
        }
    }
    func removeFromCart(itemId: Int) {
        cartItems.removeAll { $0.id == itemId }
    }
    func clearCart() {
        cartItems.removeAll()
    }
    var cartTotal: Double {
        cartItems.reduce(0) { $0 + ($1.menu.price * Double($1.quantity)) }
    }
    var cartCount: Int {
        cartItems.reduce(0) { $0 + $1.quantity }
    }
}
struct CartItem: Identifiable {
    let id: Int
    let menuId: Int
    let menu: Menu
    var quantity: Int
}