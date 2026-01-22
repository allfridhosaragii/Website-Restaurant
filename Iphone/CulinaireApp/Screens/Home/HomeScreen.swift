import SwiftUI
struct HomeScreen: View {
    @StateObject private var viewModel = HomeViewModel()
    @Binding var selectedTab: Tab
    var body: some View {
        NavigationView {
            ScrollView(showsIndicators: false) {
                VStack(spacing: 0) {
                    HStack {
                        VStack(alignment: .leading, spacing: 2) {
                            Text("Culinaire.")
                                .font(.system(size: 24, weight: .bold))
                                .foregroundColor(.primaryBrand)
                            Text("Dine In • Take Away")
                                .font(.system(size: 12))
                                .foregroundColor(Color(hex: "888888"))
                        }
                        Spacer()
                        Button(action: {}) {
                            Image(systemName: "bell") 
                                .font(.system(size: 24))
                                .foregroundColor(Color(hex: "333333"))
                        }
                    }
                    .padding(.horizontal, 20)
                    .padding(.top, 10) 
                    .padding(.bottom, 10)
                    Button(action: { selectedTab = .menu }) {
                        HStack {
                            Image(systemName: "magnifyingglass")
                                .foregroundColor(Color(hex: "999999"))
                                .font(.system(size: 18))
                            Text("Cari menu favorit...")
                                .foregroundColor(Color(hex: "999999"))
                                .font(.system(size: 14))
                            Spacer()
                        }
                        .padding(12)
                        .background(Color(hex: "F5F5F5"))
                        .cornerRadius(25)
                    }
                    .padding(.horizontal, 20)
                    .padding(.bottom, 16)
                    MemberCard(
                        userName: viewModel.userName,
                        points: viewModel.dashboardStats.points,
                        onRedeem: { selectedTab = .points },
                        onCardPress: { selectedTab = .points }
                    )
                    .padding(.horizontal, 20)
                    .padding(.bottom, 16)
                    PromoSection(onPromoPress: { selectedTab = .menu })
                        .padding(.bottom, 20)
                    VStack(alignment: .leading, spacing: 12) {
                        Text("Layanan Cepat")
                            .font(.system(size: 18, weight: .bold))
                            .foregroundColor(.textPrimary)
                            .padding(.horizontal, 20)
                        QuickMenuGrid(
                            onMenuPress: { selectedTab = .menu },
                            onReservationPress: { selectedTab = .reservations }
                        )
                        .padding(.horizontal, 20)
                    }
                    .padding(.bottom, 20)
                    StatsRow()
                        .padding(.horizontal, 20)
                        .padding(.bottom, 20)
                    FlashSaleCard(onAction: { selectedTab = .menu })
                        .padding(.horizontal, 20)
                        .padding(.bottom, 20)
                    VStack(spacing: 12) {
                        HStack {
                            VStack(alignment: .leading) {
                                Text("Menu Paling Populer")
                                    .font(.system(size: 18, weight: .bold))
                                    .foregroundColor(.textPrimary)
                                Text("Berdasarkan pesanan terbanyak")
                                    .font(.system(size: 12))
                                    .foregroundColor(.textSecondary)
                            }
                            Spacer()
                            Button(action: { selectedTab = .menu }) {
                                Text("Lihat Semua >")
                                    .font(.system(size: 12))
                                    .foregroundColor(.primaryBrand)
                            }
                        }
                        .padding(.horizontal, 20)
                        if viewModel.isLoadingMenus {
                            ProgressView()
                                .tint(.primaryBrand)
                                .padding()
                        } else {
                            ScrollView(.horizontal, showsIndicators: false) {
                                HStack(spacing: 12) {
                                    ForEach(viewModel.popularMenus) { menu in
                                        PopularMenuCard(
                                            menu: menu,
                                            isBestSeller: menu.id == 1, 
                                            onAdd: {}
                                        )
                                    }
                                }
                                .padding(.horizontal, 20)
                            }
                        }
                    }
                    .padding(.bottom, 20)
                    WhyCulinaireSection(onReservation: { selectedTab = .reservations })
                        .padding(.horizontal, 20)
                        .padding(.bottom, 100) 
                }
            }
            .navigationBarHidden(true)
            .background(Color.white)
            .onAppear {
                viewModel.fetchData()
            }
        }
    }
}