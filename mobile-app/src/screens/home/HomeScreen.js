import React, { useState, useCallback, useEffect } from 'react';
import {
    View,
    Text,
    ScrollView,
    StyleSheet,
    TouchableOpacity,
    TextInput,
    Image,
    Dimensions,
    StatusBar,
    RefreshControl,
    Alert,
    ActivityIndicator
} from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import Icon from 'react-native-vector-icons/Ionicons';
import MaterialIcon from 'react-native-vector-icons/MaterialCommunityIcons';
import LinearGradient from 'react-native-linear-gradient';
import { useAuth } from '../../context/AuthContext';

const { width } = Dimensions.get('window');
const BASE_URL = 'https://website-restaurant.up.railway.app/api';
const BASE_IMAGE_URL = 'https://website-restaurant.up.railway.app/storage/';

const HomeScreen = ({ navigation }) => {
    const { user } = useAuth();
    const [refreshing, setRefreshing] = useState(false);
    const [popularMenus, setPopularMenus] = useState([]);
    const [loadingMenus, setLoadingMenus] = useState(true);
    const [dashboardStats, setDashboardStats] = useState({ points: 0, total_orders: 0, total_reservations: 0 });

    const onRefresh = useCallback(() => {
        setRefreshing(true);
        fetchPopularMenus().finally(() => setRefreshing(false));
    }, []);

    useEffect(() => {
        fetchPopularMenus();
        fetchDashboardStats();
    }, []);

    const fetchDashboardStats = async () => {
        try {
            const token = await AsyncStorage.getItem('auth_token');
            if (!token) return;

            const response = await axios.get(`${BASE_URL}/dashboard`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            setDashboardStats(response.data);
        } catch (error) {
            console.log('Dashboard stats error:', error);
        }
    };

    const fetchPopularMenus = async () => {
        try {
            const token = await AsyncStorage.getItem('auth_token');
            const config = {
                headers: { 'Accept': 'application/json' }
            };
            if (token) {
                config.headers['Authorization'] = `Bearer ${token}`;
            }

            const response = await axios.get(`${BASE_URL}/menus`, config);

            let data = [];
            if (response.data && Array.isArray(response.data)) {
                data = response.data;
            } else if (response.data?.data && Array.isArray(response.data.data)) {
                data = response.data.data;
            } else if (response.data?.menus && Array.isArray(response.data.menus)) {
                data = response.data.menus;
            }

            // Ambil 5 menu pertama sebagai contoh "Populer"
            // Idealnya backend punya endpoint /menus/popular
            setPopularMenus(data.slice(0, 5));
        } catch (error) {
            console.error('Fetch Home Menu Error:', error);
            // Silent fail for home screen usually better, or user friendly toast
        } finally {
            setLoadingMenus(false);
        }
    };

    const getImageUrl = (imagePath) => {
        if (!imagePath) return null;
        if (imagePath.startsWith('http')) return imagePath;
        let cleanPath = imagePath.replace(/^public\//, '');
        cleanPath = cleanPath.startsWith('/') ? cleanPath.substring(1) : cleanPath;
        return `${BASE_IMAGE_URL}${cleanPath}`;
    };

    const formatPrice = (price) => {
        return `Rp ${parseInt(price).toLocaleString('id-ID')}`;
    };

    return (
        <View style={styles.container}>
            <StatusBar barStyle="dark-content" backgroundColor="#FFFFFF" />

            <ScrollView
                showsVerticalScrollIndicator={false}
                refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={['#8B1538']} />}
            >
                {/* ===== HEADER ===== */}
                <View style={styles.header}>
                    <View>
                        <Text style={styles.logoText}>Culinaire.</Text>
                        <Text style={styles.subLogoText}>Dine In • Take Away</Text>
                    </View>
                    <TouchableOpacity>
                        <Icon name="notifications-outline" size={24} color="#333" />
                    </TouchableOpacity>
                </View>

                {/* ===== SEARCH BAR ===== */}
                <View style={styles.searchContainer}>
                    <TouchableOpacity
                        style={styles.searchBar}
                        onPress={() => navigation.navigate('Menu')}
                        activeOpacity={0.9}
                    >
                        <Icon name="search-outline" size={18} color="#999" />
                        <Text style={styles.searchPlaceholder}>Cari menu favorit...</Text>
                    </TouchableOpacity>
                </View>

                {/* ===== MEMBER CARD ===== */}
                <View style={styles.sectionPadding}>
                    <LinearGradient
                        colors={['#8B1538', '#5C0D24']}
                        start={{ x: 0, y: 0 }}
                        end={{ x: 1, y: 1 }}
                        style={styles.memberCard}
                    >
                        <View style={styles.platinumBadge}>
                            <MaterialIcon name="crown" size={12} color="#D4AF37" />
                            <Text style={styles.platinumText}>PLATINUM MEMBER</Text>
                        </View>
                        <View style={styles.memberRow}>
                            <View>
                                <Text style={styles.memberName}>{user?.name || 'Tamu Culinaire'}</Text>
                                <Text style={styles.memberPoints}>{(dashboardStats.points || 0).toLocaleString()}</Text>
                                <Text style={styles.memberPointsLabel}>Poin Available</Text>
                            </View>
                            <TouchableOpacity style={styles.redeemBtn}>
                                <Text style={styles.redeemBtnText}>Tukar Poin</Text>
                            </TouchableOpacity>
                        </View>
                    </LinearGradient>
                </View>

                {/* ===== PROMO BANNER ===== */}
                <ScrollView
                    horizontal
                    showsHorizontalScrollIndicator={false}
                    contentContainerStyle={styles.promoContainer}
                >
                    <LinearGradient colors={['#8B1538', '#6A1030']} style={styles.promoCard}>
                        <View style={styles.promoContent}>
                            <Text style={styles.promoTitle}>Diskon 30% Hari Ini!</Text>
                            <Text style={styles.promoSubtitle}>Untuk semua menu pilihan</Text>
                            <TouchableOpacity style={styles.promoBtn} onPress={() => navigation.navigate('Menu')}>
                                <Text style={styles.promoBtnText}>Pesan Sekarang</Text>
                            </TouchableOpacity>
                        </View>
                        <Image
                            source={{ uri: 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=300' }}
                            style={styles.promoImage}
                        />
                    </LinearGradient>

                    <LinearGradient colors={['#1A1A1A', '#000']} style={styles.promoCard}>
                        <View style={styles.promoContent}>
                            <Text style={styles.promoTitle}>Makan Gratis!</Text>
                            <Text style={styles.promoSubtitle}>Minimal belanja Rp 200rb</Text>
                            <TouchableOpacity style={styles.promoBtn} onPress={() => navigation.navigate('Menu')}>
                                <Text style={styles.promoBtnText}>Pesan Sekarang</Text>
                            </TouchableOpacity>
                        </View>
                    </LinearGradient>
                </ScrollView>

                {/* ===== LAYANAN CEPAT ===== */}
                <View style={styles.sectionPadding}>
                    <Text style={styles.sectionTitle}>Layanan Cepat</Text>
                    <View style={styles.quickGrid}>
                        <TouchableOpacity style={styles.quickItem} onPress={() => navigation.navigate('Menu')}>
                            <View style={[styles.quickIcon, { backgroundColor: '#8B1538' }]}>
                                <MaterialIcon name="silverware-fork-knife" size={24} color="#FFF" />
                            </View>
                            <Text style={styles.quickLabel}>Pesan Menu</Text>
                        </TouchableOpacity>

                        <TouchableOpacity style={styles.quickItem} onPress={() => navigation.navigate('Reservations')}>
                            <View style={[styles.quickIcon, { backgroundColor: '#FF8C42' }]}>
                                <MaterialIcon name="calendar-clock" size={24} color="#FFF" />
                            </View>
                            <Text style={styles.quickLabel}>Reservasi</Text>
                        </TouchableOpacity>

                        <TouchableOpacity style={styles.quickItem} onPress={() => navigation.navigate('Menu')}>
                            <View style={[styles.quickIcon, { backgroundColor: '#9C27B0' }]}>
                                <MaterialIcon name="percent" size={24} color="#FFF" />
                            </View>
                            <Text style={styles.quickLabel}>Promo</Text>
                        </TouchableOpacity>

                        <TouchableOpacity style={styles.quickItem}>
                            <View style={[styles.quickIcon, { backgroundColor: '#D4AF37' }]}>
                                <MaterialIcon name="gift" size={24} color="#FFF" />
                            </View>
                            <Text style={styles.quickLabel}>Voucher</Text>
                        </TouchableOpacity>
                    </View>
                </View>

                {/* ===== STATS ROW ===== */}
                <View style={styles.statsContainer}>
                    <View style={[styles.statCard, { backgroundColor: '#E8F5E9' }]}>
                        <Icon name="star" size={20} color="#4CAF50" />
                        <Text style={styles.statLabel}>Rating</Text>
                        <Text style={styles.statValue}>4.9</Text>
                    </View>
                    <View style={[styles.statCard, { backgroundColor: '#FFF3E0' }]}>
                        <Icon name="flame" size={20} color="#FF9800" />
                        <Text style={styles.statLabel}>Hari Ini</Text>
                        <Text style={styles.statValue}>342+</Text>
                    </View>
                    <View style={[styles.statCard, { backgroundColor: '#FCE4EC' }]}>
                        <Icon name="heart" size={20} color="#E91E63" />
                        <Text style={styles.statLabel}>Favorit</Text>
                        <Text style={styles.statValue}>Best</Text>
                    </View>
                </View>

                {/* ===== FLASH SALE BANNER ===== */}
                <View style={styles.sectionPadding}>
                    <LinearGradient
                        colors={['#FFD54F', '#FFC107']}
                        start={{ x: 0, y: 0 }}
                        end={{ x: 1, y: 0 }}
                        style={styles.flashSaleCard}
                    >
                        <View style={styles.flashSaleContent}>
                            <View style={styles.flashSaleBadge}>
                                <Text style={styles.flashSaleBadgeText}>FLASH SALE</Text>
                            </View>
                            <Text style={styles.flashSaleTitle}>Diskon hingga 50%</Text>
                            <Text style={styles.flashSaleSubtitle}>Khusus hari ini untuk menu pilihan</Text>
                            <TouchableOpacity style={styles.flashSaleBtn} onPress={() => navigation.navigate('Menu')}>
                                <Text style={styles.flashSaleBtnText}>Lihat Menu</Text>
                                <Icon name="arrow-forward" size={14} color="#FFF" />
                            </TouchableOpacity>
                        </View>
                        <Text style={styles.fireEmoji}>🔥</Text>
                    </LinearGradient>
                </View>

                {/* ===== MENU PALING POPULER (REAL DATA) ===== */}
                <View style={styles.sectionPadding}>
                    <View style={styles.sectionHeader}>
                        <View>
                            <Text style={styles.sectionTitle}>Menu Paling Populer</Text>
                            <Text style={styles.sectionSubtitle}>Berdasarkan pesanan terbanyak</Text>
                        </View>
                        <TouchableOpacity onPress={() => navigation.navigate('Menu')}>
                            <Text style={styles.seeAllText}>Lihat Semua &gt;</Text>
                        </TouchableOpacity>
                    </View>
                </View>

                {loadingMenus ? (
                    <View style={{ padding: 20, alignItems: 'center' }}>
                        <ActivityIndicator size="small" color="#8B1538" />
                    </View>
                ) : (
                    <ScrollView
                        horizontal
                        showsHorizontalScrollIndicator={false}
                        contentContainerStyle={styles.menuScrollContainer}
                    >
                        {popularMenus.length > 0 ? (
                            popularMenus.map((item, index) => (
                                <TouchableOpacity
                                    key={index}
                                    style={styles.menuCard}
                                    onPress={() => navigation.navigate('MenuDetail', { slug: item.slug || item.id })}
                                >
                                    <View style={styles.menuImageContainer}>
                                        <Image
                                            source={item.image_url ? { uri: getImageUrl(item.image_url) } : null}
                                            style={styles.menuImage}
                                        />
                                        {!item.image_url && (
                                            <View style={[styles.menuImage, { position: 'absolute', alignItems: 'center', justifyContent: 'center', backgroundColor: '#EEE' }]}>
                                                <Icon name="restaurant" size={30} color="#CCC" />
                                            </View>
                                        )}
                                        {/* Badge random atau based on logic */}
                                        {index === 0 && (
                                            <View style={styles.bestSellerBadge}>
                                                <Text style={styles.bestSellerText}>Best Seller</Text>
                                            </View>
                                        )}
                                    </View>
                                    <View style={styles.menuInfo}>
                                        <Text style={styles.menuName} numberOfLines={1}>{item.name}</Text>
                                        <View style={styles.menuRatingRow}>
                                            <Icon name="star" size={12} color="#FFD700" />
                                            <Text style={styles.menuRating}>4.8</Text>
                                            <Text style={styles.menuOrders}>{150 + index * 20}+ pesanan</Text>
                                        </View>
                                        <View style={styles.menuPriceRow}>
                                            <Text style={styles.menuPrice}>{formatPrice(item.price)}</Text>
                                            <TouchableOpacity style={styles.addBtn}>
                                                <Icon name="add" size={18} color="#FFF" />
                                            </TouchableOpacity>
                                        </View>
                                    </View>
                                </TouchableOpacity>
                            ))
                        ) : (
                            <View style={{ paddingHorizontal: 20 }}>
                                <Text style={{ color: '#999', fontStyle: 'italic' }}>Belum ada menu populer.</Text>
                            </View>
                        )}
                    </ScrollView>
                )}

                {/* Dots indicator - Only show if menus exist */}
                {popularMenus.length > 0 && (
                    <View style={styles.dotsContainer}>
                        {popularMenus.slice(0, 3).map((_, i) => (
                            <View key={i} style={[styles.dot, i === 0 && styles.dotActive]} />
                        ))}
                    </View>
                )}

                {/* ===== MENGAPA CULINAIRE ===== */}
                <View style={styles.sectionPadding}>
                    <Text style={styles.sectionTitle}>Mengapa Culinaire?</Text>

                    <View style={styles.featureRow}>
                        <View style={styles.featureCard}>
                            <MaterialIcon name="chef-hat" size={32} color="#8B1538" />
                            <Text style={styles.featureTitle}>Bahan Premium</Text>
                            <Text style={styles.featureSubtitle}>Kualitas bintang 5</Text>
                        </View>
                        <View style={styles.featureCard}>
                            <MaterialIcon name="account-tie" size={32} color="#8B1538" />
                            <Text style={styles.featureTitle}>Chef Ahli</Text>
                            <Text style={styles.featureSubtitle}>25+ Tahun</Text>
                        </View>
                    </View>

                    <View style={styles.experienceCard}>
                        <Icon name="star" size={24} color="#D4AF37" />
                        <Text style={styles.experienceTitle}>Pengalaman Kuliner Terbaik</Text>
                        <Text style={styles.experienceSubtitle}>Chef berpengalaman & bahan premium untuk kepuasan Anda</Text>
                    </View>

                    <TouchableOpacity style={styles.reserveBtn} onPress={() => navigation.navigate('Reservations')}>
                        <Text style={styles.reserveBtnText}>Reservasi Sekarang</Text>
                    </TouchableOpacity>
                </View>

                <View style={{ height: 100 }} />
            </ScrollView>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#FFFFFF',
    },
    // Header
    header: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingHorizontal: 20,
        paddingTop: 50,
        paddingBottom: 10,
    },
    logoText: {
        fontSize: 24,
        fontWeight: 'bold',
        color: '#8B1538',
    },
    subLogoText: {
        fontSize: 12,
        color: '#888',
        marginTop: 2,
    },
    // Search
    searchContainer: {
        paddingHorizontal: 20,
        marginBottom: 16,
    },
    searchBar: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: '#F5F5F5',
        paddingHorizontal: 16,
        paddingVertical: 12,
        borderRadius: 25,
    },
    searchPlaceholder: {
        marginLeft: 10,
        fontSize: 14,
        color: '#999',
    },
    // Section
    sectionPadding: {
        paddingHorizontal: 20,
    },
    sectionTitle: {
        fontSize: 18,
        fontWeight: 'bold',
        color: '#333',
        marginBottom: 4,
    },
    sectionSubtitle: {
        fontSize: 12,
        color: '#888',
    },
    sectionHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: 12,
    },
    seeAllText: {
        fontSize: 12,
        color: '#8B1538',
    },
    // Member Card
    memberCard: {
        borderRadius: 16,
        padding: 16,
        marginBottom: 16,
    },
    platinumBadge: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: 'rgba(212,175,55,0.2)',
        alignSelf: 'flex-start',
        paddingHorizontal: 10,
        paddingVertical: 4,
        borderRadius: 20,
        marginBottom: 8,
    },
    platinumText: {
        color: '#D4AF37',
        fontSize: 10,
        fontWeight: 'bold',
        marginLeft: 4,
    },
    memberRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'flex-end',
    },
    memberName: {
        color: '#FFF',
        fontSize: 14,
        marginBottom: 4,
    },
    memberPoints: {
        color: '#FFF',
        fontSize: 36,
        fontWeight: 'bold',
    },
    memberPointsLabel: {
        color: 'rgba(255,255,255,0.7)',
        fontSize: 12,
    },
    redeemBtn: {
        backgroundColor: '#D4AF37',
        paddingHorizontal: 16,
        paddingVertical: 10,
        borderRadius: 8,
    },
    redeemBtnText: {
        color: '#000',
        fontSize: 12,
        fontWeight: 'bold',
    },
    // Promo
    promoContainer: {
        paddingLeft: 20,
        paddingRight: 10,
        marginBottom: 20,
    },
    promoCard: {
        width: width * 0.75,
        height: 140,
        borderRadius: 16,
        marginRight: 12,
        flexDirection: 'row',
        overflow: 'hidden',
    },
    promoContent: {
        flex: 1,
        padding: 16,
        justifyContent: 'center',
    },
    promoImage: {
        width: 120,
        height: '100%',
        borderTopRightRadius: 16,
        borderBottomRightRadius: 16,
    },
    promoTitle: {
        color: '#FFF',
        fontSize: 18,
        fontWeight: 'bold',
        marginBottom: 4,
    },
    promoSubtitle: {
        color: 'rgba(255,255,255,0.8)',
        fontSize: 11,
        marginBottom: 12,
    },
    promoBtn: {
        backgroundColor: '#FFF',
        alignSelf: 'flex-start',
        paddingHorizontal: 14,
        paddingVertical: 8,
        borderRadius: 20,
    },
    promoBtnText: {
        color: '#333',
        fontSize: 11,
        fontWeight: '600',
    },
    // Quick Menu
    quickGrid: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        marginTop: 12,
        marginBottom: 20,
    },
    quickItem: {
        alignItems: 'center',
        width: (width - 60) / 4,
    },
    quickIcon: {
        width: 52,
        height: 52,
        borderRadius: 14,
        justifyContent: 'center',
        alignItems: 'center',
        marginBottom: 8,
    },
    quickLabel: {
        fontSize: 11,
        color: '#333',
        textAlign: 'center',
    },
    // Stats
    statsContainer: {
        flexDirection: 'row',
        paddingHorizontal: 20,
        marginBottom: 20,
    },
    statCard: {
        flex: 1,
        marginHorizontal: 4,
        padding: 14,
        borderRadius: 12,
        alignItems: 'center',
    },
    statLabel: {
        fontSize: 11,
        color: '#666',
        marginTop: 4,
    },
    statValue: {
        fontSize: 18,
        fontWeight: 'bold',
        color: '#333',
        marginTop: 2,
    },
    // Flash Sale
    flashSaleCard: {
        borderRadius: 16,
        padding: 16,
        flexDirection: 'row',
        alignItems: 'center',
        marginBottom: 20,
    },
    flashSaleContent: {
        flex: 1,
    },
    flashSaleBadge: {
        backgroundColor: '#C62828',
        alignSelf: 'flex-start',
        paddingHorizontal: 8,
        paddingVertical: 4,
        borderRadius: 4,
        marginBottom: 8,
    },
    flashSaleBadgeText: {
        color: '#FFF',
        fontSize: 10,
        fontWeight: 'bold',
    },
    flashSaleTitle: {
        fontSize: 18,
        fontWeight: 'bold',
        color: '#333',
        marginBottom: 4,
    },
    flashSaleSubtitle: {
        fontSize: 12,
        color: '#555',
        marginBottom: 12,
    },
    flashSaleBtn: {
        backgroundColor: '#8B1538',
        flexDirection: 'row',
        alignItems: 'center',
        alignSelf: 'flex-start',
        paddingHorizontal: 14,
        paddingVertical: 8,
        borderRadius: 20,
    },
    flashSaleBtnText: {
        color: '#FFF',
        fontSize: 12,
        fontWeight: '600',
        marginRight: 4,
    },
    fireEmoji: {
        fontSize: 48,
    },
    // Menu Cards
    menuScrollContainer: {
        paddingLeft: 20,
        paddingRight: 10,
    },
    menuCard: {
        width: 160,
        backgroundColor: '#FFF',
        borderRadius: 12,
        marginRight: 12,
        elevation: 3,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 2 },
        shadowOpacity: 0.1,
        shadowRadius: 4,
        marginBottom: 10,
    },
    menuImageContainer: {
        position: 'relative',
    },
    menuImage: {
        width: '100%',
        height: 100,
        borderTopLeftRadius: 12,
        borderTopRightRadius: 12,
        resizeMode: 'cover',
    },
    bestSellerBadge: {
        position: 'absolute',
        top: 8,
        left: 8,
        backgroundColor: '#8B1538',
        paddingHorizontal: 8,
        paddingVertical: 4,
        borderRadius: 4,
    },
    bestSellerText: {
        color: '#FFF',
        fontSize: 9,
        fontWeight: 'bold',
    },
    menuInfo: {
        padding: 10,
    },
    menuName: {
        fontSize: 13,
        fontWeight: 'bold',
        color: '#333',
        marginBottom: 6,
    },
    menuRatingRow: {
        flexDirection: 'row',
        alignItems: 'center',
        marginBottom: 8,
    },
    menuRating: {
        fontSize: 11,
        color: '#333',
        fontWeight: '600',
        marginLeft: 4,
    },
    menuOrders: {
        fontSize: 10,
        color: '#888',
        marginLeft: 6,
    },
    menuPriceRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
    },
    menuPrice: {
        fontSize: 14,
        fontWeight: 'bold',
        color: '#8B1538',
    },
    addBtn: {
        backgroundColor: '#00BCD4',
        width: 26,
        height: 26,
        borderRadius: 8,
        justifyContent: 'center',
        alignItems: 'center',
    },
    // Dots
    dotsContainer: {
        flexDirection: 'row',
        justifyContent: 'center',
        marginVertical: 16,
    },
    dot: {
        width: 8,
        height: 8,
        borderRadius: 4,
        backgroundColor: '#DDD',
        marginHorizontal: 4,
    },
    dotActive: {
        backgroundColor: '#8B1538',
    },
    // Features
    featureRow: {
        flexDirection: 'row',
        marginTop: 16,
        marginBottom: 16,
    },
    featureCard: {
        flex: 1,
        backgroundColor: '#F9F9F9',
        borderRadius: 12,
        padding: 16,
        alignItems: 'center',
        marginHorizontal: 4,
    },
    featureTitle: {
        fontSize: 14,
        fontWeight: 'bold',
        color: '#333',
        marginTop: 8,
    },
    featureSubtitle: {
        fontSize: 11,
        color: '#888',
        marginTop: 2,
    },
    experienceCard: {
        backgroundColor: '#F9F9F9',
        borderRadius: 12,
        padding: 16,
        alignItems: 'center',
        marginBottom: 16,
    },
    experienceTitle: {
        fontSize: 14,
        fontWeight: 'bold',
        color: '#333',
        marginTop: 8,
    },
    experienceSubtitle: {
        fontSize: 11,
        color: '#888',
        marginTop: 4,
        textAlign: 'center',
    },
    reserveBtn: {
        backgroundColor: '#D4AF37',
        paddingVertical: 14,
        borderRadius: 25,
        alignItems: 'center',
    },
    reserveBtnText: {
        color: '#000',
        fontSize: 14,
        fontWeight: 'bold',
    },
});

export default HomeScreen;
