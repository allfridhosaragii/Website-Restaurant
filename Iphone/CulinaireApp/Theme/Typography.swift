import SwiftUI
struct Typography {
    static let displayFontName = "PlayfairDisplay-Bold"
    static let bodyFontName = "Inter-Regular"
    static let bodyBoldFontName = "Inter-Bold"
    static func display(size: CGFloat) -> Font {
        return Font.custom(displayFontName, size: size)
            .weight(.bold)
    }
    static func body(size: CGFloat) -> Font {
        return Font.custom(bodyFontName, size: size)
    }
    static func bodyBold(size: CGFloat) -> Font {
        return Font.custom(bodyBoldFontName, size: size)
            .weight(.bold)
    }
    static func systemDisplay(size: CGFloat) -> Font {
        return Font.system(size: size, weight: .bold, design: .serif)
    }
    static func systemBody(size: CGFloat) -> Font {
        return Font.system(size: size, design: .default)
    }
}
extension Font {
    static func brandDisplay(_ size: CGFloat) -> Font {
        return Typography.display(size: size)
    }
    static func brandBody(_ size: CGFloat) -> Font {
        return Typography.body(size: size)
    }
}