import SwiftUI
enum Tab: String, CaseIterable {
    case home = "Home"
    case menu = "Menu"
    case reservations = "Reservations"
    case points = "Points"
    case profile = "Profile"
    var icon: String {
        switch self {
        case .home: return "house"
        case .menu: return "menucard" 
        case .reservations: return "calendar"
        case .points: return "gift"
        case .profile: return "person"
        }
    }
    var label: String {
        switch self {
        case .home: return "Beranda"
        case .menu: return "Menu"
        case .reservations: return "Reservasi"
        case .points: return "Poin Saya"
        case .profile: return "Profil"
        }
    }
}
struct LiquidGlassTabBar: View {
    @Binding var selectedTab: Tab
    @Namespace private var animation
    var body: some View {
        HStack(spacing: 0) {
            ForEach(Tab.allCases, id: \.self) { tab in
                Button(action: {
                    withAnimation(.spring(response: 0.3, dampingFraction: 0.7)) {
                        selectedTab = tab
                    }
                }) {
                    VStack(spacing: 4) {
                        Image(systemName: selectedTab == tab ? tab.icon + ".fill" : tab.icon)
                            .font(.system(size: 24))
                            .scaleEffect(selectedTab == tab ? 1.1 : 1.0)
                        Text(tab.label)
                            .font(.system(size: 10))
                            .fontWeight(selectedTab == tab ? .semibold : .regular)
                    }
                    .foregroundColor(selectedTab == tab ? .primaryBrand : .primaryBrand.opacity(0.4))
                    .frame(maxWidth: .infinity)
                    .frame(height: 60)
                    .background(
                        ZStack {
                            if selectedTab == tab {
                                RoundedRectangle(cornerRadius: 30)
                                    .fill(Color.primaryBrand.opacity(0.1))
                                    .matchedGeometryEffect(id: "TabPill", in: animation)
                            }
                        }
                    )
                    .contentShape(Rectangle())
                }
            }
        }
        .padding(.horizontal, 6)
        .padding(.vertical, 6)
        .background(
            ZStack {
                RoundedRectangle(cornerRadius: 36)
                    .fill(Color.backgroundBrand.opacity(0.8))
                    .blur(radius: 0) 
                RoundedRectangle(cornerRadius: 36)
                    .stroke(Color.white.opacity(0.5), lineWidth: 1)
            }
            .background(.ultraThinMaterial, in: RoundedRectangle(cornerRadius: 36))
            .shadow(color: Color.black.opacity(0.15), radius: 10, x: 0, y: 5)
        )
        .padding(.horizontal, 20)
        .padding(.bottom, 20) 
    }
}