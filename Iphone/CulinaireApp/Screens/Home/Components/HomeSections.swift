import SwiftUI
struct FlashSaleCard: View {
    var onAction: () -> Void
    var body: some View {
        Button(action: onAction) {
            ZStack {
                LinearGradient(
                    colors: [Color(hex: "FFD54F"), Color(hex: "FFC107")],
                    startPoint: .leading,
                    endPoint: .trailing
                )
                HStack {
                    VStack(alignment: .leading, spacing: 4) {
                        Text("FLASH SALE")
                            .font(.system(size: 10, weight: .bold))
                            .foregroundColor(.white)
                            .padding(.horizontal, 8)
                            .padding(.vertical, 4)
                            .background(Color(hex: "C62828"))
                            .cornerRadius(4)
                            .padding(.bottom, 4)
                        Text("Diskon hingga 50%")
                            .font(.system(size: 18, weight: .bold))
                            .foregroundColor(.textPrimary)
                        Text("Khusus hari ini untuk menu pilihan")
                            .font(.system(size: 12))
                            .foregroundColor(Color(hex: "555555"))
                            .padding(.bottom, 8)
                        HStack(spacing: 4) {
                            Text("Lihat Menu")
                                .font(.system(size: 12, weight: .semibold))
                            Image(systemName: "arrow.forward")
                                .font(.system(size: 12))
                        }
                        .foregroundColor(.white)
                        .padding(.horizontal, 14)
                        .padding(.vertical, 8)
                        .background(Color.primaryBrand)
                        .cornerRadius(20)
                    }
                    Spacer()
                    Text("🔥")
                        .font(.system(size: 48))
                }
                .padding(16)
            }
        }
        .frame(height: 140) 
        .cornerRadius(16)
    }
}
struct WhyCulinaireSection: View {
    var onReservation: () -> Void
    var body: some View {
        VStack(alignment: .leading, spacing: 16) {
            Text("Mengapa Culinaire?")
                .font(.system(size: 18, weight: .bold))
                .foregroundColor(.textPrimary)
            HStack(spacing: 8) {
                FeatureCard(icon: "cook.fill", title: "Bahan Premium", subtitle: "Kualitas bintang 5") 
                FeatureCard(icon: "person.crop.circle.badge.checkmark", title: "Chef Ahli", subtitle: "25+ Tahun") 
            }
            ExperienceCard()
            Button(action: onReservation) {
                Text("Reservasi Sekarang")
                    .font(.system(size: 14, weight: .bold))
                    .foregroundColor(.blackBrand)
                    .frame(maxWidth: .infinity)
                    .padding(.vertical, 14)
                    .background(Color.gold)
                    .cornerRadius(25)
            }
        }
    }
}
struct FeatureCard: View {
    let icon: String
    let title: String
    let subtitle: String
    var body: some View {
        VStack(spacing: 8) {
            Image(systemName: icon)
                .font(.system(size: 32))
                .foregroundColor(.primaryBrand)
            Text(title)
                .font(.system(size: 14, weight: .bold))
                .foregroundColor(.textPrimary)
            Text(subtitle)
                .font(.system(size: 11))
                .foregroundColor(.textSecondary)
        }
        .frame(maxWidth: .infinity)
        .padding(16)
        .background(Color(hex: "F9F9F9"))
        .cornerRadius(12)
    }
}
struct ExperienceCard: View {
    var body: some View {
        VStack(spacing: 8) {
            Image(systemName: "star.fill")
                .font(.system(size: 24))
                .foregroundColor(.gold)
            Text("Pengalaman Kuliner Terbaik")
                .font(.system(size: 14, weight: .bold))
                .foregroundColor(.textPrimary)
            Text("Chef berpengalaman & bahan premium untuk kepuasan Anda")
                .font(.system(size: 11))
                .foregroundColor(.textSecondary)
                .multilineTextAlignment(.center)
        }
        .frame(maxWidth: .infinity)
        .padding(16)
        .background(Color(hex: "F9F9F9"))
        .cornerRadius(12)
    }
}