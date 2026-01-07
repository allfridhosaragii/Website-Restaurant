import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';

import { useAuth } from '../context/AuthContext';
import { colors } from '../theme/colors';
import LiquidGlassTabBar from '../components/LiquidGlassTabBar';

// Auth Screens
import SplashScreen from '../screens/auth/SplashScreen';
import LoginScreen from '../screens/auth/LoginScreen';
import RegisterScreen from '../screens/auth/RegisterScreen';

// Main Screens
import HomeScreen from '../screens/home/HomeScreen';
import MenuScreen from '../screens/menu/MenuScreen';
import MenuDetailScreen from '../screens/menu/MenuDetailScreen';
import CartScreen from '../screens/cart/CartScreen';
import CheckoutScreen from '../screens/cart/CheckoutScreen';
import ProfileScreen from '../screens/profile/ProfileScreen';
import OrdersScreen from '../screens/orders/OrdersScreen';
import OrderDetailScreen from '../screens/orders/OrderDetailScreen';
import ReservationsScreen from '../screens/reservations/ReservationsScreen';
import CreateReservationScreen from '../screens/reservations/CreateReservationScreen';

// New Feature Screens
import FavoritesScreen from '../screens/favorites/FavoritesScreen';
import PointsScreen from '../screens/points/PointsScreen';
import AboutScreen from '../screens/about/AboutScreen';
import ContactScreen from '../screens/contact/ContactScreen';

const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

// Bottom Tab Navigator with Floating Animated Tab Bar
const MainTabs = () => {
    return (
        <Tab.Navigator
            tabBar={(props) => <LiquidGlassTabBar {...props} />}
            screenOptions={{
                headerShown: false,
            }}
        >
            <Tab.Screen name="Home" component={HomeScreen} options={{ title: 'Beranda' }} />
            <Tab.Screen name="Menu" component={MenuScreen} options={{ title: 'Menu' }} />
            <Tab.Screen name="Cart" component={CartScreen} options={{ title: 'Keranjang' }} />
            <Tab.Screen name="Profile" component={ProfileScreen} options={{ title: 'Profil' }} />
        </Tab.Navigator>
    );
};

// Main Navigator
const AppNavigator = () => {
    const { isAuthenticated, loading } = useAuth();

    const screenOptions = {
        headerStyle: {
            backgroundColor: colors.primary,
        },
        headerTintColor: colors.text,
        headerTitleStyle: {
            fontWeight: '600',
        },
        contentStyle: {
            backgroundColor: colors.background,
        },
    };

    if (loading) {
        return (
            <NavigationContainer>
                <Stack.Navigator screenOptions={{ headerShown: false }}>
                    <Stack.Screen name="Splash" component={SplashScreen} />
                </Stack.Navigator>
            </NavigationContainer>
        );
    }

    return (
        <NavigationContainer>
            <Stack.Navigator screenOptions={screenOptions}>
                {!isAuthenticated ? (
                    // Auth Stack
                    <>
                        <Stack.Screen name="Login" component={LoginScreen} options={{ headerShown: false }} />
                        <Stack.Screen name="Register" component={RegisterScreen} options={{ title: 'Daftar' }} />
                    </>
                ) : (
                    // App Stack
                    <>
                        <Stack.Screen name="MainTabs" component={MainTabs} options={{ headerShown: false }} />
                        <Stack.Screen name="MenuDetail" component={MenuDetailScreen} options={{ title: 'Detail Menu' }} />
                        <Stack.Screen name="Checkout" component={CheckoutScreen} options={{ title: 'Checkout' }} />
                        <Stack.Screen name="Orders" component={OrdersScreen} options={{ title: 'Pesanan Saya' }} />
                        <Stack.Screen name="OrderDetail" component={OrderDetailScreen} options={{ title: 'Detail Pesanan' }} />
                        <Stack.Screen name="Reservations" component={ReservationsScreen} options={{ title: 'Reservasi Saya' }} />
                        <Stack.Screen name="CreateReservation" component={CreateReservationScreen} options={{ title: 'Buat Reservasi' }} />
                        <Stack.Screen name="Favorites" component={FavoritesScreen} options={{ title: 'Menu Favorit' }} />
                        <Stack.Screen name="Points" component={PointsScreen} options={{ title: 'Poin Saya' }} />
                        <Stack.Screen name="About" component={AboutScreen} options={{ title: 'Tentang Kami' }} />
                        <Stack.Screen name="Contact" component={ContactScreen} options={{ title: 'Hubungi Kami' }} />
                    </>
                )}
            </Stack.Navigator>
        </NavigationContainer>
    );
};

export default AppNavigator;

