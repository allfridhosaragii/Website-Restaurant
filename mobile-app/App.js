import React from 'react';
import { StatusBar } from 'react-native';
import { GestureHandlerRootView } from 'react-native-gesture-handler';
import { AuthProvider } from './src/context/AuthContext';
import { SettingsProvider } from './src/context/SettingsContext';
import { CartProvider } from './src/context/CartContext';
import AppNavigator from './src/navigation/AppNavigator';
import { colors } from './src/theme/colors';

const App = () => {
    return (
        <SettingsProvider>
            <AuthProvider>
                <CartProvider>
                    <StatusBar
                        barStyle="light-content"
                        backgroundColor={colors.primary}
                    />
                    <GestureHandlerRootView style={{ flex: 1 }}>
                        <AppNavigator />
                    </GestureHandlerRootView>
                </CartProvider>
            </AuthProvider>
        </SettingsProvider>
    );
};

export default App;
