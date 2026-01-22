import React from 'react';
import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    TouchableOpacity,
    StatusBar,
    Dimensions
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { colors, spacing, borderRadius, fontSize } from '../../theme/colors';
import LinearGradient from 'react-native-linear-gradient';
const { width } = Dimensions.get('window');
const AdminDashboardScreen = ({ navigation }) => {
    const adminFeatures = [
        {
            id: 'menus',
            title: 'Manage Menu',
            subtitle: 'Add, Edit, Delete items',
            icon: 'restaurant',
            route: 'AdminMenus',
            color: '#FFD700'
        },
        {
            id: 'orders',
            title: 'Manage Orders',
            subtitle: 'View & Update Status',
            icon: 'receipt',
            route: 'AdminOrders',
            color: '#4CD964'
        },
        {
            id: 'reservations',
            title: 'Reservations',
            subtitle: 'Manage Bookings',
            icon: 'calendar',
            route: 'AdminReservations',
            color: '#5856D6'
        },
        {
            id: 'users',
            title: 'User Management',
            subtitle: 'View & Ban Users',
            icon: 'people',
            route: 'AdminUsers',
            color: '#FF3B30'
        },
        {
            id: 'reports',
            title: 'Reports',
            subtitle: 'Sales & Activity',
            icon: 'bar-chart',
            route: 'AdminReports',
            color: '#FF9500'
        },
        {
            id: 'activities',
            title: 'Activity Log',
            subtitle: 'Monitor System',
            icon: 'pulse',
            route: 'AdminActivities',
            color: '#007AFF'
        }
    ];
    const renderFeatureCard = (item) => (
        <TouchableOpacity
            key={item.id}
            style={styles.card}
            onPress={() => navigation.navigate(item.route)}
            activeOpacity={0.8}
        >
            <View style={[styles.iconContainer, { backgroundColor: `${item.color}20` }]}>
                <Icon name={item.icon} size={28} color={item.color} />
            </View>
            <View style={styles.cardContent}>
                <Text style={styles.cardTitle}>{item.title}</Text>
                <Text style={styles.cardSubtitle}>{item.subtitle}</Text>
            </View>
            <Icon name="chevron-forward" size={20} color={colors.textSecondary} />
        </TouchableOpacity>
    );
    return (
        <View style={styles.container}>
            <StatusBar barStyle="light-content" backgroundColor="#000" />
            {}
            <View style={styles.header}>
                <View>
                    <Text style={styles.headerTitle}>Admin Access</Text>
                    <Text style={styles.headerSubtitle}>Control Panel</Text>
                </View>
                <View style={styles.adminBadge}>
                    <Icon name="shield-checkmark" size={16} color="#000" />
                    <Text style={styles.adminBadgeText}>SUPER ADMIN</Text>
                </View>
            </View>
            <ScrollView contentContainerStyle={styles.scrollContent}>
                {}
                <View style={styles.statsContainer}>
                    <LinearGradient
                        colors={['#D4AF37', '#B8860B']}
                        start={{ x: 0, y: 0 }}
                        end={{ x: 1, y: 1 }}
                        style={styles.statCard}
                    >
                        <Text style={styles.statLabel}>Today's Orders</Text>
                        <Text style={styles.statValue}>24</Text>
                        <Icon name="trending-up" size={20} color="#FFF" style={styles.statIcon} />
                    </LinearGradient>
                    <LinearGradient
                        colors={['#333', '#111']}
                        start={{ x: 0, y: 0 }}
                        end={{ x: 1, y: 1 }}
                        style={styles.statCard}
                    >
                        <Text style={[styles.statLabel, { color: '#D4AF37' }]}>Pending</Text>
                        <Text style={[styles.statValue, { color: '#D4AF37' }]}>8</Text>
                        <Icon name="time" size={20} color="#D4AF37" style={styles.statIcon} />
                    </LinearGradient>
                </View>
                {}
                <Text style={styles.sectionTitle}>Management Tools</Text>
                <View style={styles.grid}>
                    {adminFeatures.map(renderFeatureCard)}
                </View>
            </ScrollView>
        </View>
    );
};
const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#0F0F0F', 
    },
    header: {
        paddingTop: 60,
        paddingHorizontal: spacing.lg,
        paddingBottom: spacing.lg,
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        borderBottomWidth: 1,
        borderBottomColor: '#333',
    },
    headerTitle: {
        fontSize: 28,
        fontFamily: 'PlayfairDisplay-Bold', 
        fontWeight: 'bold',
        color: '#D4AF37',
        letterSpacing: 0.5,
    },
    headerSubtitle: {
        fontSize: fontSize.sm,
        color: colors.textSecondary,
        marginTop: 2,
        letterSpacing: 2,
        textTransform: 'uppercase',
    },
    adminBadge: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: '#D4AF37',
        paddingHorizontal: 12,
        paddingVertical: 6,
        borderRadius: 20,
        gap: 6,
    },
    adminBadgeText: {
        fontSize: 10,
        fontWeight: '900',
        color: '#000',
        letterSpacing: 1,
    },
    scrollContent: {
        padding: spacing.lg,
    },
    statsContainer: {
        flexDirection: 'row',
        gap: spacing.md,
        marginBottom: spacing.xl,
    },
    statCard: {
        flex: 1,
        borderRadius: borderRadius.lg,
        padding: spacing.md,
        height: 100,
        justifyContent: 'center',
        shadowColor: "#000",
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.3,
        shadowRadius: 4.65,
        elevation: 8,
    },
    statLabel: {
        fontSize: fontSize.xs,
        color: 'rgba(255,255,255,0.8)',
        fontWeight: '600',
        textTransform: 'uppercase',
    },
    statValue: {
        fontSize: 32,
        fontWeight: 'bold',
        color: '#FFF',
        marginTop: 4,
    },
    statIcon: {
        position: 'absolute',
        top: 15,
        right: 15,
        opacity: 0.5,
    },
    sectionTitle: {
        fontSize: fontSize.lg,
        fontWeight: '600',
        color: colors.text,
        marginBottom: spacing.md,
    },
    grid: {
        gap: spacing.md,
    },
    card: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: '#1A1A1A',
        padding: spacing.md,
        borderRadius: borderRadius.lg,
        borderWidth: 1,
        borderColor: '#333',
    },
    iconContainer: {
        width: 50,
        height: 50,
        borderRadius: 15,
        justifyContent: 'center',
        alignItems: 'center',
        marginRight: spacing.md,
    },
    cardContent: {
        flex: 1,
    },
    cardTitle: {
        fontSize: fontSize.md,
        fontWeight: '600',
        color: colors.text,
        marginBottom: 2,
    },
    cardSubtitle: {
        fontSize: fontSize.xs,
        color: colors.textSecondary,
    },
});
export default AdminDashboardScreen;