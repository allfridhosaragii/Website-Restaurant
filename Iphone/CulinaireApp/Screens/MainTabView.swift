import SwiftUI
struct MainTabView: View {
    @State private var selectedTab: Tab = .home
    init() {
        UITabBar.appearance().isHidden = true
    }
    var body: some View {
        ZStack(alignment: .bottom) {
            Group {
                switch selectedTab {
                case .home:
                    HomeScreen(selectedTab: $selectedTab)
                case .menu:
                    MenuScreen()
                case .reservations:
                    ReservationsScreen()
                case .points:
                    PointsScreen()
                case .profile:
                    ProfileScreen()
                }
            }
            .frame(maxWidth: .infinity, maxHeight: .infinity)
            LiquidGlassTabBar(selectedTab: $selectedTab)
        }
        .ignoresSafeArea(.keyboard, edges: .bottom)
    }
}