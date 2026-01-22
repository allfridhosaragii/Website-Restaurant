import SwiftUI
struct MenuScreen: View {
    @StateObject private var viewModel = MenuViewModel()
    @State private var showCartPopup = false
    @State private var showCheckout = false
    let columns = [
        GridItem(.flexible(), spacing: 16),
        GridItem(.flexible(), spacing: 16)
    ]
    var body: some View {
        NavigationView {
            ZStack(alignment: .bottom) {
                Color.backgroundBrand.ignoresSafeArea()
                VStack(spacing: 0) {
                    VStack(alignment: .leading, spacing: 4) {
                        Text("Menu Kami")
                            .font(.system(size: 24, weight: .bold))
                            .foregroundColor(.white)
                        Text("Pilih menu favorit Anda")
                            .font(.system(size: 13))
                            .foregroundColor(.white.opacity(0.8))
                    }
                    .frame(maxWidth: .infinity, alignment: .leading)
                    .padding(20)
                    .padding(.top, 40) 
                    .background(Color.primaryBrand)
                    .clipShape(RoundedCorner(radius: 24, corners: [.bottomLeft, .bottomRight]))
                    .shadow(color: Color.black.opacity(0.1), radius: 4, y: 2)
                    HStack {
                        HStack {
                            Image(systemName: "magnifyingglass")
                                .foregroundColor(Color(hex: "999999"))
                            TextField("Cari menu...", text: $viewModel.searchText)
                                .foregroundColor(.textPrimary)
                        }
                        .padding(12)
                        .background(Color.white)
                        .cornerRadius(12)
                        .overlay(RoundedRectangle(cornerRadius: 12).stroke(Color.border, lineWidth: 1))
                        Button(action: {}) {
                            Image(systemName: "slider.horizontal.3")
                                .foregroundColor(.primaryBrand)
                                .padding(12)
                                .background(Color.white)
                                .cornerRadius(12)
                                .overlay(RoundedRectangle(cornerRadius: 12).stroke(Color.border, lineWidth: 1))
                        }
                    }
                    .padding(16)
                    ScrollView(.horizontal, showsIndicators: false) {
                        HStack(spacing: 8) {
                            ForEach(viewModel.categories, id: \.self) { category in
                                CategoryPill(
                                    title: category,
                                    isSelected: viewModel.selectedCategory == category,
                                    action: { viewModel.selectCategory(category) }
                                )
                            }
                        }
                        .padding(.horizontal, 16)
                    }
                    .padding(.bottom, 16)
                    if viewModel.isLoading {
                        Spacer()
                        ProgressView().tint(.primaryBrand)
                        Spacer()
                    } else {
                        ScrollView(showsIndicators: false) {
                            LazyVGrid(columns: columns, spacing: 16) {
                                ForEach(viewModel.filteredMenus) { menu in
                                    NavigationLink(destination: MenuDetailScreen(menu: menu)) {
                                        MenuCard(
                                            menu: menu,
                                            onAdd: { viewModel.addToCart(menu) },
                                            onTap: {
                                            }
                                        )
                                    }
                                    .buttonStyle(PlainButtonStyle())
                                }
                            }
                            .padding(16)
                            .padding(.bottom, 100) 
                        }
                        .refreshable {
                            viewModel.fetchMenus()
                        }
                    }
                }
                .ignoresSafeArea(.all, edges: .top)
                NavigationLink(destination: CheckoutScreen(), isActive: $showCheckout) {
                    EmptyView()
                }
                FloatingCartButton(
                    itemCount: viewModel.cartCount,
                    totalPrice: viewModel.cartTotal,
                    action: { withAnimation { showCartPopup = true } }
                )
                CartPopup(
                    isPresented: $showCartPopup,
                    viewModel: viewModel,
                    onCheckout: {
                        withAnimation {
                           showCartPopup = false
                           showCheckout = true
                        }
                    }
                )
                .zIndex(100)
            }
            .navigationBarHidden(true)
            .onAppear {
                viewModel.fetchMenus()
            }
        }
    }
}
struct RoundedCorner: Shape {
    var radius: CGFloat = .infinity
    var corners: UIRectCorner = .allCorners
    func path(in rect: CGRect) -> Path {
        let path = UIBezierPath(roundedRect: rect, byRoundingCorners: corners, cornerRadii: CGSize(width: radius, height: radius))
        return Path(path.cgPath)
    }
}