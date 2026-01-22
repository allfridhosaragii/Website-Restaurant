import SwiftUI
struct RegisterScreen: View {
    @Binding var isAuthenticated: Bool
    @Environment(\.presentationMode) var presentationMode
    @State private var name = ""
    @State private var email = ""
    @State private var phone = ""
    @State private var password = ""
    @State private var confirmPassword = ""
    @State private var isLoading = false
    let creamBg = Color(hex: "F5F5F0")
    let burgundy = Color(hex: "9A1B3F")
    var body: some View {
        ZStack {
            creamBg.ignoresSafeArea()
            ScrollView(showsIndicators: false) {
                VStack(spacing: 0) {
                    VStack(spacing: 8) {
                        ZStack {
                            RoundedRectangle(cornerRadius: 20)
                                .fill(burgundy)
                                .frame(width: 64, height: 64)
                                .shadow(color: burgundy.opacity(0.3), radius: 8, x: 0, y: 6)
                            Text("C")
                                .font(.system(size: 32, weight: .bold))
                                .foregroundColor(.white)
                        }
                        .padding(.bottom, 16)
                        Text("Daftar")
                            .font(.brandDisplay(24))
                            .foregroundColor(Color(hex: "1A1A1A"))
                        Text("Buat akun baru untuk memulai perjalanan kuliner Anda")
                            .font(.system(size: 14))
                            .foregroundColor(Color(hex: "666666"))
                            .multilineTextAlignment(.center)
                            .padding(.horizontal, 40)
                    }
                    .padding(.top, 20)
                    .padding(.bottom, 32)
                    VStack(spacing: 16) {
                        CustomTextField(icon: "person", title: "Nama Lengkap", placeholder: "Masukkan nama lengkap", text: $name)
                        CustomTextField(icon: "envelope", title: "Email", placeholder: "nama@email.com", text: $email, keyboardType: .emailAddress)
                        CustomTextField(icon: "phone", title: "Nomor Telepon", placeholder: "+62 812...", text: $phone, keyboardType: .phonePad)
                        CustomTextField(icon: "lock", title: "Password", placeholder: "••••••••", text: $password, isSecure: true)
                        CustomTextField(icon: "lock", title: "Konfirmasi Password", placeholder: "••••••••", text: $confirmPassword, isSecure: true)
                        Button(action: handleRegister) {
                            if isLoading {
                                ProgressView()
                                    .tint(.white)
                            } else {
                                Text("Daftar")
                                    .font(.system(size: 16, weight: .bold))
                            }
                        }
                        .frame(maxWidth: .infinity)
                        .frame(height: 56)
                        .background(burgundy)
                        .foregroundColor(.white)
                        .cornerRadius(16)
                        .shadow(color: burgundy.opacity(0.3), radius: 8, x: 0, y: 4)
                        .padding(.top, 8)
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
                    Button(action: {}) {
                        HStack {
                            Text("G").fontWeight(.bold).foregroundColor(.blue) +
                            Text("o").fontWeight(.bold).foregroundColor(.red) +
                            Text("o").fontWeight(.bold).foregroundColor(.orange) +
                            Text("g").fontWeight(.bold).foregroundColor(.blue) +
                            Text("l").fontWeight(.bold).foregroundColor(.green) +
                            Text("e").fontWeight(.bold).foregroundColor(.red)
                            Text("  Daftar dengan Google")
                                .font(.system(size: 15, weight: .semibold))
                                .foregroundColor(Color(hex: "333333"))
                        }
                        .frame(maxWidth: .infinity)
                        .frame(height: 56)
                        .background(Color.white)
                        .cornerRadius(16)
                        .overlay(RoundedRectangle(cornerRadius: 16).stroke(Color.border, lineWidth: 1))
                        .shadow(color: Color.black.opacity(0.05), radius: 2, x: 0, y: 1)
                    }
                    .padding(.horizontal, 24)
                    .padding(.bottom, 40)
                    HStack {
                        Text("Sudah punya akun?")
                            .font(.system(size: 14))
                            .foregroundColor(Color(hex: "666666"))
                        Button(action: {
                            presentationMode.wrappedValue.dismiss()
                        }) {
                            Text("Masuk Disini")
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
    func handleRegister() {
        isLoading = true
        DispatchQueue.main.asyncAfter(deadline: .now() + 1.5) {
            isLoading = false
            withAnimation {
                isAuthenticated = true
            }
        }
    }
}