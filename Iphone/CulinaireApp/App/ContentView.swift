import SwiftUI
struct ContentView: View {
    @State private var isAppReady: Bool = false
    @State private var isAuthenticated: Bool = false 
    var body: some View {
        Group {
            if !isAppReady {
                SplashScreen(isAppReady: $isAppReady)
            } else {
                if isAuthenticated {
                    MainTabView()
                        .transition(.opacity)
                } else {
                    LoginScreen(isAuthenticated: $isAuthenticated)
                        .transition(.opacity)
                }
            }
        }
        .animation(.easeInOut, value: isAppReady)
        .animation(.easeInOut, value: isAuthenticated)
    }
}