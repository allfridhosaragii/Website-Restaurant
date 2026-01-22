import SwiftUI
struct ProfileScreen: View {
    @State private var isDarkMode = false
    @State private var isIndonesian = true
    var body: some View {
        NavigationView {
            ZStack {
                Color(hex: "F5F5F0").ignoresSafeArea()
                VStack(spacing: 0) {
                    ZStack(alignment: .top) {
                        ProfileHeaderShape()
                            .fill(Color.primaryBrand)
                            .frame(height: 300)
                            .shadow(color: .black.opacity(0.1), radius: 10, x: 0, y: 5)
                        VStack(spacing: 20) {
                            Text("Profile")
                                .font(.system(size: 28, weight: .bold))
                                .foregroundColor(.white)
                                .padding(.top, 50)
                            ProfileCard()
                                .padding(.horizontal, 20)
                        }
                    }
                    .ignoresSafeArea(.all, edges: .top)
                    .zIndex(1)
                    ScrollView(showsIndicators: false) {
                        VStack(spacing: 24) {
                            HStack(spacing: 16) {
                                NavigationLink(destination: OrdersScreen()) {
                                    QuickActionCard(icon: "clock.fill", color: Color(hex: "D81B60"), bg: Color(hex: "FCE4EC"), value: "12", label: "Histori")
                                }
                                NavigationLink(destination: FavoritesScreen()) {
                                    QuickActionCard(icon: "star.fill", color: Color(hex: "F57C00"), bg: Color(hex: "FFF3E0"), value: "5", label: "Favorit")
                                }
                            }
                            .padding(.top, 16)
                            VStack(alignment: .leading, spacing: 12) {
                                Text("Pengaturan Tampilan")
                                    .font(.system(size: 16, weight: .bold))
                                    .foregroundColor(.textPrimary)
                                VStack(spacing: 0) {
                                    SettingToggleRow(icon: "sun.max.fill", iconColor: .orange, bg: Color(hex: "FFF3E0"), title: "Mode Terang", isOn: $isDarkMode)
                                    Divider().padding(.leading, 60)
                                    SettingToggleRow(icon: "globe", iconColor: .blue, bg: Color(hex: "E3F2FD"), title: "Bahasa Indonesia", isOn: $isIndonesian)
                                }
                                .background(Color.white)
                                .cornerRadius(16)
                                .shadow(color: Color.black.opacity(0.05), radius: 2)
                            }
                            VStack(alignment: .leading, spacing: 12) {
                                Text("Hubungi Kami")
                                    .font(.system(size: 16, weight: .bold))
                                    .foregroundColor(.textPrimary)
                                VStack(spacing: 0) {
                                    SettingLinkRow(icon: "phone.fill", iconColor: .teal, bg: Color(hex: "E0F2F1"), title: "Telepon", subtitle: "+62 21 1234 5678", destination: nil) 
                                    Divider().padding(.leading, 60)
                                    SettingLinkRow(icon: "envelope.fill", iconColor: .blue, bg: Color(hex: "E3F2FD"), title: "Email", subtitle: "info@culinaire.com", destination: nil) 
                                    Divider().padding(.leading, 60)
                                    SettingLinkRow(icon: "mappin.circle.fill", iconColor: .red, bg: Color(hex: "FFEBEE"), title: "Alamat", subtitle: "Jl. Sudirman No. 123", destination: AnyView(ContactScreen()))
                                }
                                .background(Color.white)
                                .cornerRadius(16)
                                .shadow(color: Color.black.opacity(0.05), radius: 2)
                            }
                            VStack(alignment: .leading, spacing: 12) {
                                Text("Informasi Lainnya")
                                    .font(.system(size: 16, weight: .bold))
                                    .foregroundColor(.textPrimary)
                                VStack(spacing: 0) {
                                     SettingLinkRow(icon: "info.circle.fill", iconColor: .purple, bg: Color(hex: "F3E5F5"), title: "Tentang Culinaire", subtitle: nil, destination: AnyView(AboutScreen()))
                                }
                                .background(Color.white)
                                .cornerRadius(16)
                                .shadow(color: Color.black.opacity(0.05), radius: 2)
                            }
                            Button(action: {}) {
                                HStack {
                                    Image(systemName: "rectangle.portrait.and.arrow.right")
                                    Text("Keluar")
                                }
                                .font(.system(size: 16, weight: .bold))
                                .foregroundColor(.red)
                                .frame(maxWidth: .infinity)
                                .frame(height: 56)
                                .background(Color.white)
                                .cornerRadius(16)
                                .overlay(RoundedRectangle(cornerRadius: 16).stroke(Color.red.opacity(0.2), lineWidth: 1))
                            }
                            VStack {
                                Text("Culinaire v1.0.0")
                                    .font(.caption)
                                    .foregroundColor(.gray)
                                Text("Premium Dining Experience")
                                    .font(.caption2)
                                    .italic()
                                    .foregroundColor(.gray.opacity(0.8))
                            }
                            .padding(.bottom, 100)
                        }
                        .paddingHorizontal(20)
                    }
                    .padding(.top, -30) 
                }
            }
            .navigationBarHidden(true)
        }
    }
}
struct ProfileHeaderShape: Shape {
    func path(in rect: CGRect) -> Path {
        var path = Path()
        path.move(to: CGPoint(x: 0, y: 0))
        path.addLine(to: CGPoint(x: rect.width, y: 0))
        path.addLine(to: CGPoint(x: rect.width, y: rect.height - 40))
        path.addQuadCurve(to: CGPoint(x: 0, y: rect.height - 40), control: CGPoint(x: rect.width/2, y: rect.height + 40))
        path.closeSubpath()
        return path
    }
}
struct ProfileCard: View {
    var body: some View {
        VStack(spacing: 20) {
            HStack {
                ZStack {
                    Circle()
                        .fill(Color.gold)
                        .frame(width: 64, height: 64)
                        .overlay(Circle().stroke(Color.white, lineWidth: 2))
                    Image(systemName: "person.fill")
                        .font(.system(size: 30))
                        .foregroundColor(.white)
                    Circle()
                        .fill(Color.gold)
                        .frame(width: 20, height: 20)
                        .overlay(Image(systemName: "crown.fill").font(.system(size: 10)).foregroundColor(.white))
                        .overlay(Circle().stroke(Color.primaryBrand, lineWidth: 1))
                        .offset(x: 22, y: 22)
                }
                VStack(alignment: .leading, spacing: 4) {
                    Text("Pengguna Culinaire")
                        .font(.system(size: 18, weight: .bold))
                        .foregroundColor(.white)
                    Text("email@culinaire.com")
                        .font(.system(size: 12))
                        .foregroundColor(.white.opacity(0.8))
                    HStack(spacing: 4) {
                        Image(systemName: "star.fill")
                            .font(.system(size: 10))
                            .foregroundColor(.gold)
                        Text("PLATINUM MEMBER")
                            .font(.system(size: 10, weight: .bold))
                            .foregroundColor(.gold)
                    }
                    .padding(.horizontal, 8)
                    .padding(.vertical, 4)
                    .background(Color.black.opacity(0.2))
                    .cornerRadius(8)
                    .overlay(RoundedRectangle(cornerRadius: 8).stroke(Color.gold, lineWidth: 1))
                }
                Spacer()
                NavigationLink(destination: EditProfileScreen()) {
                    Image(systemName: "pencil")
                        .font(.system(size: 16))
                        .foregroundColor(.white)
                        .frame(width: 40, height: 40)
                        .background(Color.white.opacity(0.2))
                        .cornerRadius(12)
                }
            }
            Divider().background(Color.white.opacity(0.3))
            HStack {
                StatItem(value: "1,250", label: "Poin")
                Divider().frame(height: 30).background(Color.white.opacity(0.3))
                StatItem(value: "12", label: "Pesanan")
                Divider().frame(height: 30).background(Color.white.opacity(0.3))
                StatItem(value: "5", label: "Favorit")
            }
        }
        .padding(20)
        .background(LinearGradient(colors: [.white.opacity(0.15), .white.opacity(0.05)], startPoint: .topLeading, endPoint: .bottomTrailing))
        .cornerRadius(24)
        .overlay(RoundedRectangle(cornerRadius: 24).stroke(Color.white.opacity(0.2), lineWidth: 1))
    }
}
struct StatItem: View {
    let value: String
    let label: String
    var body: some View {
        VStack {
            Text(value).font(.system(size: 20, weight: .bold)).foregroundColor(.white)
            Text(label).font(.system(size: 11)).foregroundColor(.white.opacity(0.8))
        }
        .frame(maxWidth: .infinity)
    }
}
struct QuickActionCard: View {
    let icon: String
    let color: Color
    let bg: Color
    let value: String
    let label: String
    var body: some View {
        HStack {
            Image(systemName: icon)
                .font(.system(size: 20))
                .foregroundColor(color)
                .frame(width: 40, height: 40)
                .background(bg)
                .cornerRadius(12)
            VStack(alignment: .leading) {
                Text(value).font(.system(size: 16, weight: .bold)).foregroundColor(.textPrimary)
                Text(label).font(.system(size: 11)).foregroundColor(.textSecondary)
            }
            Spacer()
        }
        .padding()
        .background(Color.white)
        .cornerRadius(16)
        .shadow(color: Color.black.opacity(0.05), radius: 4)
    }
}
struct SettingToggleRow: View {
    let icon: String
    let iconColor: Color
    let bg: Color
    let title: String
    @Binding var isOn: Bool
    var body: some View {
        HStack {
            Image(systemName: icon)
                .foregroundColor(iconColor)
                .frame(width: 36, height: 36)
                .background(bg)
                .cornerRadius(10)
            Text(title)
                .font(.system(size: 14, weight: .medium))
                .foregroundColor(.textPrimary)
            Spacer()
            Toggle("", isOn: $isOn)
                .labelsHidden()
                .tint(.primaryBrand)
        }
        .padding(12)
    }
}
struct SettingLinkRow: View {
    let icon: String
    let iconColor: Color
    let bg: Color
    let title: String
    let subtitle: String?
    let destination: AnyView?
    var body: some View {
        Group {
            if let dest = destination {
                NavigationLink(destination: dest) {
                    content
                }
            } else {
                Button(action: {}) {
                    content
                }
            }
        }
    }
    var content: some View {
        HStack {
            Image(systemName: icon)
                .foregroundColor(iconColor)
                .frame(width: 36, height: 36)
                .background(bg)
                .cornerRadius(10)
            VStack(alignment: .leading) {
                Text(title)
                    .font(.system(size: 14, weight: .medium))
                    .foregroundColor(.textPrimary)
                if let sub = subtitle {
                    Text(sub)
                        .font(.system(size: 11))
                        .foregroundColor(.textSecondary)
                }
            }
            Spacer()
            Image(systemName: "chevron.forward")
                .font(.system(size: 14))
                .foregroundColor(.gray)
        }
        .padding(12)
    }
}