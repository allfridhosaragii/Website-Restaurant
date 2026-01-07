import React, { useState, useEffect } from 'react';
import {
    View,
    Text,
    ScrollView,
    StyleSheet,
    TouchableOpacity,
    Image,
    RefreshControl,
    ActivityIndicator,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { colors, spacing, fontSize, borderRadius } from '../../theme/colors';
import { menuAPI, dashboardAPI } from '../../api/client';
import { useAuth } from '../../context/AuthContext';
import { MOCK_MENUS } from '../../data/mockData';
import MenuCard from '../../components/MenuCard';

const HomeScreen = ({ navigation }) => {
    const { user } = useAuth();
    const [featuredMenus, setFeaturedMenus] = useState([]);
    const [stats, setStats] = useState(null);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        try {
            const [menuRes, statsRes] = await Promise.all([
                menuAPI.getAll({ limit: 6 }),
                dashboardAPI.getStats(),
            ]);

            if (menuRes.data && menuRes.data.menus && menuRes.data.menus.length > 0) {
                setFeaturedMenus(menuRes.data.menus.slice(0, 6));
            } else {
                throw new Error("Empty menus");
            }

            if (statsRes.data) {
                setStats(statsRes.data);
            } else {
                throw new Error("Empty stats");
            }

        } catch (error) {
            console.log('API Error, using Mock Data:', error);
            // Fallback to Mock Data
            setFeaturedMenus(MOCK_MENUS.slice(0, 6));
            setStats({
                total_orders: 12,
                total_reservations: 3,
                points: 450
            });
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };

    const onRefresh = () => {
        setRefreshing(true);
        loadData();
    };

    if (loading) {
        return (
            <View style={styles.loader}>
                <ActivityIndicator size="large" color={colors.accent} />
            </View>
        );
    }

    return (
        <ScrollView
            style={styles.container}
            refreshControl={
                <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
            }
        >
            {/* Header */}
            <View style={styles.header}>
                <View>
                    <Text style={styles.greeting}>Selamat datang,</Text>
                    <Text style={styles.userName}>{user?.name || 'Guest'}</Text>
                </View>
                <TouchableOpacity onPress={() => navigation.navigate('Profile')}>
                    <View style={styles.avatar}>
                        <Icon name="person" size={24} color={colors.accent} />
                    </View>
                </TouchableOpacity>
            </View>

            {/* Stats */}
            {stats && (
                <View style={styles.statsContainer}>
                    <View style={styles.statCard}>
                        <Icon name="receipt-outline" size={24} color={colors.accent} />
                        <Text style={styles.statNumber}>{stats.total_orders || 0}</Text>
                        <Text style={styles.statLabel}>Pesanan</Text>
                    </View>
                    <View style={styles.statCard}>
                        <Icon name="calendar-outline" size={24} color={colors.accent} />
                        <Text style={styles.statNumber}>{stats.total_reservations || 0}</Text>
                        <Text style={styles.statLabel}>Reservasi</Text>
                    </View>
                    <View style={styles.statCard}>
                        <Icon name="star-outline" size={24} color={colors.accent} />
                        <Text style={styles.statNumber}>{stats.points || 0}</Text>
                        <Text style={styles.statLabel}>Poin</Text>
                    </View>
                </View>
            )}

            {/* Quick Actions */}
            <View style={styles.section}>
                <Text style={styles.sectionTitle}>Menu Cepat</Text>
                <View style={styles.quickActions}>
                    <TouchableOpacity
                        style={styles.actionCard}
                        onPress={() => navigation.navigate('Menu')}
                    >
                        <Icon name="restaurant" size={28} color={colors.accent} />
                        <Text style={styles.actionText}>Pesan Menu</Text>
                    </TouchableOpacity>
                    <TouchableOpacity
                        style={styles.actionCard}
                        onPress={() => navigation.navigate('CreateReservation')}
                    >
                        <Icon name="calendar" size={28} color={colors.accent} />
                        <Text style={styles.actionText}>Reservasi</Text>
                    </TouchableOpacity>
                    <TouchableOpacity
                        style={styles.actionCard}
                        onPress={() => navigation.navigate('Orders')}
                    >
                        <Icon name="receipt" size={28} color={colors.accent} />
                        <Text style={styles.actionText}>Pesanan</Text>
                    </TouchableOpacity>
                </View>
            </View>

            {/* Featured Menus */}
            <View style={styles.section}>
                <View style={styles.sectionHeader}>
                    <Text style={styles.sectionTitle}>Menu Populer</Text>
                    <TouchableOpacity onPress={() => navigation.navigate('Menu')}>
                        <Text style={styles.seeAll}>Lihat Semua</Text>
                    </TouchableOpacity>
                </View>
                <ScrollView horizontal showsHorizontalScrollIndicator={false}>
                    {featuredMenus.map((menu) => (
                        <MenuCard
                            key={menu.id}
                            menu={menu}
                            onPress={() => navigation.navigate('MenuDetail', { slug: menu.slug })}
                            horizontal
                        />
                    ))}
                </ScrollView>
            </View>
        </ScrollView>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.background,
    },
    loader: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        backgroundColor: colors.background,
    },
    header: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        padding: spacing.lg,
        paddingTop: spacing.xxl,
    },
    greeting: {
        fontSize: fontSize.md,
        color: colors.textSecondary,
    },
    userName: {
        fontSize: fontSize.xl,
        fontWeight: 'bold',
        color: colors.text,
    },
    avatar: {
        width: 48,
        height: 48,
        borderRadius: 24,
        backgroundColor: colors.surface,
        justifyContent: 'center',
        alignItems: 'center',
        borderWidth: 2,
        borderColor: colors.accent,
    },
    statsContainer: {
        flexDirection: 'row',
        paddingHorizontal: spacing.lg,
        gap: spacing.md,
    },
    statCard: {
        flex: 1,
        backgroundColor: colors.surface,
        padding: spacing.md,
        borderRadius: borderRadius.lg,
        alignItems: 'center',
    },
    statNumber: {
        fontSize: fontSize.xl,
        fontWeight: 'bold',
        color: colors.text,
        marginTop: spacing.xs,
    },
    statLabel: {
        fontSize: fontSize.sm,
        color: colors.textSecondary,
    },
    section: {
        padding: spacing.lg,
    },
    sectionHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: spacing.md,
    },
    sectionTitle: {
        fontSize: fontSize.lg,
        fontWeight: '600',
        color: colors.text,
        marginBottom: spacing.md,
    },
    seeAll: {
        fontSize: fontSize.md,
        color: colors.accent,
    },
    quickActions: {
        flexDirection: 'row',
        gap: spacing.md,
    },
    actionCard: {
        flex: 1,
        backgroundColor: colors.surface,
        padding: spacing.lg,
        borderRadius: borderRadius.lg,
        alignItems: 'center',
        gap: spacing.sm,
    },
    actionText: {
        fontSize: fontSize.sm,
        color: colors.text,
        textAlign: 'center',
    },
});

export default HomeScreen;
