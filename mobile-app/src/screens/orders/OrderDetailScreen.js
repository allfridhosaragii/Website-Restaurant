import React, { useState, useEffect } from 'react';
import {
    View,
    Text,
    ScrollView,
    StyleSheet,
    ActivityIndicator,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { colors, spacing, fontSize, borderRadius } from '../../theme/colors';
import { orderAPI } from '../../api/client';
const OrderDetailScreen = ({ route }) => {
    const { id } = route.params;
    const [order, setOrder] = useState(null);
    const [loading, setLoading] = useState(true);
    useEffect(() => {
        loadOrder();
    }, [id]);
    const loadOrder = async () => {
        try {
            const response = await orderAPI.getById(id);
            setOrder(response.data.order);
        } catch (error) {
            console.log('Load order error:', error);
        } finally {
            setLoading(false);
        }
    };
    const formatPrice = (price) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(price);
    };
    const getStatusColor = (status) => {
        switch (status) {
            case 'completed': return colors.success;
            case 'cancelled': return colors.error;
            case 'pending': return colors.warning;
            default: return colors.accent;
        }
    };
    if (loading) {
        return (
            <View style={styles.loader}>
                <ActivityIndicator size="large" color={colors.accent} />
            </View>
        );
    }
    if (!order) {
        return null;
    }
    return (
        <ScrollView style={styles.container}>
            {}
            <View style={styles.section}>
                <View style={styles.headerRow}>
                    <Text style={styles.orderNo}>#{order.order_number}</Text>
                    <View style={[styles.statusBadge, { backgroundColor: getStatusColor(order.status) }]}>
                        <Text style={styles.statusText}>{order.status}</Text>
                    </View>
                </View>
                <Text style={styles.date}>{order.created_at}</Text>
            </View>
            {}
            <View style={styles.section}>
                <Text style={styles.sectionTitle}>Item Pesanan</Text>
                {order.items?.map((item, index) => (
                    <View key={index} style={styles.item}>
                        <View style={styles.itemInfo}>
                            <Text style={styles.itemName}>{item.menu_name}</Text>
                            <Text style={styles.itemPrice}>{formatPrice(item.price)}</Text>
                        </View>
                        <Text style={styles.itemQty}>x{item.quantity}</Text>
                        <Text style={styles.itemTotal}>{formatPrice(item.subtotal)}</Text>
                    </View>
                ))}
            </View>
            {}
            <View style={styles.section}>
                <Text style={styles.sectionTitle}>Ringkasan Pembayaran</Text>
                <View style={styles.summaryRow}>
                    <Text style={styles.summaryLabel}>Subtotal</Text>
                    <Text style={styles.summaryValue}>{formatPrice(order.subtotal)}</Text>
                </View>
                <View style={styles.summaryRow}>
                    <Text style={styles.summaryLabel}>Pajak</Text>
                    <Text style={styles.summaryValue}>{formatPrice(order.tax)}</Text>
                </View>
                <View style={[styles.summaryRow, styles.totalRow]}>
                    <Text style={styles.totalLabel}>Total</Text>
                    <Text style={styles.totalValue}>{formatPrice(order.total)}</Text>
                </View>
            </View>
            {}
            <View style={styles.section}>
                <View style={styles.infoRow}>
                    <Icon name="card-outline" size={20} color={colors.accent} />
                    <Text style={styles.infoLabel}>Metode Pembayaran</Text>
                    <Text style={styles.infoValue}>{order.payment_method || 'Cash'}</Text>
                </View>
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
    },
    section: {
        padding: spacing.lg,
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
    },
    headerRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: spacing.xs,
    },
    orderNo: {
        fontSize: fontSize.xl,
        fontWeight: 'bold',
        color: colors.text,
    },
    statusBadge: {
        paddingVertical: spacing.xs,
        paddingHorizontal: spacing.md,
        borderRadius: borderRadius.full,
    },
    statusText: {
        fontSize: fontSize.sm,
        color: colors.white,
        fontWeight: '600',
        textTransform: 'uppercase',
    },
    date: {
        fontSize: fontSize.md,
        color: colors.textSecondary,
    },
    sectionTitle: {
        fontSize: fontSize.lg,
        fontWeight: '600',
        color: colors.text,
        marginBottom: spacing.md,
    },
    item: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingVertical: spacing.sm,
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
    },
    itemInfo: {
        flex: 1,
    },
    itemName: {
        fontSize: fontSize.md,
        color: colors.text,
    },
    itemPrice: {
        fontSize: fontSize.sm,
        color: colors.textSecondary,
    },
    itemQty: {
        fontSize: fontSize.md,
        color: colors.textSecondary,
        marginHorizontal: spacing.md,
    },
    itemTotal: {
        fontSize: fontSize.md,
        fontWeight: '600',
        color: colors.text,
    },
    summaryRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        paddingVertical: spacing.sm,
    },
    summaryLabel: {
        fontSize: fontSize.md,
        color: colors.textSecondary,
    },
    summaryValue: {
        fontSize: fontSize.md,
        color: colors.text,
    },
    totalRow: {
        borderTopWidth: 1,
        borderTopColor: colors.border,
        marginTop: spacing.sm,
        paddingTop: spacing.md,
    },
    totalLabel: {
        fontSize: fontSize.lg,
        fontWeight: '600',
        color: colors.text,
    },
    totalValue: {
        fontSize: fontSize.xl,
        fontWeight: 'bold',
        color: colors.accent,
    },
    infoRow: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: spacing.sm,
    },
    infoLabel: {
        flex: 1,
        fontSize: fontSize.md,
        color: colors.textSecondary,
    },
    infoValue: {
        fontSize: fontSize.md,
        color: colors.text,
        textTransform: 'capitalize',
    },
});
export default OrderDetailScreen;