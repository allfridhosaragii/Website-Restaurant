import SwiftUI
struct SplashScreen: View {
    @State private var isActive: Bool = false
    @Binding var isAppReady: Bool
    var body: some View {
        ZStack {
            Color.backgroundBrand.ignoresSafeArea()
            VStack {
                Text("Culinaire")
                    .font(.brandDisplay(40))
                    .foregroundColor(.primaryBrand)
                Text("Premium Indonesian Cuisine")
                    .font(.brandBody(14))
                    .foregroundColor(.textSecondary)
                    .padding(.top, 8)
                ProgressView()
                    .tint(.gold)
                    .scaleEffect(1.5)
                    .padding(.top, 40)
            }
            .scaleEffect(isActive ? 1.0 : 0.8)
            .opacity(isActive ? 1.0 : 0.0)
            .onAppear {
                withAnimation(.easeOut(duration: 1.0)) {
                    isActive = true
                }
                DispatchQueue.main.asyncAfter(deadline: .now() + 2.5) {
                    withAnimation {
                        isAppReady = true
                    }
                }
            }
        }
    }
}