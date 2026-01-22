import SwiftUI
struct MemberCard: View {
    var userName: String = "Tamu Culinaire"
    var points: Int = 0
    var onRedeem: () -> Void
    var onCardPress: () -> Void
    var body: some View {
        Button(action: onCardPress) {
            ZStack {
                LinearGradient(
                    colors: [Color.primaryBrand, Color.primaryDim],
                    startPoint: .topLeading,
                    endPoint: .bottomTrailing
                )
                VStack(alignment: .leading, spacing: 12) {
                    HStack(spacing: 4) {
                        Image(systemName: "crown.fill")
                            .font(.system(size: 12))
                            .foregroundColor(Color.gold)
                        Text("PLATINUM MEMBER")
                            .font(.system(size: 10, weight: .bold))
                            .foregroundColor(Color.gold)
                    }
                    .padding(.horizontal, 10)
                    .padding(.vertical, 4)
                    .background(Color.gold.opacity(0.2))
                    .cornerRadius(20)
                    HStack(alignment: .bottom) {
                        VStack(alignment: .leading, spacing: 4) {
                            Text(userName)
                                .font(.system(size: 14, weight: .regular))
                                .foregroundColor(.white)
                            Text("\(points)")
                                .font(.system(size: 36, weight: .bold))
                                .foregroundColor(.white)
                            Text("Poin Available")
                                .font(.system(size: 12))
                                .foregroundColor(.white.opacity(0.7))
                        }
                        Spacer()
                        Button(action: onRedeem) {
                            Text("Tukar Poin")
                                .font(.system(size: 12, weight: .bold))
                                .foregroundColor(.blackBrand)
                                .padding(.horizontal, 16)
                                .padding(.vertical, 10)
                                .background(Color.gold)
                                .cornerRadius(8)
                        }
                    }
                }
                .padding(16)
            }
        }
        .buttonStyle(PlainButtonStyle()) 
        .frame(height: 140)
        .cornerRadius(16)
    }
}
struct MemberCard_Previews: PreviewProvider {
    static var previews: some View {
        MemberCard(points: 1250, onRedeem: {}, onCardPress: {})
            .padding()
            .previewLayout(.sizeThatFits)
    }
}