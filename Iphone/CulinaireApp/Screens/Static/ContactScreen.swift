import SwiftUI
struct ContactScreen: View {
    @Environment(\.presentationMode) var presentationMode
    var body: some View {
        ZStack {
            Color.black.ignoresSafeArea()
            VStack(spacing: 0) {
                HStack {
                    Button(action: { presentationMode.wrappedValue.dismiss() }) {
                        Image(systemName: "arrow.backward")
                            .font(.system(size: 20, weight: .bold))
                            .foregroundColor(.white)
                            .padding(8)
                    }
                    Spacer()
                }
                .padding()
                ScrollView {
                    VStack(alignment: .leading, spacing: 30) {
                        VStack(alignment: .leading, spacing: 10) {
                            Text("GET IN TOUCH")
                                .font(.caption)
                                .tracking(2)
                                .foregroundColor(.gold)
                            Text("Let's Start a Conversation")
                                .font(.system(size: 32, weight: .bold))
                                .foregroundColor(.white)
                            Rectangle().fill(Color.gold).frame(width: 40, height: 2)
                        }
                        .padding(24)
                        VStack(spacing: 0) {
                            ContactRow(num: "01", title: "Visit Us", l1: "Jl. Ketintang No. 156", l2: "Surabaya, Indonesia")
                            ContactRow(num: "02", title: "Call Us", l1: "+62 31 828 6500", l2: "Mon - Sun, 08:00 - 20:00")
                            ContactRow(num: "03", title: "Email Us", l1: "info@surabaya.telkom", l2: "university.ac.id")
                        }
                        .padding(.horizontal, 24)
                        VStack(alignment: .leading, spacing: 20) {
                            Text("Send a Message")
                                .font(.title2)
                                .foregroundColor(.white)
                            DarkInput(placeholder: "Your Name")
                            DarkInput(placeholder: "Email Address")
                            DarkInput(placeholder: "Message", isMulti: true)
                            Button(action: {}) {
                                Text("SEND MESSAGE")
                                    .font(.system(size: 14, weight: .bold))
                                    .tracking(2)
                                    .foregroundColor(.white)
                                    .frame(maxWidth: .infinity)
                                    .padding()
                                    .overlay(Rectangle().stroke(Color.white, lineWidth: 1))
                            }
                        }
                        .padding(24)
                        .padding.bottom(40)
                    }
                }
            }
        }
        .navigationBarHidden(true)
    }
}
struct ContactRow: View {
    let num: String
    let title: String
    let l1: String
    let l2: String
    var body: some View {
        HStack(alignment: .top, spacing: 20) {
            Text(num)
                .font(.system(size: 24, weight: .light)) 
                .foregroundColor(.gold.opacity(0.5))
            VStack(alignment: .leading, spacing: 4) {
                Text(title)
                    .font(.system(size: 18, weight: .medium))
                    .foregroundColor(.white)
                Text(l1)
                    .font(.system(size: 14))
                    .foregroundColor(.gray)
                Text(l2)
                    .font(.system(size: 14))
                    .foregroundColor(.gray)
            }
            Spacer()
        }
        .padding(.vertical, 20)
        .overlay(Rectangle().frame(height: 1).foregroundColor(.white.opacity(0.1)), alignment: .bottom)
    }
}
struct DarkInput: View {
    let placeholder: String
    var isMulti: Bool = false
    @State private var text = ""
    var body: some View {
        VStack {
            if isMulti {
                TextEditor(text: $text)
                    .frame(height: 100)
                    .colorMultiply(Color.black) 
                    .overlay(Text(text.isEmpty ? placeholder : "").foregroundColor(.gray).padding(.top, 8).padding(.leading, 4), alignment: .topLeading)
            } else {
                TextField("", text: $text)
                    .placeholder(when: text.isEmpty) {
                        Text(placeholder).foregroundColor(.gray)
                    }
            }
            Rectangle().fill(Color.gray.opacity(0.5)).frame(height: 1)
        }
        .padding(.bottom, 10)
    }
}
extension View {
    func placeholder<Content: View>(
        when shouldShow: Bool,
        alignment: Alignment = .leading,
        @ViewBuilder content: () -> Content) -> some View {
        ZStack(alignment: alignment) {
            placeholder().opacity(shouldShow ? 1 : 0)
            self
        }
    }
}