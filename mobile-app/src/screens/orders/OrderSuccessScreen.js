import React, { useEffect } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, BackHandler } from 'react-native';
import LinearGradient from 'react-native-linear-gradient';
import Icon from 'react-native-vector-icons/Ionicons';
import Animated, { FadeInDown, ZoomIn } from 'react-native-reanimated';
import { colors } from '../../theme/colors';

const OrderSuccessScreen = ({ navigation, route }) => {
    // Generate random Order ID if not passed
    const orderId = route.params?.orderId || `#ORD-${Math.floor(100000 + Math.random() * 900000)}`;
    const total = route.params?.total || 150000;
    const items = route.params?.items || [];

    // Disable Back Button
    useEffect(() => {
        const backAction = () => true;
        const backHandler = BackHandler.addEventListener('hardwareBackPress', backAction);
        return () => backHandler.remove();
    }, []);

    const formatPrice = (price) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(price);
    };

    return (
        <View style={styles.container}>
            <ScrollView contentContainerStyle={styles.scrollContent}>
                <Animated.View entering={ZoomIn.duration(600).springify()} style={styles.iconContainer}>
                    <View style={styles.successCircle}>
                        <Icon name="checkmark" size={60} color="#FFF" />
                    </View>
                </Animated.View>

                <Animated.Text entering={FadeInDown.delay(300)} style={styles.title}>
                    Pesanan Diterima!
                </Animated.Text>
                <Animated.Text entering={FadeInDown.delay(400)} style={styles.subtitle}>
                    Kami sedang menyiapkan hidangan lezat untuk Anda.
                </Animated.Text>

                <Animated.View entering={FadeInDown.delay(600)} style={styles.receiptCard}>
                    <View style={styles.receiptHeader}>
                        <Text style={styles.receiptTitle}>Struk Digital</Text>
                        <Text style={styles.orderId}>{orderId}</Text>
                    </View>

                    <View style={styles.divider} />

                    <View style={styles.receiptRow}>
                        <Text style={styles.label}>Waktu</Text>
                        <Text style={styles.value}>{new Date().toLocaleString()}</Text>
                    </View>
                    <View style={styles.receiptRow}>
                        <Text style={styles.label}>Pembayaran</Text>
                        <Text style={styles.value}>QRIS / E-Wallet</Text>
                    </View>
                    <View style={styles.receiptRow}>
                        <Text style={styles.label}>Status</Text>
                        <View style={styles.statusBadge}>
                            <Text style={styles.statusText}>LUNAS</Text>
                        </View>
                    </View>

                    <View style={styles.divider} />

                    {/* Summary Items (Max 3) */}
                    <View style={styles.itemsContainer}>
                        {items.slice(0, 3).map((item, index) => (
                            <View key={index} style={styles.itemRow}>
                                <Text style={styles.itemName}>{item.quantity}x {item.menu_name || 'Menu Item'}</Text>
                                <Text style={styles.itemPrice}>{formatPrice((item.price || 0) * item.quantity)}</Text>
                            </View>
                        ))}
                        {items.length > 3 && (
                            <Text style={styles.moreItems}>+ {items.length - 3} menu lainnya</Text>
                        )}
                        {items.length === 0 && <Text style={styles.moreItems}>Paket Hemat Spesial</Text>}
                    </View>

                    <View style={styles.divider} />

                    <View style={styles.totalRow}>
                        <Text style={styles.totalLabel}>TOTAL</Text>
                        <Text style={styles.totalValue}>{formatPrice(total)}</Text>
                    </View>
                </Animated.View>
            </ScrollView>

            <View style={styles.footer}>
                <TouchableOpacity
                    style={styles.primaryBtn}
                    onPress={() => navigation.reset({ index: 0, routes: [{ name: 'MainTabs' }] })}
                >
                    <LinearGradient
                        colors={[colors.primary, '#5D0016']}
                        style={styles.gradientBtn}
                    >
                        <Text style={styles.btnText}>Kembali ke Beranda</Text>
                    </LinearGradient>
                </TouchableOpacity>

                <TouchableOpacity
                    style={styles.secondaryBtn}
                    onPress={() => {
                        navigation.reset({ index: 0, routes: [{ name: 'MainTabs' }] });
                        // Navigate to Orders tab logic would need precise tab handling, for now reset to home is safe
                        // Ideally: navigation.navigate('MainTabs', { screen: 'Orders' });
                        setTimeout(() => navigation.navigate('Orders'), 100);
                    }}
                >
                    <Text style={styles.btnTextSecondary}>Lihat Status Pesanan</Text>
                </TouchableOpacity>
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.primary, // Full burgundy bg for premium feel
    },
    scrollContent: {
        alignItems: 'center',
        paddingTop: 60,
        paddingHorizontal: 20,
        paddingBottom: 100,
    },
    iconContainer: {
        marginBottom: 20,
        shadowColor: "#000",
        shadowOffset: { width: 0, height: 10 },
        shadowOpacity: 0.3,
        shadowRadius: 20,
        elevation: 10,
    },
    successCircle: {
        width: 100,
        height: 100,
        borderRadius: 50,
        backgroundColor: '#4CAF50', // Success Green
        justifyContent: 'center',
        alignItems: 'center',
        borderWidth: 4,
        borderColor: '#FFF',
    },
    title: {
        fontSize: 24,
        fontWeight: 'bold',
        color: '#FFF',
        marginBottom: 8,
        fontFamily: 'serif',
    },
    subtitle: {
        fontSize: 14,
        color: 'rgba(255,255,255,0.8)',
        textAlign: 'center',
        marginBottom: 40,
    },
    receiptCard: {
        width: '100%',
        backgroundColor: '#FFF',
        borderRadius: 20,
        padding: 24,
        alignItems: 'center',
        elevation: 5,
    },
    receiptHeader: {
        width: '100%',
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: 16,
    },
    receiptTitle: {
        fontSize: 18,
        fontWeight: 'bold',
        color: colors.text,
    },
    orderId: {
        fontSize: 14,
        fontWeight: 'bold',
        color: colors.textSecondary,
        backgroundColor: '#F5F5F5',
        paddingHorizontal: 10,
        paddingVertical: 4,
        borderRadius: 8,
    },
    divider: {
        width: '100%',
        height: 1,
        backgroundColor: '#E0E0E0',
        marginVertical: 16,
        borderStyle: 'dashed',
        borderWidth: 1, // Simulate dash with border style if possible or just line
        borderColor: '#E0E0E0'
    },
    receiptRow: {
        width: '100%',
        flexDirection: 'row',
        justifyContent: 'space-between',
        marginBottom: 12,
    },
    label: {
        fontSize: 14,
        color: colors.textSecondary,
    },
    value: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.text,
    },
    statusBadge: {
        backgroundColor: '#E8F5E9',
        paddingHorizontal: 8,
        paddingVertical: 2,
        borderRadius: 4,
    },
    statusText: {
        color: '#2E7D32',
        fontSize: 12,
        fontWeight: 'bold',
    },
    itemsContainer: {
        width: '100%',
    },
    itemRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        marginBottom: 8,
    },
    itemName: {
        fontSize: 14,
        color: colors.text,
        flex: 1,
        marginRight: 10,
    },
    itemPrice: {
        fontSize: 14,
        color: colors.text,
        fontWeight: '500',
    },
    moreItems: {
        fontSize: 12,
        color: colors.textSecondary,
        fontStyle: 'italic',
        marginTop: 4,
    },
    totalRow: {
        width: '100%',
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
    },
    totalLabel: {
        fontSize: 16,
        fontWeight: 'bold',
        color: colors.text,
    },
    totalValue: {
        fontSize: 24,
        fontWeight: 'bold',
        color: colors.primary,
    },
    footer: {
        position: 'absolute',
        bottom: 0,
        left: 0,
        right: 0,
        padding: 20,
        backgroundColor: 'rgba(0,0,0,0)',
    },
    primaryBtn: {
        width: '100%',
        borderRadius: 30,
        marginBottom: 12,
        overflow: 'hidden',
        elevation: 4,
    },
    gradientBtn: {
        paddingVertical: 16,
        alignItems: 'center',
        backgroundColor: '#FFF' // Fallback
    },
    btnText: {
        color: '#FFF',
        fontWeight: 'bold',
        fontSize: 16,
        textTransform: 'uppercase',
    },
    secondaryBtn: {
        width: '100%',
        paddingVertical: 16,
        alignItems: 'center',
    },
    btnTextSecondary: {
        color: 'rgba(255,255,255,0.8)',
        fontWeight: '600',
        fontSize: 14,
    },
});

export default OrderSuccessScreen;
