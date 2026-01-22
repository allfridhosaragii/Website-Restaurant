import Foundation
struct Menu: Identifiable, Decodable {
    var id: Int
    var name: String
    var description: String?
    var price: Double
    var image_url: String?
    var category_id: Int?
    var is_available: Bool?
    var stock: Int?
    var formattedPrice: String {
        let formatter = NumberFormatter()
        formatter.numberStyle = .currency
        formatter.locale = Locale(identifier: "id_ID")
        formatter.currencySymbol = "Rp "
        formatter.maximumFractionDigits = 0
        return formatter.string(from: NSNumber(value: price)) ?? "Rp \(Int(price))"
    }
}