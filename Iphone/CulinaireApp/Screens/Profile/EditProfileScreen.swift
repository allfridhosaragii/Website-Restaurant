import SwiftUI
struct EditProfileScreen: View {
    @Environment(\.presentationMode) var presentationMode
    @State private var name: String = "Pengguna Culinaire"
    @State private var email: String = "email@culinaire.com"
    @State private var phone: String = "08123456789"
    @State private var address: String = "Jl. Sudirman No. 123"
    @State private var isSaving: Bool = false
    var body: some View {
        ZStack {
            Color(hex: "F5F5F0").ignoresSafeArea()
            VStack(spacing: 0) {
                HStack {
                    Button(action: { presentationMode.wrappedValue.dismiss() }) {
                        Image(systemName: "arrow.backward")
                            .font(.system(size: 20, weight: .bold))
                            .foregroundColor(.white)
                            .padding(8)
                    }
                    Spacer()
                    Text("Edit Profile")
                        .font(.system(size: 18, weight: .bold))
                        .foregroundColor(.white)
                    Spacer()
                    Color.clear.frame(width: 40)
                }
                .padding()
                .background(Color.primaryBrand)
                ScrollView {
                    VStack(spacing: 24) {
                        VStack(spacing: 8) {
                            ZStack {
                                Circle()
                                    .fill(Color.primaryBrand)
                                    .frame(width: 100, height: 100)
                                    .overlay(Circle().stroke(Color.white, lineWidth: 4))
                                Image(systemName: "person.fill")
                                    .font(.system(size: 40))
                                    .foregroundColor(.white)
                                Button(action: {}) {
                                    Circle()
                                        .fill(Color.gold)
                                        .frame(width: 32, height: 32)
                                        .overlay(Image(systemName: "camera.fill").font(.system(size: 14)).foregroundColor(.white))
                                        .overlay(Circle().stroke(Color(hex: "F5F5F0"), lineWidth: 3))
                                        .offset(x: 35, y: 35)
                                }
                            }
                            Text("Klik untuk ubah foto")
                                .font(.caption)
                                .foregroundColor(.gray)
                        }
                        .padding(.top, 24)
                        VStack(alignment: .leading, spacing: 20) {
                            CustomTextField(title: "Nama Lengkap", placeholder: "Nama Anda", text: $name, icon: "person")
                            VStack(alignment: .leading, spacing: 8) {
                                Text("Email")
                                    .font(.system(size: 14, weight: .semibold))
                                    .foregroundColor(.textPrimary)
                                HStack {
                                    Image(systemName: "envelope")
                                        .foregroundColor(.gray)
                                        .frame(width: 24)
                                    Text(email)
                                        .foregroundColor(.gray)
                                    Spacer()
                                    Image(systemName: "lock.fill")
                                        .font(.caption)
                                        .foregroundColor(.gray)
                                }
                                .padding()
                                .background(Color(hex: "F5F5F5"))
                                .cornerRadius(12)
                                .overlay(RoundedRectangle(cornerRadius: 12).stroke(Color.border, lineWidth: 1))
                            }
                            CustomTextField(title: "Nomor Telepon", placeholder: "0812...", text: $phone, icon: "phone", keyboardType: .phonePad)
                            VStack(alignment: .leading, spacing: 8) {
                                Text("Alamat")
                                    .font(.system(size: 14, weight: .semibold))
                                    .foregroundColor(.textPrimary)
                                HStack(alignment: .top) {
                                    Image(systemName: "mappin.and.ellipse")
                                        .foregroundColor(.gray)
                                        .frame(width: 24)
                                        .padding(.top, 4)
                                    TextEditor(text: $address)
                                        .frame(height: 80)
                                        .colorMultiply(Color(hex: "F9F9F9")) 
                                }
                                .padding()
                                .background(Color.white)
                                .cornerRadius(12)
                                .overlay(RoundedRectangle(cornerRadius: 12).stroke(Color.border, lineWidth: 1))
                            }
                            HStack(spacing: 16) {
                                Button(action: { presentationMode.wrappedValue.dismiss() }) {
                                    Text("Batal")
                                        .fontWeight(.bold)
                                        .foregroundColor(.textSecondary)
                                        .frame(maxWidth: .infinity)
                                        .padding()
                                        .background(Color(hex: "E0E0E0"))
                                        .cornerRadius(12)
                                }
                                Button(action: handleSave) {
                                    if isSaving {
                                        ProgressView().tint(.white)
                                    } else {
                                        Text("Simpan")
                                            .fontWeight(.bold)
                                            .foregroundColor(.white)
                                            .frame(maxWidth: .infinity)
                                            .padding()
                                            .background(Color.primaryBrand)
                                            .cornerRadius(12)
                                    }
                                }
                                .disabled(isSaving)
                            }
                            .padding(.top, 16)
                        }
                        .padding(24)
                        .background(Color.white)
                        .cornerRadius(24)
                        .shadow(color: Color.black.opacity(0.05), radius: 5)
                        .padding(.horizontal, 16)
                    }
                    .padding(.bottom, 40)
                }
            }
        }
        .navigationBarHidden(true)
    }
    func handleSave() {
        isSaving = true
        DispatchQueue.main.asyncAfter(deadline: .now() + 1.5) {
            isSaving = false
            presentationMode.wrappedValue.dismiss()
        }
    }
}