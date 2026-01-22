import SwiftUI
struct ReservationsScreen: View {
    @State private var step: Int = 1
    @State private var selectedTableId: Int?
    @State private var tables: [Table] = []
    @State private var isLoading: Bool = true
    @State private var name: String = ""
    @State private var phone: String = ""
    @State private var email: String = ""
    @State private var date: Date = Date()
    @State private var time: Date = Date()
    @State private var isUploading: Bool = false
    @State private var showSuccess: Bool = false
    var body: some View {
        NavigationView {
            ZStack {
                Color.backgroundBrand.ignoresSafeArea()
                VStack(spacing: 0) {
                    VStack(alignment: .leading, spacing: 4) {
                        HStack {
                            Image(systemName: "calendar")
                                .font(.system(size: 24))
                                .foregroundColor(.white)
                                .padding(8)
                                .background(Color.white.opacity(0.2))
                                .clipShape(RoundedRectangle(cornerRadius: 8))
                            VStack(alignment: .leading) {
                                Text("Reservasi Meja")
                                    .font(.system(size: 20, weight: .bold))
                                    .foregroundColor(.white)
                                Text("Pesan tempat spesial Anda")
                                    .font(.system(size: 12))
                                    .foregroundColor(.white.opacity(0.8))
                            }
                        }
                        .padding(.top, 40)
                        HStack(spacing: 4) {
                            ProgressStep(step: 1, current: step, title: "Pilih Meja")
                            ProgressStep(step: 2, current: step, title: "Isi Data")
                            ProgressStep(step: 3, current: step, title: "Bayar")
                        }
                        .padding(.top, 16)
                    }
                    .padding(20)
                    .background(Color.primaryBrand)
                    if isLoading {
                        Spacer()
                        ProgressView().tint(.primaryBrand)
                        Spacer()
                    } else {
                        ScrollView(showsIndicators: false) {
                            VStack(spacing: 24) {
                                if step == 1 {
                                    StepOneTableSelection(tables: tables, selectedId: $selectedTableId)
                                } else if step == 2 {
                                    StepTwoForm(name: $name, phone: $phone, email: $email, date: $date, time: $time)
                                } else if step == 3 {
                                    StepThreePayment(
                                        selectedTable: tables.first(where: { $0.id == selectedTableId }),
                                        isUploading: $isUploading,
                                        onConfirm: handlePayment
                                    )
                                }
                            }
                            .padding(20)
                        }
                    }
                    HStack {
                        if step > 1 {
                            Button(action: { withAnimation { step -= 1 } }) {
                                Text("Kembali")
                                    .foregroundColor(.textPrimary)
                                    .fontWeight(.semibold)
                                    .frame(maxWidth: .infinity)
                                    .frame(height: 50)
                                    .background(Color(hex: "E5E7EB"))
                                    .cornerRadius(12)
                            }
                        }
                        Button(action: handleNext) {
                            Text(step == 3 ? "Konfirmasi" : "Lanjut")
                                .foregroundColor(.white)
                                .fontWeight(.bold)
                                .frame(maxWidth: .infinity)
                                .frame(height: 50)
                                .background(isNextDisabled ? Color.gray : Color.primaryBrand)
                                .cornerRadius(12)
                        }
                        .disabled(isNextDisabled)
                    }
                    .padding(20)
                    .background(Color.white)
                    .shadow(color: Color.black.opacity(0.05), radius: 5, y: -5)
                }
                .ignoresSafeArea(.all, edges: .top)
                if showSuccess {
                     SuccessOverlay(onClose: {
                         step = 1
                         selectedTableId = nil
                         showSuccess = false
                     })
                }
            }
            .navigationBarHidden(true)
            .onAppear(perform: loadTables)
        }
    }
    var isNextDisabled: Bool {
        if step == 1 { return selectedTableId == nil }
        if step == 2 { return name.isEmpty || phone.isEmpty || email.isEmpty }
        if step == 3 { return isUploading }
        return false
    }
    func handleNext() {
        if step < 3 {
            withAnimation { step += 1 }
        } else {
        }
    }
    func handlePayment() {
        isUploading = true
        DispatchQueue.main.asyncAfter(deadline: .now() + 2.0) {
            isUploading = false
            withAnimation {
                showSuccess = true
            }
        }
    }
    func loadTables() {
        DispatchQueue.main.asyncAfter(deadline: .now() + 1.0) {
            var mockTables: [Table] = []
            for i in 1...27 {
                let isPremium = [1,2,3,4,5].contains(i)
                let status = [6, 12, 18].contains(i) ? "booked" : "available"
                let shape = [22, 23].contains(i) ? "rectangle" : ([1,2].contains(i) ? "round" : "square")
                let capacity = isPremium ? 4 : 2
                mockTables.append(Table(id: i, number: i, capacity: capacity, shape: shape, status: status, is_premium: isPremium))
            }
            self.tables = mockTables
            self.isLoading = false
        }
    }
}
struct ProgressStep: View {
    let step: Int
    let current: Int
    let title: String
    var body: some View {
        HStack {
            Circle()
                .fill(step <= current ? Color.white : Color.white.opacity(0.3))
                .frame(width: 8, height: 8)
            Text(title)
                .font(.system(size: 10, weight: .bold))
                .foregroundColor(step <= current ? .white : .white.opacity(0.5))
        }
        .frame(maxWidth: .infinity)
    }
}
struct StepOneTableSelection: View {
    let tables: [Table]
    @Binding var selectedId: Int?
    let columns = Array(repeating: GridItem(.flexible(), spacing: 16), count: 3) 
    var body: some View {
        VStack(alignment: .leading) {
            Text("Peta Meja")
                .font(.headline)
            Text("Silakan pilih meja yang tersedia")
                .font(.caption)
                .foregroundColor(.gray)
            LazyVGrid(columns: columns, spacing: 24) {
                ForEach(tables) { table in
                    TableView(table: table, isSelected: selectedId == table.id) {
                        selectedId = table.id
                    }
                }
            }
            .padding(.top, 16)
            HStack(spacing: 16) {
                LegendItem(color: .white, text: "Tersedia")
                LegendItem(color: .primaryBrand, text: "Dipilih")
                LegendItem(color: .red.opacity(0.2), text: "Terisi")
                LegendItem(color: .gold.opacity(0.2), text: "Premium")
            }
            .padding(.top, 24)
        }
    }
}
struct LegendItem: View {
    let color: Color
    let text: String
    var body: some View {
        HStack(spacing: 4) {
            Circle().fill(color).frame(width: 10, height: 10).overlay(Circle().stroke(Color.gray, lineWidth: 0.5))
            Text(text).font(.system(size: 10))
        }
    }
}
struct StepTwoForm: View {
    @Binding var name: String
    @Binding var phone: String
    @Binding var email: String
    @Binding var date: Date
    @Binding var time: Date
    var body: some View {
        VStack(spacing: 16) {
            CustomTextField(title: "Nama Lengkap", placeholder: "Nama Anda", text: $name)
            CustomTextField(title: "No. Telepon", placeholder: "0812...", text: $phone, keyboardType: .phonePad)
            CustomTextField(title: "Email", placeholder: "email@contoh.com", text: $email, keyboardType: .emailAddress)
            DatePicker("Tanggal", selection: $date, displayedComponents: .date)
                .padding()
                .background(Color.white)
                .cornerRadius(12)
                .overlay(RoundedRectangle(cornerRadius: 12).stroke(Color.border, lineWidth: 1))
            DatePicker("Waktu", selection: $time, displayedComponents: .hourAndMinute)
                .padding()
                .background(Color.white)
                .cornerRadius(12)
                .overlay(RoundedRectangle(cornerRadius: 12).stroke(Color.border, lineWidth: 1))
        }
    }
}
struct StepThreePayment: View {
    let selectedTable: Table?
    @Binding var isUploading: Bool
    var onConfirm: () -> Void
    var body: some View {
        VStack(spacing: 20) {
            HStack {
                VStack(alignment: .leading) {
                    Text("Meja \(selectedTable?.number ?? 0)")
                        .font(.headline)
                    Text(selectedTable?.is_premium == true ? "Premium Table" : "Standard Table")
                        .font(.caption)
                        .foregroundColor(.gold)
                }
                Spacer()
                Text("Rp 150.000") 
                    .font(.title3)
                    .fontWeight(.bold)
                    .foregroundColor(.primaryBrand)
            }
            .padding()
            .background(Color.white)
            .cornerRadius(16)
            .shadow(color: Color.black.opacity(0.05), radius: 5)
            VStack(spacing: 12) {
                Text("Scan QRIS untuk Deposit")
                    .font(.headline)
                Rectangle()
                    .fill(Color.white)
                    .frame(width: 200, height: 200)
                    .overlay(
                        Image(systemName: "qrcode")
                            .resizable()
                            .padding(20)
                            .foregroundColor(.black)
                    )
                    .overlay(RoundedRectangle(cornerRadius: 12).stroke(Color.gray, lineWidth: 1))
                Text("Silakan transfer Rp 150.000 sebagai deposit.")
                    .font(.caption)
                    .multilineTextAlignment(.center)
                    .foregroundColor(.gray)
                Button(action: onConfirm) {
                    HStack {
                        if isUploading {
                            ProgressView()
                        } else {
                            Image(systemName: "square.and.arrow.up")
                            Text("Upload Bukti Transfer")
                        }
                    }
                    .font(.system(size: 14, weight: .semibold))
                    .foregroundColor(.primaryBrand)
                    .padding()
                    .frame(maxWidth: .infinity)
                    .background(Color.primaryBrand.opacity(0.1))
                    .cornerRadius(12)
                }
                .disabled(isUploading)
            }
            .padding()
            .background(Color.white)
            .cornerRadius(16)
        }
    }
}
struct SuccessOverlay: View {
    var onClose: () -> Void
    var body: some View {
        ZStack {
            Color.black.opacity(0.6).ignoresSafeArea()
            VStack(spacing: 20) {
                Image(systemName: "checkmark.circle.fill")
                    .font(.system(size: 60))
                    .foregroundColor(.green)
                    .padding(.top, 20)
                Text("Reservasi Berhasil!")
                    .font(.title2)
                    .fontWeight(.bold)
                Text("Bukti reservasi telah dikirim ke email Anda.")
                    .font(.body)
                    .multilineTextAlignment(.center)
                    .foregroundColor(.gray)
                    .padding(.horizontal)
                Button(action: onClose) {
                    Text("Selesai")
                        .fontWeight(.bold)
                        .foregroundColor(.white)
                        .frame(maxWidth: .infinity)
                        .padding()
                        .background(Color.primaryBrand)
                        .cornerRadius(12)
                }
                .padding(20)
            }
            .background(Color.white)
            .cornerRadius(24)
            .padding(40)
        }
    }
}