import SwiftUI
struct ForgotPasswordScreen: View {
    @Environment(\.presentationMode) var presentationMode
    @State private var email: String = ""
    @State private var isSending: Bool = false
    @State private var sent: Bool = false
    var body: some View {
        ZStack {
            Color.backgroundBrand.ignoresSafeArea()
            VStack(alignment: .leading, spacing: 20) {
                Button(action: { presentationMode.wrappedValue.dismiss() }) {
                    Image(systemName: "arrow.backward")
                        .foregroundColor(.blackBrand)
                        .padding()
                        .background(Color.white)
                        .clipShape(Circle())
                }
                Text("Lupa Password?")
                    .font(.brandDisplay(32))
                    .foregroundColor(.textPrimary)
                Text("Masukkan email terdaftar Anda untuk menerima instruksi reset password.")
                    .font(.body)
                    .foregroundColor(.textSecondary)
                if sent {
                    VStack(spacing: 16) {
                        Image(systemName: "envelope.badge.fill")
                            .font(.system(size: 50))
                            .foregroundColor(.green)
                        Text("Email Terkirim!")
                            .font(.headline)
                        Text("Cek inbox email Anda.")
                            .foregroundColor(.gray)
                    }
                    .frame(maxWidth: .infinity)
                    .padding(.vertical, 40)
                } else {
                    CustomTextField(title: "Email", placeholder: "email@example.com", text: $email, icon: "envelope", keyboardType: .emailAddress)
                    Button(action: handleSend) {
                        if isSending {
                            ProgressView().tint(.white)
                        } else {
                            Text("Kirim Instruksi")
                                .fontWeight(.bold)
                                .foregroundColor(.white)
                                .frame(maxWidth: .infinity)
                                .padding()
                                .background(Color.primaryBrand)
                                .cornerRadius(12)
                        }
                    }
                    .disabled(isSending || email.isEmpty)
                }
                Spacer()
            }
            .padding(24)
        }
        .navigationBarHidden(true)
    }
    func handleSend() {
        isSending = true
        DispatchQueue.main.asyncAfter(deadline: .now() + 1.5) {
            isSending = false
            withAnimation {
                sent = true
            }
        }
    }
}