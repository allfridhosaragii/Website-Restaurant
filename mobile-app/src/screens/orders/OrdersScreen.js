import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, FlatList, TouchableOpacity, StyleSheet, StatusBar, ActivityIndicator, RefreshControl } from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { colors } from '../../theme/colors';

const BASE_URL = 'https://website-restaurant.up.railway.app/api';

const OrdersScreen = ({ navigation }) => {
    const [activeTab, setActiveTab] = useState('menu');
    const [orders, setOrders] = useState([]);
    const [reservations, setReservations] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);

    useEffect(() => {
        fetchData();
    }, []);

    const fetchData = async () => {
        try {
            const token = await AsyncStorage.getItem('auth_token');
            if (!token) {
                setLoading(false);
                return;
            }

            const config = {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            };

            const [ordersRes, reservationsRes] = await Promise.all([
                axios.get(`${BASE_URL}/orders`, config).catch(() => ({ data: [] })),
                axios.get(`${BASE_URL}/reservations`, config).catch(() => ({ data: [] }))
            ]);

            const ordersData = ordersRes.data?.data || ordersRes.data || [];
            const reservationsData = reservationsRes.data?.data || reservationsRes.data || [];

            setOrders(Array.isArray(ordersData) ? ordersData : []);
            setReservations(Array.isArray(reservationsData) ? reservationsData : []);
        } catch (error) {
            console.log('Fetch history error:', error);
        } finally {
            setLoading(false);
        }
    };

    const onRefresh = useCallback(() => {
        setRefreshing(true);
        fetchData().finally(() => setRefreshing(false));
    }, []);

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    };

    const formatPrice = (price) => {
        return `Rp ${parseInt(price || 0).toLocaleString('id-ID')}`;
    };

    const getStatusColor = (status) => {
        switch (status?.toLowerCase()) {
            case 'completed':
            case 'accepted':
            case 'selesai':
                return { bg: '#E8F5E9', text: '#2E7D32' };
            case 'pending':
            case 'menunggu':
                return { bg: '#FFF8E1', text: '#F57F17' };
            case 'cancelled':
            case 'rejected':
            case 'dibatalkan':
                return { bg: '#FFEBEE', text: '#C62828' };
            default:
                return { bg: '#E3F2FD', text: '#1565C0' };
        }
    };

    const renderMenuOrder = ({ item }) => {
        const statusColor = getStatusColor(item.status);
        return (
            <TouchableOpacity
                style={styles.card}
                activeOpacity={0.8}
                onPress={() => navigation.navigate('OrderDetail', { orderId: item.id })}
            >
                <View style={styles.cardHeader}>
                    <View>
                        <Text style={styles.orderNo}>#{item.order_number || item.id}</Text>
                        <Text style={styles.date}>{formatDate(item.created_at)}</Text>
                    </View>
                    <View style={[styles.statusBadge, { backgroundColor: statusColor.bg }]}>
                        <Text style={[styles.statusText, { color: statusColor.text }]}>
                            {item.status || 'pending'}
                        </Text>
                    </View>
                </View>
                <View style={styles.divider} />
                <View style={styles.cardFooter}>
                    <Text style={styles.totalLabel}>Total: <Text style={styles.totalValue}>{formatPrice(item.total)}</Text></Text>
                    <Text style={styles.itemCount}>{item.items?.length || item.items_count || 0} Items</Text>
                </View>
            </TouchableOpacity>
        );
    };

    const renderReservation = ({ item }) => {
        const statusColor = getStatusColor(item.status);
        return (
            <TouchableOpacity style={styles.card} activeOpacity={0.8}>
                <View style={styles.cardHeader}>
                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                        <Icon name="calendar-outline" size={20} color={colors.primary} style={{ marginRight: 8 }} />
                        <View>
                            <Text style={styles.orderNo}>{item.reservation_date || item.date}</Text>
                            <Text style={styles.date}>{item.reservation_time || item.time} • {item.guests || item.guest_count} Tamu</Text>
                        </View>
                    </View>
                    <View style={[styles.statusBadge, { backgroundColor: statusColor.bg }]}>
                        <Text style={[styles.statusText, { color: statusColor.text }]}>
                            {item.status}
                        </Text>
                    </View>
                </View>
                {item.table_number && (
                    <>
                        <View style={styles.divider} />
                        <Text style={styles.tableInfo}>Meja: {item.table_number}</Text>
                    </>
                )}
            </TouchableOpacity>
        );
    };

    const renderEmpty = () => (
        <View style={styles.emptyContainer}>
            <Icon name={activeTab === 'menu' ? 'receipt-outline' : 'calendar-outline'} size={60} color="#CCC" />
            <Text style={styles.emptyText}>
                {activeTab === 'menu' ? 'Belum ada pesanan' : 'Belum ada reservasi'}
            </Text>
        </View>
    );

    return (
        <View style={styles.container}>
            <StatusBar barStyle="dark-content" backgroundColor="#F9F9F9" />
            <View style={styles.header}>
                <Text style={styles.headerTitle}>Histori</Text>
                <View style={styles.tabsContainer}>
                    <TouchableOpacity
                        style={[styles.tab, activeTab === 'menu' && styles.activeTab]}
                        onPress={() => setActiveTab('menu')}
                    >
                        <Text style={[styles.tabText, activeTab === 'menu' && styles.activeTabText]}>Pesanan Menu</Text>
                    </TouchableOpacity>
                    <TouchableOpacity
                        style={[styles.tab, activeTab === 'reservations' && styles.activeTab]}
                        onPress={() => setActiveTab('reservations')}
                    >
                        <Text style={[styles.tabText, activeTab === 'reservations' && styles.activeTabText]}>Reservasi</Text>
                    </TouchableOpacity>
                </View>
            </View>

            {loading ? (
                <View style={styles.loadingContainer}>
                    <ActivityIndicator size="large" color={colors.primary} />
                </View>
            ) : (
                <FlatList
                    data={activeTab === 'menu' ? orders : reservations}
                    keyExtractor={item => item.id?.toString() || Math.random().toString()}
                    renderItem={activeTab === 'menu' ? renderMenuOrder : renderReservation}
                    contentContainerStyle={styles.list}
                    showsVerticalScrollIndicator={false}
                    ListEmptyComponent={renderEmpty}
                    refreshControl={
                        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[colors.primary]} />
                    }
                />
            )}
        </View>
    );
};

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#F9F9F9' },
    header: { padding: 20, paddingTop: 40, backgroundColor: '#FFF' },
    headerTitle: { fontSize: 24, fontWeight: 'bold', color: colors.primary, fontFamily: 'serif', marginBottom: 20 },
    tabsContainer: { flexDirection: 'row', backgroundColor: '#F5F5F5', borderRadius: 12, padding: 4 },
    tab: { flex: 1, paddingVertical: 10, alignItems: 'center', borderRadius: 8 },
    activeTab: { backgroundColor: '#FFF', elevation: 2 },
    tabText: { color: '#757575', fontWeight: '500' },
    activeTabText: { color: colors.primary, fontWeight: 'bold' },
    list: { padding: 20, flexGrow: 1 },
    card: {
        backgroundColor: '#FFF',
        borderRadius: 12,
        padding: 16,
        marginBottom: 12,
        elevation: 2,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 1 },
        shadowOpacity: 0.1,
        shadowRadius: 4,
    },
    cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
    orderNo: { fontSize: 16, fontWeight: 'bold', color: '#333' },
    date: { fontSize: 12, color: '#888', marginTop: 2 },
    statusBadge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 20 },
    statusText: { fontSize: 11, fontWeight: '600', textTransform: 'capitalize' },
    divider: { height: 1, backgroundColor: '#F0F0F0', marginVertical: 12 },
    cardFooter: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
    totalLabel: { fontSize: 14, color: '#555' },
    totalValue: { fontWeight: 'bold', color: colors.primary },
    itemCount: { fontSize: 12, color: '#888' },
    tableInfo: { fontSize: 13, color: '#555', marginTop: 4 },
    loadingContainer: { flex: 1, justifyContent: 'center', alignItems: 'center' },
    emptyContainer: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingTop: 60 },
    emptyText: { fontSize: 16, color: '#999', marginTop: 16 },
});

export default OrdersScreen;
