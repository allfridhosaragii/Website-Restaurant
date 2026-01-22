import SwiftUI
struct TableView: View {
    let table: Table
    let isSelected: Bool
    let onSelect: () -> Void
    var chairColor: Color {
        if table.status == "booked" { return Color(hex: "FECACA") }
        if isSelected { return .primaryBrand }
        if table.is_premium { return Color(hex: "FDE68A") }
        return Color(hex: "D1D5DB")
    }
    var tableColor: Color {
        if table.status == "booked" { return Color(hex: "FEF2F2") }
        if isSelected { return .primaryBrand }
        if table.is_premium { return Color(hex: "FFFBEB") }
        return .white
    }
    var borderColor: Color {
        if table.status == "booked" { return .red.opacity(0.3) }
        if isSelected { return .primaryBrand }
        if table.is_premium { return .gold }
        return .white
    }
    let size: CGFloat = 50 
    var body: some View {
        Button(action: onSelect) {
            ZStack {
                ForEach(0..<min(table.capacity > 2 ? 4 : 2, 4), id: \.self) { i in
                    chair(index: i)
                }
                ZStack {
                    if table.shape == "round" {
                        Circle()
                            .fill(tableColor)
                            .overlay(Circle().stroke(borderColor, lineWidth: 1))
                    } else {
                        RoundedRectangle(cornerRadius: 8)
                            .fill(tableColor)
                            .frame(width: table.shape == "rectangle" ? size * 1.5 : size)
                            .overlay(RoundedRectangle(cornerRadius: 8).stroke(borderColor, lineWidth: 1))
                    }
                    VStack(spacing: 0) {
                        if isSelected {
                            Image(systemName: "checkmark")
                                .foregroundColor(.white)
                        } else if table.status == "booked" {
                            Image(systemName: "xmark")
                                .foregroundColor(.red.opacity(0.5))
                        } else {
                            Text("\(table.number)")
                                .font(.system(size: 10, weight: .bold))
                                .foregroundColor(table.is_premium ? .gold : .gray)
                        }
                    }
                    if table.is_premium && !isSelected && table.status != "booked" {
                        Image(systemName: "star.fill")
                            .font(.system(size: 8))
                            .foregroundColor(.gold)
                            .padding(2)
                            .background(Color.white)
                            .clipShape(Circle())
                            .offset(x: 14, y: -14)
                    }
                }
                .frame(width: size, height: size)
                .shadow(color: Color.black.opacity(0.05), radius: 2, x: 0, y: 2)
            }
        }
        .disabled(table.status == "booked")
    }
    func chair(index: Int) -> some View {
        let offset: CGFloat = 30
        var x: CGFloat = 0
        var y: CGFloat = 0
        switch index {
        case 0: y = -offset 
        case 1: y = offset 
        case 2: x = -offset 
        case 3: x = offset 
        default: break
        }
        return Circle()
            .fill(chairColor)
            .frame(width: 14, height: 14)
            .offset(x: x, y: y)
    }
}
struct Table: Identifiable, Decodable {
    let id: Int
    let number: Int
    let capacity: Int
    let shape: String 
    let status: String 
    let is_premium: Bool
    var row: Int { (number - 1) / 6 }
    var col: Int { (number - 1) % 6 }
}