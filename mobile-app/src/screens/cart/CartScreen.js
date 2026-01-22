import React, { useState, useEffect, useCallback } from 'react';
import {
    View,
    Text,
    FlatList,
    StyleSheet,
    TouchableOpacity,
    Image,
    ActivityIndicator,
    Alert,
    StatusBar,
} from 'react-native';
import { useFocusEffect } from '@react-navigation/native';
import Icon from 'react-native-vector-icons/Ionicons';
import LinearGradient from 'react-native-linear-gradient';
import Animated, { FadeInDown, SlideInDown } from 'react-native-reanimated';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { colors, spacing, fontSize, borderRadius, shadows } from '../../theme/colors';
import { useSettings } from '../../context/SettingsContext';
const BASE_URL = 'https://website-restaurant.up.railway.app/api';
const BASE_IMAGE_URL = 'https://website-restaurant.up.railway.app/storage/';
const CartScreen = ({ navigation }) => {
    const { isDarkMode, colors, t } = useSettings();
    const [cartItems, setCartItems] = useState([]);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(true);
    useFocusEffect(
        useCallback(() => {
            loadCart();
        }, [])
    );
    const getAuthConfig = async () => {
        const token = await AsyncStorage.getItem('auth_token');
        return {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        };
    };
    const loadCart = async () => {
        try {
            const config = await getAuthConfig();
            const response = await axios.get(`${BASE_URL}/cart`, config);
            const data = response.data;
            setCartItems(data.items || data.data || []);
            setTotal(data.total || 0);
        } catch (error) {
            console.log('Load cart error:', error);
            setCartItems([]);
        } finally {
            setLoading(false);
        }
    };
    const updateQuantity = async (id, newQuantity) => {
        if (newQuantity < 1) {
            removeItem(id);
            return;
        }
        try {
            const config = await getAuthConfig();
            await axios.put(`${BASE_URL}/cart/${id}`, { quantity: newQuantity }, config);
            loadCart();
        } catch (error) {
            Alert.alert('Error', 'Gagal mengubah jumlah');
        }
    };
    const removeItem = async (id) => {
        try {
            const config = await getAuthConfig();
            await axios.delete(`${BASE_URL}/cart/${id}`, config);
            loadCart();
        } catch (error) {
            Alert.alert('Error', 'Gagal menghapus item');
        }
    };
    const formatPrice = (price) => {
        return `Rp ${parseInt(price || 0).toLocaleString('id-ID')}`;
    };
    const getImageUrl = (imagePath) => {
        if (!imagePath) return 'https://via.placeholder.com/80';
        if (imagePath.startsWith('http')) return imagePath;
        const cleanPath = imagePath.replace(/^public\
        return `${BASE_IMAGE_URL}${cleanPath}`;
    };
    const renderCartItem = ({ item, index }) => (
        <Animated.View entering={FadeInDown.delay(index * 100).springify()} style={[styles.cartItem, { backgroundColor: colors.surface, borderColor: colors.border }]}>
            <Image
                source={{ uri: getImageUrl(item.menu_image || item.image_url) }}
                style={styles.itemImage}
                resizeMode="cover"
            />
            <View style={styles.itemInfo}>
                <Text style={[styles.itemName, { color: colors.text }]} numberOfLines={1}>{item.menu_name || item.name}</Text>
                <Text style={[styles.itemVariant, { color: colors.textSecondary }]}>{item.menu_category || item.category || 'Menu'}</Text>
                <Text style={[styles.itemPrice, { color: colors.primary }]}>{formatPrice(item.price)}</Text>
            </View>
            <View style={[styles.quantityControls, { backgroundColor: colors.surfaceLight }]}>
                <TouchableOpacity
                    style={styles.qtyBtn}
                    onPress={() => updateQuantity(item.id, item.quantity - 1)}
                >
                    <Icon name="remove" size={16} color={colors.text} />
                </TouchableOpacity>
                <Text style={[styles.qtyText, { color: colors.text }]}>{item.quantity}</Text>
                <TouchableOpacity
                    style={styles.qtyBtn}
                    onPress={() => updateQuantity(item.id, item.quantity + 1)}
                >
                    <Icon name="add" size={16} color={colors.text} />
                </TouchableOpacity>
            </View>
        </Animated.View>
    );
    const renderEmpty = () => (
        <View style={styles.emptyContainer}>
            <Icon name="cart-outline" size={80} color={colors.textSecondary} />
            <Text style={[styles.emptyTitle, { color: colors.text }]}>Keranjang Kosong</Text>
            <Text style={[styles.emptyText, { color: colors.textSecondary }]}>Tambahkan menu favorit Anda</Text>
            <TouchableOpacity
                style={[styles.browseButton, { backgroundColor: colors.primary }]}
                onPress={() => navigation.navigate('Menu')}
            >
                <Text style={styles.browseButtonText}>Jelajahi Menu</Text>
            </TouchableOpacity>
        </View>
    );
    if (loading) {
        return (
            <View style={[styles.loaderContainer, { backgroundColor: colors.background }]}>
                <ActivityIndicator size="large" color={colors.primary} />
            </View>
        );
    }
    return (
        <View style={[styles.container, { backgroundColor: colors.background }]}>
            <StatusBar barStyle={isDarkMode ? "light-content" : "dark-content"} backgroundColor={colors.background} />
            <View style={[styles.header, { borderBottomColor: colors.border }]}>
                <Text style={[styles.headerTitle, { color: colors.primary }]}>Keranjang</Text>
                <Text style={[styles.headerSubtitle, { color: colors.textSecondary }]}>{cartItems.length} items</Text>
            </View>
            {cartItems.length === 0 ? (
                renderEmpty()
            ) : (
                <>
                    <FlatList
                        data={cartItems}
                        keyExtractor={(item) => item.id?.toString() || Math.random().toString()}
                        renderItem={renderCartItem}
                        contentContainerStyle={styles.list}
                        showsVerticalScrollIndicator={false}
                    />
                    {}
                    <Animated.View entering={SlideInDown.springify()} style={[styles.bottomBar, { backgroundColor: colors.surface }]}>
                        <View style={styles.totalSection}>
                            <Text style={[styles.totalLabel, { color: colors.textSecondary }]}>Total</Text>
                            <Text style={[styles.totalValue, { color: colors.primary }]}>{formatPrice(total)}</Text>
                        </View>
                        <TouchableOpacity
                            style={styles.checkoutButton}
                            onPress={() => navigation.navigate('Checkout')}
                        >
                            <LinearGradient
                                colors={['#8B1538', '#A91D3A']}
                                style={styles.checkoutGradient}
                            >
                                <Text style={styles.checkoutText}>Checkout</Text>
                                <Icon name="arrow-forward" size={20} color="#FFF" />
                            </LinearGradient>
                        </TouchableOpacity>
                    </Animated.View>
                </>
            )}
        </View>
    );
};
const styles = StyleSheet.create({
    container: { flex: 1 },
    loaderContainer: { flex: 1, justifyContent: 'center', alignItems: 'center' },
    header: {
        padding: 20,
        paddingTop: 50,
        borderBottomWidth: 1,
    },
    headerTitle: {
        fontSize: 28,
        fontWeight: 'bold',
        fontFamily: 'serif',
    },
    headerSubtitle: {
        fontSize: 14,
        marginTop: 4,
    },
    list: {
        padding: 16,
        paddingBottom: 120,
    },
    cartItem: {
        flexDirection: 'row',
        padding: 12,
        borderRadius: 16,
        marginBottom: 12,
        borderWidth: 1,
        alignItems: 'center',
    },
    itemImage: {
        width: 70,
        height: 70,
        borderRadius: 12,
    },
    itemInfo: {
        flex: 1,
        marginLeft: 12,
    },
    itemName: {
        fontSize: 16,
        fontWeight: '600',
    },
    itemVariant: {
        fontSize: 12,
        marginTop: 2,
    },
    itemPrice: {
        fontSize: 15,
        fontWeight: 'bold',
        marginTop: 4,
    },
    quantityControls: {
        flexDirection: 'row',
        alignItems: 'center',
        borderRadius: 20,
        paddingHorizontal: 4,
        paddingVertical: 4,
    },
    qtyBtn: {
        width: 28,
        height: 28,
        borderRadius: 14,
        justifyContent: 'center',
        alignItems: 'center',
    },
    qtyText: {
        fontSize: 14,
        fontWeight: 'bold',
        marginHorizontal: 8,
    },
    emptyContainer: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        padding: 40,
    },
    emptyTitle: {
        fontSize: 20,
        fontWeight: '600',
        marginTop: 20,
    },
    emptyText: {
        fontSize: 14,
        marginTop: 8,
        textAlign: 'center',
    },
    browseButton: {
        paddingVertical: 14,
        paddingHorizontal: 30,
        borderRadius: 25,
        marginTop: 24,
    },
    browseButtonText: {
        color: '#FFF',
        fontWeight: 'bold',
        fontSize: 15,
    },
    bottomBar: {
        position: 'absolute',
        bottom: 0,
        left: 0,
        right: 0,
        flexDirection: 'row',
        padding: 16,
        paddingBottom: 24,
        alignItems: 'center',
        borderTopWidth: 1,
        borderTopColor: '#EEE',
        elevation: 10,
    },
    totalSection: {
        flex: 1,
    },
    totalLabel: {
        fontSize: 12,
    },
    totalValue: {
        fontSize: 22,
        fontWeight: 'bold',
    },
    checkoutButton: {
        borderRadius: 25,
        overflow: 'hidden',
    },
    checkoutGradient: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingVertical: 14,
        paddingHorizontal: 24,
    },
    checkoutText: {
        color: '#FFF',
        fontWeight: 'bold',
        fontSize: 16,
        marginRight: 8,
    },
});
export default CartScreen;