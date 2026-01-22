import React, { useState, useEffect, useCallback } from 'react';
import {
    View,
    Text,
    FlatList,
    StyleSheet,
    TouchableOpacity,
    TextInput,
    Image,
    Dimensions,
    StatusBar,
    RefreshControl,
    ActivityIndicator,
    Alert
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';
import Icon from 'react-native-vector-icons/Ionicons';
import { useFocusEffect } from '@react-navigation/native';
import { useCart } from '../../context/CartContext';
import FloatingCartButton from '../../components/FloatingCartButton';
import CartPopup from '../../components/CartPopup';
import ReceiptModal from '../../components/ReceiptModal';
const { width } = Dimensions.get('window');
const CARD_WIDTH = (width - 48) / 2;
const BASE_URL = 'https://website-restaurant.up.railway.app/api';
const BASE_IMAGE_URL = 'https://website-restaurant.up.railway.app/storage/';
const CATEGORIES = ['Semua', 'Nasi & Mie', 'Hidangan Utama', 'Minuman'];
const MenuScreen = ({ navigation }) => {
    const { addToCart, openCart, isCartOpen, closeCart, checkout } = useCart();
    const [menus, setMenus] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [selectedCategory, setSelectedCategory] = useState('Semua');
    const [searchText, setSearchText] = useState('');
    const [filteredMenus, setFilteredMenus] = useState([]);
    const [showReceipt, setShowReceipt] = useState(false);
    const [receiptData, setReceiptData] = useState(null);
    useFocusEffect(
        useCallback(() => {
            loadMenus();
        }, [])
    );
    useEffect(() => {
        let result = menus;
        if (selectedCategory !== 'Semua') {
            result = result.filter(m => m.category === selectedCategory || m.category?.name === selectedCategory);
        }
        if (searchText) {
            result = result.filter(m => m.name.toLowerCase().includes(searchText.toLowerCase()));
        }
        setFilteredMenus(result);
    }, [menus, selectedCategory, searchText]);
    const loadMenus = async () => {
        try {
            const token = await AsyncStorage.getItem('auth_token');
            const config = {
                headers: { 'Accept': 'application/json' }
            };
            if (token) {
                config.headers['Authorization'] = `Bearer ${token}`;
            }
            const response = await axios.get(`${BASE_URL}/menus`, config);
            console.log('API Response Status:', response.status);
            let data = [];
            if (response.data && Array.isArray(response.data)) {
                data = response.data;
            } else if (response.data?.data && Array.isArray(response.data.data)) {
                data = response.data.data;
            } else if (response.data?.menus && Array.isArray(response.data.menus)) {
                data = response.data.menus;
            }
            setMenus(data);
        } catch (error) {
            console.error('Fetch Error:', error);
            Alert.alert(
                "Info",
                "Gagal mengambil data menu terbaru. Periksa koneksi internet Anda."
            );
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };
    const formatPrice = (price) => {
        return `Rp ${parseInt(price).toLocaleString('id-ID')}`;
    };
    const getImageUrl = (imagePath) => {
        if (!imagePath) return null;
        if (imagePath.startsWith('http')) return imagePath;
        let cleanPath = imagePath.replace(/^public\
        cleanPath = cleanPath.startsWith('/') ? cleanPath.substring(1) : cleanPath;
        return `${BASE_IMAGE_URL}${cleanPath}`;
    };
    const renderCategory = (category, index) => {
        const isSelected = selectedCategory === category;
        return (
            <TouchableOpacity
                key={index}
                style={[styles.categoryPill, isSelected && styles.categoryPillActive]}
                onPress={() => setSelectedCategory(category)}
            >
                <Text style={[styles.categoryText, isSelected && styles.categoryTextActive]}>
                    {category}
                </Text>
            </TouchableOpacity>
        );
    };
    const renderMenuItem = ({ item }) => (
        <TouchableOpacity
            style={styles.menuCard}
            onPress={() => navigation.navigate('MenuDetail', { slug: item.slug || item.id })}
            activeOpacity={0.9}
        >
            <View style={styles.imageContainer}>
                <Image
                    source={item.image_url ? { uri: getImageUrl(item.image_url) } : null}
                    style={styles.menuImage}
                />
                {!item.image_url && (
                    <View style={[styles.menuImage, { position: 'absolute', alignItems: 'center', justifyContent: 'center' }]}>
                        <Icon name="restaurant" size={30} color="#CCC" />
                    </View>
                )}
            </View>
            <View style={styles.menuInfo}>
                <Text style={styles.menuName} numberOfLines={2}>{item.name}</Text>
                {item.rating && (
                    <View style={styles.ratingRow}>
                        <Icon name="star" size={12} color="#FFD700" />
                        <Text style={styles.ratingText}>{item.rating}</Text>
                    </View>
                )}
                <View style={styles.priceRow}>
                    <Text style={styles.menuPrice}>{formatPrice(item.price)}</Text>
                    <TouchableOpacity
                        style={styles.addButton}
                        onPress={(e) => {
                            e.stopPropagation();
                            addToCart(item);
                        }}
                    >
                        <Icon name="add" size={16} color="#FFF" />
                    </TouchableOpacity>
                </View>
            </View>
        </TouchableOpacity>
    );
    const renderEmpty = () => (
        <View style={styles.emptyContainer}>
            <Icon name="fast-food-outline" size={64} color="#CCC" />
            <Text style={styles.emptyText}>Menu belum tersedia di server.</Text>
            <TouchableOpacity style={styles.retryBtn} onPress={() => { setLoading(true); loadMenus(); }}>
                <Text style={styles.retryText}>Muat Ulang</Text>
            </TouchableOpacity>
        </View>
    );
    if (loading && !refreshing) {
        return (
            <View style={styles.loadingContainer}>
                <ActivityIndicator size="large" color="#8B1538" />
                <Text style={{ marginTop: 10, color: '#666' }}>Mengambil Data Menu...</Text>
            </View>
        );
    }
    const handleCheckout = async () => {
        const result = await checkout();
        if (result.success) {
            setReceiptData(result.receipt);
            setShowReceipt(true);
        } else {
            Alert.alert('Error', result.error || 'Checkout gagal');
        }
    };
    return (
        <View style={styles.container}>
            <StatusBar barStyle="light-content" backgroundColor="#8B1538" />
            <View style={styles.header}>
                <Text style={styles.headerTitle}>Menu Kami</Text>
                <Text style={styles.headerSubtitle}>Pilih menu favorit Anda</Text>
            </View>
            <View style={styles.searchRow}>
                <View style={styles.searchBar}>
                    <Icon name="search-outline" size={18} color="#999" />
                    <TextInput
                        placeholder="Cari menu..."
                        placeholderTextColor="#999"
                        style={styles.searchInput}
                        value={searchText}
                        onChangeText={setSearchText}
                    />
                </View>
                <TouchableOpacity style={styles.filterBtn}>
                    <Icon name="options-outline" size={20} color="#8B1538" />
                </TouchableOpacity>
            </View>
            <View style={styles.categoryRow}>
                {CATEGORIES.map(renderCategory)}
            </View>
            <FlatList
                data={filteredMenus}
                keyExtractor={(item) => (item.id || Math.random()).toString()}
                renderItem={renderMenuItem}
                numColumns={2}
                columnWrapperStyle={styles.menuRow}
                contentContainerStyle={styles.menuList}
                showsVerticalScrollIndicator={false}
                ListEmptyComponent={!loading && renderEmpty}
                refreshControl={
                    <RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); loadMenus(); }} colors={['#8B1538']} />
                }
            />
            {}
            <FloatingCartButton onPress={openCart} />
            {}
            <CartPopup
                visible={isCartOpen}
                onClose={closeCart}
                onCheckout={handleCheckout}
            />
            {}
            <ReceiptModal
                visible={showReceipt}
                onClose={() => setShowReceipt(false)}
                receiptData={receiptData}
            />
        </View>
    );
};
const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#FAF9F6' },
    loadingContainer: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#FAF9F6' },
    header: { backgroundColor: '#8B1538', paddingTop: 20, paddingBottom: 20, paddingHorizontal: 20, borderBottomLeftRadius: 24, borderBottomRightRadius: 24 },
    headerTitle: { fontSize: 24, fontWeight: 'bold', color: '#FFF' },
    headerSubtitle: { fontSize: 13, color: 'rgba(255,255,255,0.8)', marginTop: 4 },
    searchRow: { flexDirection: 'row', padding: 16, alignItems: 'center', marginTop: 0 },
    searchBar: { flex: 1, flexDirection: 'row', alignItems: 'center', backgroundColor: '#FFF', paddingHorizontal: 14, paddingVertical: 10, borderRadius: 12, marginRight: 10, elevation: 1, borderWidth: 1, borderColor: '#E5E7EB' },
    searchInput: { flex: 1, marginLeft: 8, fontSize: 14, color: '#333', padding: 0 },
    filterBtn: { backgroundColor: '#FFF', padding: 10, borderRadius: 12, elevation: 1, borderWidth: 1, borderColor: '#E5E7EB' },
    categoryRow: { flexDirection: 'row', paddingHorizontal: 16, marginBottom: 16 },
    categoryPill: { paddingHorizontal: 16, paddingVertical: 8, borderRadius: 20, marginRight: 8, backgroundColor: '#FFF', borderWidth: 1, borderColor: '#E5E7EB' },
    categoryPillActive: { backgroundColor: '#8B1538', borderColor: '#8B1538' },
    categoryText: { fontSize: 13, fontWeight: '500', color: '#666' },
    categoryTextActive: { color: '#FFF' },
    menuList: { paddingHorizontal: 16, paddingBottom: 100 },
    menuRow: { justifyContent: 'space-between', marginBottom: 16 },
    menuCard: { width: CARD_WIDTH, backgroundColor: '#FFF', borderRadius: 16, overflow: 'hidden', elevation: 2, shadowColor: "#000", shadowOpacity: 0.1, shadowRadius: 5 },
    imageContainer: { width: '100%', height: 120, backgroundColor: '#F3F4F6', position: 'relative' },
    menuImage: { width: '100%', height: '100%', resizeMode: 'cover' },
    menuInfo: { padding: 12 },
    menuName: { fontSize: 14, fontWeight: 'bold', color: '#1F2937', marginBottom: 4, minHeight: 40 },
    ratingRow: { flexDirection: 'row', alignItems: 'center', marginBottom: 8 },
    ratingText: { fontSize: 11, color: '#666', marginLeft: 4, fontWeight: '600' },
    priceRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
    menuPrice: { fontSize: 14, fontWeight: 'bold', color: '#8B1538' },
    addButton: { backgroundColor: '#8B1538', width: 28, height: 28, borderRadius: 8, justifyContent: 'center', alignItems: 'center' },
    emptyContainer: { alignItems: 'center', justifyContent: 'center', padding: 40, marginTop: 40 },
    emptyText: { color: '#9CA3AF', fontSize: 16, marginTop: 16, marginBottom: 16 },
    retryBtn: { paddingHorizontal: 20, paddingVertical: 10, backgroundColor: '#8B1538', borderRadius: 8 },
    retryText: { color: 'white', fontWeight: 'bold' }
});
export default MenuScreen;