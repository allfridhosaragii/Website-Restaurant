import React, { useState, useCallback } from 'react';
import {
    View,
    Text,
    StyleSheet,
    FlatList,
    TouchableOpacity,
    Alert,
    RefreshControl,
    ScrollView
} from 'react-native';
import { useFocusEffect } from '@react-navigation/native';
import Icon from 'react-native-vector-icons/Ionicons';
import { colors, spacing, borderRadius, fontSize } from '../../theme/colors';
import { adminAPI } from '../../api/client';
const statusColors = {
    pending: '#FF9500',
    processing: '#007AFF',
    completed: '#4CD964',
    cancelled: '#FF3B30',
};
const AdminOrderScreen = ({ navigation }) => {
    const [orders, setOrders] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [activeFilter, setActiveFilter] = useState('all');
    useFocusEffect(
        useCallback(() => {
            loadOrders();
        }, [])
    );
    const loadOrders = async () => {
        try {
            const response = await adminAPI.getOrders();
            const data = response.data.data || response.data;
            setOrders(Array.isArray(data) ? data : []);
        } catch (error) {
            console.error('Load orders error:', error);
            Alert.alert('Error', 'Gagal memuat data pesanan');
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };
    const handleStatusUpdate = (orderId, newStatus) => {
        Alert.alert(
            'Update Status',
            `Ubah status pesanan menjadi ${newStatus}?`,
            [
                { text: 'Batal', style: 'cancel' },
                {
                    text: 'Ya',
                    onPress: async () => {
                        try {
                            await adminAPI.updateOrderStatus(orderId, newStatus);
                            loadOrders();
                            Alert.alert('Sukses', 'Status pesanan diperbarui');
                        } catch (error) {
                            Alert.alert('Error', 'Gagal memperbarui status');
                        }
                    }
                }
            ]
        );
    };
    const getFilteredOrders = () => {
        if (activeFilter === 'all') return orders;
        return orders.filter(o => o.status === activeFilter);
    };
    const renderFilterChip = (label, status) => (
        <TouchableOpacity
            style={[
                styles.filterChip,
                activeFilter === status && styles.activeFilterChip
            ]}
            onPress={() => setActiveFilter(status)}
        >
            <Text style={[
                styles.filterText,
                activeFilter === status && styles.activeFilterText
            ]}>{label}</Text>
        </TouchableOpacity>
    );
    const renderItem = ({ item }) => (
        <View style={styles.card}>
            <View style={styles.cardHeader}>
                <View>
                    <Text style={styles.orderId}>Order #{item.id.toString().slice(-6)}</Text>
                    <Text style={styles.date}>{new Date(item.created_at).toLocaleString()}</Text>
                </View>
                <View style={[styles.statusBadge, { backgroundColor: `${statusColors[item.status] || '#999'}20` }]}>
                    <Text style={[styles.statusText, { color: statusColors[item.status] || '#999' }]}>
                        {item.status.toUpperCase()}
                    </Text>
                </View>
            </View>
            <View style={styles.divider} />
            <View style={styles.detailsRow}>
                <View style={styles.detailItem}>
                    <Icon name="person-outline" size={16} color={colors.textSecondary} />
                    <Text style={styles.detailText}>{item.user_name || 'Guest'}</Text>
                </View>
                <View style={styles.detailItem}>
                    <Icon name="cash-outline" size={16} color={colors.textSecondary} />
                    <Text style={styles.totalPrice}>Rp {parseInt(item.total_amount).toLocaleString('id-ID')}</Text>
                </View>
            </View>
            {item.status !== 'completed' && item.status !== 'cancelled' && (
                <View style={styles.actionRow}>
                    {item.status === 'pending' && (
                        <TouchableOpacity
                            style={[styles.actionBtn, { backgroundColor: '#007AFF20', borderColor: '#007AFF' }]}
                            onPress={() => handleStatusUpdate(item.id, 'processing')}
                        >
                            <Text style={[styles.actionText, { color: '#007AFF' }]}>Process</Text>
                        </TouchableOpacity>
                    )}
                    {item.status === 'processing' && (
                        <TouchableOpacity
                            style={[styles.actionBtn, { backgroundColor: '#4CD96420', borderColor: '#4CD964' }]}
                            onPress={() => handleStatusUpdate(item.id, 'completed')}
                        >
                            <Text style={[styles.actionText, { color: '#4CD964' }]}>Complete</Text>
                        </TouchableOpacity>
                    )}
                    <TouchableOpacity
                        style={[styles.actionBtn, { backgroundColor: '#FF3B3020', borderColor: '#FF3B30' }]}
                        onPress={() => handleStatusUpdate(item.id, 'cancelled')}
                    >
                        <Text style={[styles.actionText, { color: '#FF3B30' }]}>Cancel</Text>
                    </TouchableOpacity>
                </View>
            )}
        </View>
    );
    return (
        <View style={styles.container}>
            {}
            <View style={styles.header}>
                <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
                    <Icon name="arrow-back" size={24} color="#FFF" />
                </TouchableOpacity>
                <Text style={styles.headerTitle}>Manage Orders</Text>
                <View style={{ width: 24 }} />
            </View>
            {}
            <View style={styles.filterContainer}>
                <ScrollView horizontal showsHorizontalScrollIndicator={false}>
                    {renderFilterChip('All', 'all')}
                    {renderFilterChip('Pending', 'pending')}
                    {renderFilterChip('Processing', 'processing')}
                    {renderFilterChip('Completed', 'completed')}
                    {renderFilterChip('Cancelled', 'cancelled')}
                </ScrollView>
            </View>
            <FlatList
                data={getFilteredOrders()}
                keyExtractor={(item) => item.id.toString()}
                renderItem={renderItem}
                contentContainerStyle={styles.list}
                refreshControl={
                    <RefreshControl
                        refreshing={refreshing}
                        onRefresh={() => { setRefreshing(true); loadOrders(); }}
                        tintColor="#D4AF37"
                    />
                }
                ListEmptyComponent={
                    !loading && (
                        <View style={styles.emptyContainer}>
                            <Icon name="receipt-outline" size={64} color="#333" />
                            <Text style={styles.emptyText}>No orders found</Text>
                        </View>
                    )
                }
            />
        </View>
    );
};
const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#0F0F0F',
    },
    header: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        paddingTop: 50,
        paddingHorizontal: spacing.lg,
        paddingBottom: spacing.md,
        backgroundColor: '#0F0F0F',
        borderBottomWidth: 1,
        borderBottomColor: '#333',
    },
    headerTitle: {
        fontSize: 20,
        fontWeight: 'bold',
        color: '#FFF',
        letterSpacing: 1,
    },
    filterContainer: {
        paddingVertical: spacing.md,
        paddingHorizontal: spacing.sm,
        borderBottomWidth: 1,
        borderBottomColor: '#222',
        backgroundColor: '#111',
    },
    filterChip: {
        paddingHorizontal: 16,
        paddingVertical: 8,
        borderRadius: 20,
        backgroundColor: '#222',
        marginHorizontal: 6,
        borderWidth: 1,
        borderColor: '#444',
    },
    activeFilterChip: {
        backgroundColor: '#D4AF37',
        borderColor: '#D4AF37',
    },
    filterText: {
        color: '#888',
        fontWeight: '600',
        fontSize: 12,
    },
    activeFilterText: {
        color: '#000',
    },
    list: {
        padding: spacing.md,
        paddingBottom: 40,
    },
    card: {
        backgroundColor: '#1A1A1A',
        borderRadius: borderRadius.lg,
        padding: spacing.md,
        marginBottom: spacing.md,
        borderWidth: 1,
        borderColor: '#333',
    },
    cardHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'flex-start',
    },
    orderId: {
        fontSize: fontSize.md,
        fontWeight: 'bold',
        color: '#FFF',
    },
    date: {
        fontSize: fontSize.xs,
        color: colors.textSecondary,
        marginTop: 2,
    },
    statusBadge: {
        paddingHorizontal: 8,
        paddingVertical: 4,
        borderRadius: 4,
    },
    statusText: {
        fontSize: 10,
        fontWeight: 'bold',
    },
    divider: {
        height: 1,
        backgroundColor: '#333',
        marginVertical: spacing.md,
    },
    detailsRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        marginBottom: spacing.md,
    },
    detailItem: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: 6,
    },
    detailText: {
        fontSize: fontSize.sm,
        color: '#CCC',
    },
    totalPrice: {
        fontSize: fontSize.md,
        fontWeight: 'bold',
        color: '#D4AF37',
    },
    actionRow: {
        flexDirection: 'row',
        justifyContent: 'flex-end',
        gap: spacing.sm,
        marginTop: spacing.xs,
    },
    actionBtn: {
        paddingVertical: 6,
        paddingHorizontal: 12,
        borderRadius: 6,
        borderWidth: 1,
    },
    actionText: {
        fontSize: 12,
        fontWeight: 'bold',
    },
    emptyContainer: {
        alignItems: 'center',
        marginTop: 60,
    },
    emptyText: {
        color: '#666',
        marginTop: 16,
        fontSize: 16,
    },
});
export default AdminOrderScreen;