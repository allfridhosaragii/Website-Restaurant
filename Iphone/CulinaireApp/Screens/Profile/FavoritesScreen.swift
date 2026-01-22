import SwiftUI
struct FavoritesScreen: View {
    @Environment(\.presentationMode) var presentationMode
    @State private var favorites: [Menu] = []
    var body: some View {
        ZStack {
            Color.backgroundBrand.ignoresSafeArea()
            VStack(spacing: 0) {
                HStack {
                    Button(action: { presentationMode.wrappedValue.dismiss() }) {
                        Image(systemName: "arrow.backward")
                            .font(.system(size: 20, weight: .bold))
                            .foregroundColor(.blackBrand)
                            .padding(8)
                            .background(Color.white)
                            .clipShape(Circle())
                    }
                    Spacer()
                    Text("Favorit Saya")
                        .font(.system(size: 18, weight: .bold))
                        .foregroundColor(.blackBrand)
                    Spacer()
                    Color.clear.frame(width: 40)
                }
                .padding()
                if favorites.isEmpty {
                    VStack(spacing: 16) {
                        Spacer()
                        Image(systemName: "heart.slash")
                            .font(.system(size: 60))
                            .foregroundColor(.gray.opacity(0.5))
                        Text("Belum ada favorit")
                            .font(.headline)
                            .foregroundColor(.gray)
                        Text("Tambahkan menu yang Anda sukai disini")
                            .font(.caption)
                            .foregroundColor(.gray)
                        Spacer()
                    }
                } else {
                    ScrollView {
                        LazyVGrid(columns: [GridItem(.flexible()), GridItem(.flexible())], spacing: 16) {
                            ForEach(favorites) { menu in
                                MenuCard(menu: menu, onAdd: {}, onTap: {})
                                    .overlay(
                                        Image(systemName: "heart.fill")
                                            .foregroundColor(.red)
                                            .padding(8)
                                            .background(Color.white)
                                            .clipShape(Circle())
                                            .shadow(radius: 2)
                                            .offset(x: -8, y: 8),
                                        alignment: .topTrailing
                                    )
                            }
                        }
                        .padding(20)
                    }
                }
            }
        }
        .navigationBarHidden(true)
        .onAppear(perform: loadFavorites)
    }
    func loadFavorites() {
        favorites = [
            Menu(id: 1, name: "Wagyu Steak", description: nil, price: 150000, image_url: "https://images.unsplash.com/photo-1546241072-48010ad2862c?w=500", category_id: 1, is_available: true, stock: 10),
            Menu(id: 3, name: "Pasta Carbonara", description: nil, price: 85000, image_url: "https://images.unsplash.com/photo-1612874742237-6526221588e3?w=500", category_id: 2, is_available: true, stock: 20)
        ]
    }
}