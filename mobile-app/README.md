# Culinaire App - React Native

Aplikasi mobile **fully native** untuk Culinaire Restaurant.

## 📋 Requirements

- **Node.js** 18+
- **npm** atau **yarn**
- **React Native CLI**
- **Android Studio** (untuk Android)
- **JDK 17**

## 🚀 Quick Start

### 1. Install Dependencies
```bash
cd mobile-app
npm install
```

### 2. Update API URL
Edit `src/api/client.js`:
```javascript
const BASE_URL = 'https://your-domain.up.railway.app/api';
```

### 3. Run Android
```bash
npm run android
```

### 4. Build APK
```bash
cd android
./gradlew assembleRelease
```

APK di: `android/app/build/outputs/apk/release/`

## 📁 Project Structure

```
mobile-app/
├── src/
│   ├── api/client.js          # API client (axios)
│   ├── context/AuthContext.js # Auth state
│   ├── navigation/            # React Navigation
│   ├── screens/
│   │   ├── auth/              # Login, Register
│   │   ├── home/              # HomeScreen
│   │   ├── menu/              # Menu list & detail
│   │   ├── cart/              # Cart & Checkout
│   │   ├── orders/            # Order history
│   │   ├── reservations/      # Reservations
│   │   └── profile/           # Profile & Favorites
│   ├── components/            # Reusable components
│   └── theme/colors.js        # Theme constants
├── android/                   # Android native
├── App.js                     # Entry point
└── package.json
```

## 📱 Features

| Feature | Screen |
|---------|--------|
| Login & Register | Auth screens |
| Menu browsing | Menu, MenuDetail |
| Shopping cart | Cart, Checkout |
| Order tracking | Orders |
| Table reservation | Reservations |
| Favorites | Profile |

## 🎨 UI/UX

- Native UI components (bukan WebView)
- Dark theme matching website
- Bottom tab navigation
- Pull-to-refresh
- Loading & error states

## 🔧 Development

```bash
# Start Metro bundler
npm start

# Run on Android
npm run android

# Build release
cd android && ./gradlew assembleRelease
```
