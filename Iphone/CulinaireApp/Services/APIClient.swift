import Foundation
enum APIConfig {
    static let baseURL = "https://website-restaurant.up.railway.app/api"
    static let timeout: TimeInterval = 15.0
}
enum APIError: Error, LocalizedError {
    case invalidURL
    case noData
    case decodingError(Error)
    case networkError(Error)
    case serverError(Int, String?)
    case unauthorized
    var errorDescription: String? {
        switch self {
        case .invalidURL: return "Invalid URL"
        case .noData: return "No data received"
        case .decodingError(let error): return "Decoding error: \(error.localizedDescription)"
        case .networkError(let error): return "Network error: \(error.localizedDescription)"
        case .serverError(let code, let message): return "Server error (\(code)): \(message ?? "Unknown")"
        case .unauthorized: return "Unauthorized - Please login again"
        }
    }
}
class APIClient {
    static let shared = APIClient()
    private init() {}
    private var authToken: String? {
        get { UserDefaults.standard.string(forKey: "auth_token") }
        set { UserDefaults.standard.set(newValue, forKey: "auth_token") }
    }
    func setToken(_ token: String?) {
        authToken = token
    }
    func clearToken() {
        authToken = nil
        UserDefaults.standard.removeObject(forKey: "user_data")
    }
    func request<T: Decodable>(
        endpoint: String,
        method: String = "GET",
        body: [String: Any]? = nil,
        requiresAuth: Bool = false
    ) async throws -> T {
        guard let url = URL(string: "\(APIConfig.baseURL)\(endpoint)") else {
            throw APIError.invalidURL
        }
        var request = URLRequest(url: url)
        request.httpMethod = method
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue("application/json", forHTTPHeaderField: "Accept")
        request.timeoutInterval = APIConfig.timeout
        if requiresAuth, let token = authToken {
            request.setValue("Bearer \(token)", forHTTPHeaderField: "Authorization")
        }
        if let body = body {
            request.httpBody = try? JSONSerialization.data(withJSONObject: body)
        }
        do {
            let (data, response) = try await URLSession.shared.data(for: request)
            guard let httpResponse = response as? HTTPURLResponse else {
                throw APIError.noData
            }
            if httpResponse.statusCode == 401 {
                clearToken()
                throw APIError.unauthorized
            }
            guard (200...299).contains(httpResponse.statusCode) else {
                let message = String(data: data, encoding: .utf8)
                throw APIError.serverError(httpResponse.statusCode, message)
            }
            let decoder = JSONDecoder()
            decoder.keyDecodingStrategy = .convertFromSnakeCase
            return try decoder.decode(T.self, from: data)
        } catch let error as APIError {
            throw error
        } catch let error as DecodingError {
            throw APIError.decodingError(error)
        } catch {
            throw APIError.networkError(error)
        }
    }
}
class AuthAPI {
    static let shared = AuthAPI()
    private let client = APIClient.shared
    struct LoginResponse: Decodable {
        let success: Bool
        let message: String?
        let token: String?
        let user: UserData?
    }
    struct UserData: Codable {
        let id: Int
        let name: String
        let email: String
        let avatarUrl: String?
        let points: Int?
        let role: String?
    }
    func login(email: String, password: String) async throws -> LoginResponse {
        let response: LoginResponse = try await client.request(
            endpoint: "/auth/login",
            method: "POST",
            body: ["email": email, "password": password]
        )
        if let token = response.token {
            client.setToken(token)
        }
        return response
    }
    func register(name: String, email: String, password: String) async throws -> LoginResponse {
        let response: LoginResponse = try await client.request(
            endpoint: "/auth/register",
            method: "POST",
            body: ["name": name, "email": email, "password": password, "password_confirmation": password]
        )
        if let token = response.token {
            client.setToken(token)
        }
        return response
    }
    func logout() async throws {
        let _: EmptyResponse = try await client.request(endpoint: "/auth/logout", method: "POST", requiresAuth: true)
        client.clearToken()
    }
    func getUser() async throws -> UserData {
        struct UserResponse: Decodable { let user: UserData }
        let response: UserResponse = try await client.request(endpoint: "/auth/user", requiresAuth: true)
        return response.user
    }
}
class MenuAPI {
    static let shared = MenuAPI()
    private let client = APIClient.shared
    struct MenuListResponse: Decodable {
        let menus: [MenuData]
        let categories: [CategoryData]?
    }
    struct MenuData: Decodable, Identifiable {
        let id: Int
        let name: String
        let slug: String
        let description: String?
        let price: Double
        let imageUrl: String?
        let category: String?
        let isAvailable: Bool?
        let stock: Int?
    }
    struct CategoryData: Decodable, Identifiable {
        let id: Int
        let name: String
        let slug: String?
    }
    func getAll(category: String? = nil, search: String? = nil) async throws -> MenuListResponse {
        var endpoint = "/menus"
        var params: [String] = []
        if let cat = category, !cat.isEmpty { params.append("category=\(cat)") }
        if let s = search, !s.isEmpty { params.append("search=\(s)") }
        if !params.isEmpty { endpoint += "?" + params.joined(separator: "&") }
        return try await client.request(endpoint: endpoint)
    }
    func getBySlug(_ slug: String) async throws -> MenuData {
        struct SingleMenu: Decodable { let menu: MenuData }
        let response: SingleMenu = try await client.request(endpoint: "/menus/\(slug)")
        return response.menu
    }
}
class CartAPI {
    static let shared = CartAPI()
    private let client = APIClient.shared
    struct CartResponse: Decodable {
        let items: [CartItemData]
        let total: Double?
    }
    struct CartItemData: Decodable, Identifiable {
        let id: Int
        let menuId: Int
        let quantity: Int
        let menu: MenuAPI.MenuData?
    }
    func get() async throws -> CartResponse {
        return try await client.request(endpoint: "/cart", requiresAuth: true)
    }
    func add(menuId: Int, quantity: Int = 1) async throws -> CartResponse {
        return try await client.request(
            endpoint: "/cart",
            method: "POST",
            body: ["menu_id": menuId, "quantity": quantity],
            requiresAuth: true
        )
    }
    func update(id: Int, quantity: Int) async throws -> CartResponse {
        return try await client.request(
            endpoint: "/cart/\(id)",
            method: "PUT",
            body: ["quantity": quantity],
            requiresAuth: true
        )
    }
    func remove(id: Int) async throws -> EmptyResponse {
        return try await client.request(endpoint: "/cart/\(id)", method: "DELETE", requiresAuth: true)
    }
    func clear() async throws -> EmptyResponse {
        return try await client.request(endpoint: "/cart", method: "DELETE", requiresAuth: true)
    }
}
class OrderAPI {
    static let shared = OrderAPI()
    private let client = APIClient.shared
    struct OrderListResponse: Decodable {
        let orders: [OrderData]
    }
    struct OrderData: Decodable, Identifiable {
        let id: Int
        let orderNumber: String?
        let status: String
        let total: Double
        let createdAt: String?
        let items: [OrderItemData]?
    }
    struct OrderItemData: Decodable {
        let menuName: String?
        let quantity: Int
        let price: Double
        let subtotal: Double?
    }
    func getAll() async throws -> OrderListResponse {
        return try await client.request(endpoint: "/orders", requiresAuth: true)
    }
    func create(paymentMethod: String = "cash") async throws -> OrderData {
        struct CreateResponse: Decodable { let order: OrderData }
        let response: CreateResponse = try await client.request(
            endpoint: "/orders",
            method: "POST",
            body: ["payment_method": paymentMethod],
            requiresAuth: true
        )
        return response.order
    }
    func getById(_ id: Int) async throws -> OrderData {
        struct SingleOrder: Decodable { let order: OrderData }
        let response: SingleOrder = try await client.request(endpoint: "/orders/\(id)", requiresAuth: true)
        return response.order
    }
}
class ReservationAPI {
    static let shared = ReservationAPI()
    private let client = APIClient.shared
    struct ReservationListResponse: Decodable {
        let reservations: [ReservationData]
    }
    struct ReservationData: Decodable, Identifiable {
        let id: Int
        let reservationDate: String?
        let reservationTime: String?
        let guests: Int?
        let tableNumber: Int?
        let status: String
        let depositAmount: Double?
        let depositStatus: String?
    }
    func getAll() async throws -> ReservationListResponse {
        return try await client.request(endpoint: "/reservations", requiresAuth: true)
    }
    func create(data: [String: Any]) async throws -> ReservationData {
        struct CreateResponse: Decodable { let reservation: ReservationData }
        let response: CreateResponse = try await client.request(
            endpoint: "/reservations",
            method: "POST",
            body: data,
            requiresAuth: true
        )
        return response.reservation
    }
}
class FavoritesAPI {
    static let shared = FavoritesAPI()
    private let client = APIClient.shared
    struct FavoritesResponse: Decodable {
        let favorites: [FavoriteData]
    }
    struct FavoriteData: Decodable, Identifiable {
        let id: Int
        let menuId: Int?
        let menu: MenuAPI.MenuData?
    }
    func getAll() async throws -> FavoritesResponse {
        return try await client.request(endpoint: "/favorites", requiresAuth: true)
    }
    func toggle(menuId: Int) async throws -> EmptyResponse {
        return try await client.request(endpoint: "/favorites/\(menuId)", method: "POST", requiresAuth: true)
    }
}
class DashboardAPI {
    static let shared = DashboardAPI()
    private let client = APIClient.shared
    struct DashboardStats: Decodable {
        let totalOrders: Int
        let totalReservations: Int
        let totalFavorites: Int
        let points: Int
    }
    func getStats() async throws -> DashboardStats {
        return try await client.request(endpoint: "/dashboard", requiresAuth: true)
    }
}
class TablesAPI {
    static let shared = TablesAPI()
    private let client = APIClient.shared
    struct TablesResponse: Decodable {
        let tables: [TableData]
    }
    struct TableData: Decodable, Identifiable {
        let id: Int
        let number: Int
        let capacity: Int
        let shape: String?
        let status: String
        let isPremium: Bool?
    }
    func getAll() async throws -> TablesResponse {
        return try await client.request(endpoint: "/tables")
    }
}
struct EmptyResponse: Decodable {
    let success: Bool?
    let message: String?
}