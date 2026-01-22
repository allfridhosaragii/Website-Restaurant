import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { useAuth } from '../context/AuthContext';
import { colors } from '../theme/colors';
import LiquidGlassTabBar from '../components/LiquidGlassTabBar';
import SplashScreen from '../screens/auth/SplashScreen';
import LoginScreen from '../screens/auth/LoginScreen';
import RegisterScreen from '../screens/auth/RegisterScreen';
import ForgotPasswordScreen from '../screens/auth/ForgotPasswordScreen';
import HomeScreen from '../screens/home/HomeScreen';
import MenuScreen from '../screens/menu/MenuScreen';
import MenuDetailScreen from '../screens/menu/MenuDetailScreen';
import CartScreen from '../screens/cart/CartScreen';
import CheckoutScreen from '../screens/cart/CheckoutScreen';
import ProfileScreen from '../screens/profile/ProfileScreen';
import EditProfileScreen from '../screens/profile/EditProfileScreen';
import OrdersScreen from '../screens/orders/OrdersScreen';
import OrderDetailScreen from '../screens/orders/OrderDetailScreen';
import ReservationsScreen from '../screens/reservations/ReservationsScreen';
import CreateReservationScreen from '../screens/reservations/CreateReservationScreen';
import FavoritesScreen from '../screens/favorites/FavoritesScreen';
import PointsScreen from '../screens/points/PointsScreen';
import AboutScreen from '../screens/about/AboutScreen';
import SettingsScreen from '../screens/settings/SettingsScreen';
import AdminDashboardScreen from '../screens/admin/AdminDashboardScreen';
import AdminMenuScreen from '../screens/admin/AdminMenuScreen';
import AdminOrderScreen from '../screens/admin/AdminOrderScreen';
import OrderSuccessScreen from '../screens/orders/OrderSuccessScreen';
const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();
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
            <Tab.Screen name="Reservations" component={ReservationsScreen} options={{ title: 'Reservasi' }} />
            <Tab.Screen name="Points" component={PointsScreen} options={{ title: 'Poin Saya' }} />
            <Tab.Screen name="Profile" component={ProfileScreen} options={{ title: 'Profil' }} />
        </Tab.Navigator>
    );
};
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
                    <>
                        <Stack.Screen name="Login" component={LoginScreen} options={{ headerShown: false }} />
                        <Stack.Screen name="Register" component={RegisterScreen} options={{ title: 'Daftar' }} />
                        <Stack.Screen name="ForgotPassword" component={ForgotPasswordScreen} options={{ headerShown: false }} />
                    </>
                ) : (
                    <>
                        <Stack.Screen name="MainTabs" component={MainTabs} options={{ headerShown: false }} />
                        <Stack.Screen name="MenuDetail" component={MenuDetailScreen} options={{ headerShown: false }} />
                        <Stack.Screen name="Checkout" component={CheckoutScreen} options={{ title: 'Checkout' }} />
                        <Stack.Screen name="OrderSuccess" component={OrderSuccessScreen} options={{ headerShown: false }} />
                        <Stack.Screen name="Orders" component={OrdersScreen} options={{ title: 'Pesanan Saya' }} />
                        <Stack.Screen name="OrderDetail" component={OrderDetailScreen} options={{ title: 'Detail Pesanan' }} />
                        <Stack.Screen name="Reservations" component={ReservationsScreen} options={{ title: 'Reservasi Saya' }} />
                        <Stack.Screen name="CreateReservation" component={CreateReservationScreen} options={{ title: 'Buat Reservasi' }} />
                        <Stack.Screen name="Settings" component={SettingsScreen} options={{ headerShown: false }} />
                        <Stack.Screen name="Favorites" component={FavoritesScreen} options={{ title: 'Menu Favorit' }} />
                        <Stack.Screen name="Points" component={PointsScreen} options={{ title: 'Poin Saya' }} />
                        <Stack.Screen name="EditProfile" component={EditProfileScreen} options={{ headerShown: false }} />
                        <Stack.Screen name="About" component={AboutScreen} options={{ title: 'Tentang Kami' }} />
                        <Stack.Screen name="AdminDashboard" component={AdminDashboardScreen} options={{ headerShown: false }} />
                        <Stack.Screen name="AdminMenus" component={AdminMenuScreen} options={{ headerShown: false }} />
                        <Stack.Screen name="AdminOrders" component={AdminOrderScreen} options={{ headerShown: false }} />
                    </>
                )}
            </Stack.Navigator>
        </NavigationContainer>
    );
};
export default AppNavigator;