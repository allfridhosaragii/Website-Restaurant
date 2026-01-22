import SwiftUI
struct CustomTextField: View {
    var title: String
    var placeholder: String
    @Binding var text: String
    var icon: String? = nil
    var isSecure: Bool = false
    var keyboardType: UIKeyboardType = .default
    @State private var isPasswordVisible: Bool = false
    var body: some View {
        VStack(alignment: .leading, spacing: 8) {
            Text(title)
                .font(.system(size: 14))
                .foregroundColor(.textPrimary)
                .padding(.leading, 4)
            HStack {
                if let icon = icon {
                    Image(systemName: icon)
                        .foregroundColor(Color(hex: "999999"))
                        .font(.system(size: 20))
                        .padding(.trailing, 8)
                }
                if isSecure && !isPasswordVisible {
                    SecureField(placeholder, text: $text)
                        .foregroundColor(.textPrimary)
                } else {
                    TextField(placeholder, text: $text)
                        .foregroundColor(.textPrimary)
                        .keyboardType(keyboardType)
                        .textInputAutocapitalization(.never)
                }
                if isSecure {
                    Button(action: { isPasswordVisible.toggle() }) {
                        Image(systemName: isPasswordVisible ? "eye.slash" : "eye")
                            .foregroundColor(Color(hex: "999999"))
                    }
                }
            }
            .padding()
            .background(
                RoundedRectangle(cornerRadius: 16)
                    .stroke(Color.border, lineWidth: 1)
            )
            .background(Color.white) 
            .cornerRadius(16)
        }
    }
}