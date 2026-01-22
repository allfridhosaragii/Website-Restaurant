import SwiftUI
struct LoginScreen: View {
    @Binding var isAuthenticated: Bool
    @State private var email = ""
    @State private var password = ""
    @State private var isLoading = false
    @State private var showRegister = false
    let creamBg = Color(hex: "F5F5F0")
    let burgundy = Color(hex: "9A1B3F")
    var body: some View {
        NavigationView {
            ZStack {
                creamBg.ignoresSafeArea()
                ScrollView(showsIndicators: false) {
                    VStack(spacing: 0) {
                        VStack(spacing: 8) {
                            ZStack {
                                RoundedRectangle(cornerRadius: 24)
                                    .fill(burgundy)
                                    .frame(width: 80, height: 80)
                                    .shadow(color: burgundy.opacity(0.3), radius: 10, x: 0, y: 8)
                                Text("C")
                                    .font(.system(size: 40, weight: .bold))
                                    .foregroundColor(.white)
                            }
                            .padding(.bottom, 16)
                            Text("Culinaire")
                                .font(.brandDisplay(28)) 
                                .foregroundColor(Color(hex: "1A1A1A"))
                            Text("Premium Culinary Experience")
                                .font(.system(size: 14))
                                .foregroundColor(Color(hex: "666666"))
                                .tracking(0.5)
                        }
                        .padding(.top, 40)
                        .padding(.bottom, 40)
                        VStack(spacing: 20) {
                            CustomTextField(
                                title: "Email",
                                placeholder: "nama@email.com",
                                text: $email,
                                keyboardType: .emailAddress
                            )
                            CustomTextField(
                                title: "Password",
                                placeholder: "••••••••",
                                text: $password,
                                isSecure: true
                            )
                            HStack {
                                Spacer()
                                NavigationLink(destination: ForgotPasswordScreen()) {
                                    Text("Lupa password?")
                                        .font(.system(size: 13, weight: .semibold))
                                        .foregroundColor(burgundy)
                                }
                            }
                            Button(action: handleLogin) {
                                if isLoading {
                                    ProgressView()
                                        .tint(.white)
                                } else {
                                    Text("Masuk")
                                        .font(.system(size: 16, weight: .bold))
                                }
                            }
                            .frame(maxWidth: .infinity)
                            .frame(height: 56)
                            .background(burgundy)
                            .foregroundColor(.white)
                            .cornerRadius(16)
                            .shadow(color: burgundy.opacity(0.3), radius: 8, x: 0, y: 4)
                            .disabled(isLoading)
                        }
                        .padding(24)
                        .background(Color.white)
                        .cornerRadius(24)
                        .shadow(color: Color.black.opacity(0.05), radius: 8, x: 0, y: 2)
                        .padding(.horizontal, 24)
                        .padding(.bottom, 32)
                        HStack {
                            Rectangle().fill(Color.border).frame(height: 1)
                            Text("Atau lanjutkan dengan")
                                .font(.system(size: 13))
                                .foregroundColor(Color(hex: "888888"))
                                .fixedSize()
                                .padding(.horizontal, 16)
                            Rectangle().fill(Color.border).frame(height: 1)
                        }
                        .padding(.horizontal, 24)
                        .padding(.bottom, 24)
                        GoogleLoginButton(action: handleGoogleLogin)
                            .padding(.horizontal, 24)
                            .padding(.bottom, 40)
                        HStack {
                            Text("Belum punya akun?")
                                .font(.system(size: 14))
                                .foregroundColor(Color(hex: "666666"))
                            NavigationLink(destination: RegisterScreen(isAuthenticated: $isAuthenticated)) {
                                Text("Daftar Sekarang")
                                    .font(.system(size: 14, weight: .bold))
                                    .foregroundColor(burgundy)
                            }
                        }
                        .padding(.bottom, 50)
                    }
                }
            }
            .navigationBarHidden(true)
        }
        .navigationViewStyle(.stack) 
    }
    func handleLogin() {
        isLoading = true
        DispatchQueue.main.asyncAfter(deadline: .now() + 1.5) {
            isLoading = false
            withAnimation {
                isAuthenticated = true
            }
        }
    }
    func handleGoogleLogin() {
        isLoading = true
        DispatchQueue.main.asyncAfter(deadline: .now() + 1.5) {
            isLoading = false
            withAnimation {
                isAuthenticated = true
            }
        }
    }
}