import SwiftUI
extension Color {
    static let primaryBrand = Color(hex: "800020")
    static let primaryDim = Color(hex: "5D0016")
    static let secondaryBrand = Color(hex: "D4AF37")
    static let backgroundBrand = Color(hex: "FAF3E0")
    static let surface = Color(hex: "FFFFFF")
    static let surfaceLight = Color(hex: "F5F5F5")
    static let textPrimary = Color(hex: "2D2D2D")
    static let textSecondary = Color(hex: "757575")
    static let textLight = Color(hex: "FFFFFF")
    static let gold = Color(hex: "D4AF37")
    struct Accent {
        static let orange = Color(hex: "FF8C42")
        static let purple = Color(hex: "6A1B9A")
        static let green = Color(hex: "2E7D32")
    }
    static let border = Color(hex: "E0E0E0")
    static let inputBackground = Color(hex: "F5F5F5")
    static let success = Color(hex: "4CAF50")
    static let error = Color(hex: "D32F2F")
    static let blackBrand = Color(hex: "1C1C1E")
}
extension Color {
    init(hex: String) {
        let hex = hex.trimmingCharacters(in: CharacterSet.alphanumerics.inverted)
        var int: UInt64 = 0
        Scanner(string: hex).scanHexInt64(&int)
        let a, r, g, b: UInt64
        switch hex.count {
        case 3: 
            (a, r, g, b) = (255, (int >> 8) * 17, (int >> 4 & 0xF) * 17, (int & 0xF) * 17)
        case 6: 
            (a, r, g, b) = (255, int >> 16, int >> 8 & 0xFF, int & 0xFF)
        case 8: 
            (a, r, g, b) = (int >> 24, int >> 16 & 0xFF, int >> 8 & 0xFF, int & 0xFF)
        default:
            (a, r, g, b) = (1, 1, 1, 0)
        }
        self.init(
            .sRGB,
            red: Double(r) / 255,
            green: Double(g) / 255,
            blue: Double(b) / 255,
            opacity: Double(a) / 255
        )
    }
}