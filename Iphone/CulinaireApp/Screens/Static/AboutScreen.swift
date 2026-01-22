import SwiftUI
struct AboutScreen: View {
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
                    VStack(alignment: .leading, spacing: 0) {
                        VStack(spacing: 16) {
                            Text("The Legacy")
                                .font(.system(size: 40, weight: .bold))
                                .foregroundColor(.white)
                            Rectangle()
                                .fill(Color.gold)
                                .frame(width: 60, height: 2)
                            Text("OF CULINAIRE")
                                .font(.system(size: 14))
                                .tracking(4)
                                .foregroundColor(.gold)
                        }
                        .frame(maxWidth: .infinity)
                        .padding(.vertical, 60)
                        VStack(alignment: .leading, spacing: 30) {
                            ChapterTitle(title: "The Foundations", chapter: "I")
                            TimelineItem(year: "2009", title: "The Inception", desc: "A humble beginning in the heart of Surabaya.")
                            TimelineItem(year: "2010", title: "First Recognition", desc: "Awarded 'Best Newcomer' by Surabaya Culinary Association.")
                            TimelineItem(year: "2012", title: "Going Global", desc: "Featured in international food magazines.")
                        }
                        .padding(24)
                        VStack(alignment: .leading, spacing: 30) {
                            ChapterTitle(title: "The Golden Era", chapter: "II")
                            ScrollView(.horizontal, showsIndicators: false) {
                                HStack(spacing: 16) {
                                    YearCard(year: "2013", title: "Michelin Star", desc: "Received our first star.")
                                    YearCard(year: "2014", title: "New Heights", desc: "Launched truffel menu.")
                                    YearCard(year: "2016", title: "Innovation", desc: "Molecular gastronomy.")
                                }
                                .padding(.horizontal, 24)
                            }
                            .padding(.horizontal, -24)
                        }
                        .padding(24)
                        .background(Color(hex: "0A0C10"))
                        VStack {
                            Text("EST. 2009 • CULINAIRE")
                                .font(.system(size: 10))
                                .tracking(2)
                                .foregroundColor(.gray)
                        }
                        .frame(maxWidth: .infinity)
                        .padding(.vertical, 40)
                    }
                }
            }
        }
        .navigationBarHidden(true)
    }
}
struct ChapterTitle: View {
    let title: String
    let chapter: String
    var body: some View {
        VStack(spacing: 8) {
            Text("CHAPTER \(chapter)")
                .font(.system(size: 10))
                .tracking(2)
                .foregroundColor(.gold)
            Text(title)
                .font(.system(size: 28)) 
                .foregroundColor(.white)
        }
        .frame(maxWidth: .infinity)
        .padding(.bottom, 20)
    }
}
struct TimelineItem: View {
    let year: String
    let title: String
    let desc: String
    var body: some View {
        HStack(alignment: .top, spacing: 20) {
            Text(year)
                .font(.system(size: 24, weight: .bold))
                .foregroundColor(.white.opacity(0.1))
                .frame(width: 60, alignment: .trailing)
            VStack(alignment: .leading, spacing: 4) {
                Text(title)
                    .font(.system(size: 18, weight: .semibold))
                    .foregroundColor(.white)
                Text(desc)
                    .font(.system(size: 14))
                    .foregroundColor(.gray)
            }
        }
    }
}
struct YearCard: View {
    let year: String
    let title: String
    let desc: String
    var body: some View {
        VStack(alignment: .leading, spacing: 12) {
            Text(year)
                .font(.system(size: 24, weight: .bold))
                .foregroundColor(.gold)
            Text(title)
                .font(.system(size: 16, weight: .bold))
                .foregroundColor(.white)
            Text(desc)
                .font(.system(size: 12))
                .foregroundColor(.gray)
        }
        .padding(20)
        .frame(width: 180, height: 200)
        .background(Color.white.opacity(0.05))
        .cornerRadius(12)
        .overlay(RoundedRectangle(cornerRadius: 12).stroke(Color.white.opacity(0.1), lineWidth: 1))
    }
}