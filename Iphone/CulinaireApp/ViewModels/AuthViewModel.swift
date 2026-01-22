import Foundation
import SwiftUI
@MainActor
class AuthViewModel: ObservableObject {
    @Published var isAuthenticated: Bool = false
    @Published var isLoading: Bool = false
    @Published var user: UserData?
    @Published var error: String?
    struct UserData: Codable {
        let id: Int
        let name: String
        let email: String
        let avatarUrl: String?
        let points: Int?
        let role: String?
    }
    init() {
        if let _ = UserDefaults.standard.string(forKey: "auth_token") {
            isAuthenticated = true
            Task { await loadUser() }
        }
    }
    func login(email: String, password: String) async -> Bool {
        isLoading = true
        error = nil
        do {
            let response = try await AuthAPI.shared.login(email: email, password: password)
            if response.success, let user = response.user {
                self.user = UserData(
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    avatarUrl: user.avatarUrl,
                    points: user.points,
                    role: user.role
                )
                isAuthenticated = true
                isLoading = false
                return true
            } else {
                error = response.message ?? "Login gagal"
                isLoading = false
                return false
            }
        } catch {
            self.error = error.localizedDescription
            isLoading = false
            return false
        }
    }
    func register(name: String, email: String, password: String) async -> Bool {
        isLoading = true
        error = nil
        do {
            let response = try await AuthAPI.shared.register(name: name, email: email, password: password)
            if response.success, let user = response.user {
                self.user = UserData(
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    avatarUrl: user.avatarUrl,
                    points: user.points,
                    role: user.role
                )
                isAuthenticated = true
                isLoading = false
                return true
            } else {
                error = response.message ?? "Registrasi gagal"
                isLoading = false
                return false
            }
        } catch {
            self.error = error.localizedDescription
            isLoading = false
            return false
        }
    }
    func logout() {
        Task {
            try? await AuthAPI.shared.logout()
        }
        APIClient.shared.clearToken()
        isAuthenticated = false
        user = nil
    }
    func loadUser() async {
        do {
            let apiUser = try await AuthAPI.shared.getUser()
            self.user = UserData(
                id: apiUser.id,
                name: apiUser.name,
                email: apiUser.email,
                avatarUrl: apiUser.avatarUrl,
                points: apiUser.points,
                role: apiUser.role
            )
        } catch {
            if case APIError.unauthorized = error {
                logout()
            }
        }
    }
}