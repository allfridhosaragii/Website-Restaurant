import React, { useState, useEffect, useCallback } from 'react';
import {
    View,
    Text,
    FlatList,
    StyleSheet,
    TouchableOpacity,
    RefreshControl,
    ActivityIndicator,
    Image,
    Alert,
    StatusBar,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { colors, spacing, fontSize, borderRadius } from '../../theme/colors';
import { useAuth } from '../../context/AuthContext';
import { useSettings } from '../../context/SettingsContext';

const BASE_URL = 'https://website-restaurant.up.railway.app/api';
const BASE_IMAGE_URL = 'https://website-restaurant.up.railway.app/storage/';

const FavoritesScreen = ({ navigation }) => {
    const { user, isGuest } = useAuth();
    const { isDarkMode, colors, t } = useSettings();
    const [favorites, setFavorites] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);

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

    const loadFavorites = useCallback(async () => {
        if (isGuest) {
            setLoading(false);
            return;
        }
        try {
            const config = await getAuthConfig();
            const response = await axios.get(`${BASE_URL}/favorites`, config);
            const data = response.data?.favorites || response.data?.data || response.data || [];
            setFavorites(Array.isArray(data) ? data : []);
        } catch (error) {
            console.log('Load favorites error:', error);
            setFavorites([]);
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    }, [isGuest]);

    useEffect(() => {
        loadFavorites();
    }, [loadFavorites]);

    const onRefresh = () => {
        setRefreshing(true);
        loadFavorites();
    };

    const removeFavorite = async (menuId) => {
        try {
            const config = await getAuthConfig();
            await axios.post(`${BASE_URL}/favorites/${menuId}`, {}, config);
            setFavorites(prev => prev.filter(f => f.menu_id !== menuId && f.id !== menuId));
            Alert.alert('Berhasil', 'Dihapus dari favorit');
        } catch (error) {
            Alert.alert('Error', 'Gagal menghapus favorit');
        }
    };

    const formatPrice = (price) => {
        return `Rp ${parseInt(price || 0).toLocaleString('id-ID')}`;
    };

    const getImageUrl = (imagePath) => {
        if (!imagePath) return 'https://via.placeholder.com/100';
        if (imagePath.startsWith('http')) return imagePath;
        const cleanPath = imagePath.replace(/^public\//, '');
        return `${BASE_IMAGE_URL}${cleanPath}`;
    };

    if (isGuest) {
        return (
            <View style={[styles.emptyContainer, { backgroundColor: colors.background }]}>
                <StatusBar barStyle={isDarkMode ? "light-content" : "dark-content"} backgroundColor={colors.background} />
                <Icon name="heart-outline" size={64} color={colors.textSecondary} />
                <Text style={[styles.emptyTitle, { color: colors.text }]}>Login Diperlukan</Text>
                <Text style={[styles.emptyText, { color: colors.textSecondary }]}>Silakan login untuk melihat favorit Anda</Text>
                <TouchableOpacity
                    style={[styles.loginButton, { backgroundColor: colors.primary }]}
                    onPress={() => navigation.navigate('Login')}
                >
                    <Text style={styles.loginButtonText}>Login</Text>
                </TouchableOpacity>
            </View>
        );
    }

    if (loading) {
        return (
            <View style={[styles.loader, { backgroundColor: colors.background }]}>
                <ActivityIndicator size="large" color={colors.primary} />
            </View>
        );
    }

    const renderItem = ({ item }) => {
        const menu = item.menu || item;
        return (
            <View style={[styles.card, { backgroundColor: colors.surface, borderColor: colors.border }]}>
                <Image
                    source={{ uri: getImageUrl(menu.image_url || menu.image) }}
                    style={styles.image}
                />
                <View style={styles.cardContent}>
                    <Text style={[styles.menuName, { color: colors.text }]}>{menu.name}</Text>
                    <Text style={[styles.menuCategory, { color: colors.textSecondary }]}>{menu.category}</Text>
                    <Text style={[styles.menuPrice, { color: colors.primary }]}>{formatPrice(menu.price)}</Text>
                </View>
                <TouchableOpacity
                    style={styles.removeButton}
                    onPress={() => removeFavorite(item.menu_id || menu.id)}
                >
                    <Icon name="heart" size={24} color="#E53935" />
                </TouchableOpacity>
            </View>
        );
    };

    return (
        <View style={[styles.container, { backgroundColor: colors.background }]}>
            <StatusBar barStyle={isDarkMode ? "light-content" : "dark-content"} backgroundColor={colors.background} />
            <View style={[styles.header, { borderBottomColor: colors.border }]}>
                <Text style={[styles.headerTitle, { color: colors.primary }]}>Favorit</Text>
                <Text style={[styles.headerSubtitle, { color: colors.textSecondary }]}>{favorites.length} menu</Text>
            </View>

            {favorites.length === 0 ? (
                <View style={styles.emptyContainer}>
                    <Icon name="heart-outline" size={64} color={colors.textSecondary} />
                    <Text style={[styles.emptyTitle, { color: colors.text }]}>Belum Ada Favorit</Text>
                    <Text style={[styles.emptyText, { color: colors.textSecondary }]}>Tambahkan menu favoritmu!</Text>
                    <TouchableOpacity
                        style={[styles.browseButton, { backgroundColor: colors.primary }]}
                        onPress={() => navigation.navigate('Menu')}
                    >
                        <Text style={styles.browseButtonText}>Jelajahi Menu</Text>
                    </TouchableOpacity>
                </View>
            ) : (
                <FlatList
                    data={favorites}
                    keyExtractor={(item) => String(item.id || item.menu_id)}
                    renderItem={renderItem}
                    contentContainerStyle={styles.list}
                    refreshControl={
                        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[colors.primary]} />
                    }
                />
            )}
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
    },
    loader: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
    },
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
    },
    card: {
        flexDirection: 'row',
        borderRadius: 16,
        marginBottom: 12,
        overflow: 'hidden',
        alignItems: 'center',
        borderWidth: 1,
    },
    image: {
        width: 80,
        height: 80,
    },
    cardContent: {
        flex: 1,
        padding: 12,
    },
    menuName: {
        fontSize: 16,
        fontWeight: '600',
    },
    menuCategory: {
        fontSize: 12,
        marginTop: 2,
    },
    menuPrice: {
        fontSize: 15,
        fontWeight: '600',
        marginTop: 4,
    },
    removeButton: {
        padding: 16,
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
        textAlign: 'center',
        marginTop: 8,
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
    loginButton: {
        paddingVertical: 14,
        paddingHorizontal: 30,
        borderRadius: 25,
        marginTop: 24,
    },
    loginButtonText: {
        color: '#FFF',
        fontWeight: 'bold',
        fontSize: 15,
    },
});

export default FavoritesScreen;
