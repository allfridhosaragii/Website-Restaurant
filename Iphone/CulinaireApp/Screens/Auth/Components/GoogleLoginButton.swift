import SwiftUI
struct GoogleLoginButton: View {
    var action: () -> Void
    var body: some View {
        Button(action: action) {
            HStack {
                HStack(spacing: 0) {
                    Text("G").foregroundColor(Color(hex: "4285F4"))
                    Text("o").foregroundColor(Color(hex: "DB4437"))
                    Text("o").foregroundColor(Color(hex: "F4B400"))
                    Text("g").foregroundColor(Color(hex: "4285F4"))
                    Text("l").foregroundColor(Color(hex: "0F9D58"))
                    Text("e").foregroundColor(Color(hex: "DB4437"))
                }
                .font(.system(size: 24, weight: .bold))
                .padding(.trailing, 8)
                Text( "Masuk dengan Google") 
                    .font(.system(size: 15, weight: .semibold))
                    .foregroundColor(Color(hex: "333333"))
            }
            .frame(maxWidth: .infinity)
            .frame(height: 56)
            .background(Color.white)
            .cornerRadius(16)
            .overlay(
                RoundedRectangle(cornerRadius: 16)
                    .stroke(Color.border, lineWidth: 1)
            )
            .shadow(color: Color.black.opacity(0.05), radius: 2, x: 0, y: 1)
        }
    }
}