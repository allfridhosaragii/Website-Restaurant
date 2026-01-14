import React, { useState, useEffect } from 'react';
import {
    View,
    Text,
    ScrollView,
    StyleSheet,
    TouchableOpacity,
    ActivityIndicator,
    Alert,
    StatusBar,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import LinearGradient from 'react-native-linear-gradient';
import Animated, { FadeInUp, SlideInDown } from 'react-native-reanimated';
import { colors, spacing, fontSize, borderRadius, shadows } from '../../theme/colors';
import { cartAPI, orderAPI } from '../../api/client';

const CheckoutScreen = ({ navigation }) => {
    const [cartItems, setCartItems] = useState([]);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(true);
    const [placing, setPlacing] = useState(false);
    const [paymentMethod, setPaymentMethod] = useState('cash');

    useEffect(() => {
        loadCart();
    }, []);

    const loadCart = async () => {
        try {
            const response = await cartAPI.get();
            setCartItems(response.data.items || []);
            setTotal(response.data.total || 0);
        } catch (error) {
            console.log('Load cart error:', error);
        } finally {
            setLoading(false);
        }
    };

    const handlePlaceOrder = async () => {
        try {
            setPlacing(true);
            await orderAPI.create({ payment_method: paymentMethod });
            Alert.alert(
                'Order Success',
                'Your order has been placed. Please wait for confirmation.',
                [{ text: 'OK', onPress: () => navigation.replace('Orders') }]
            );
        } catch (error) {
            Alert.alert('Error', 'Failed to place order');
        } finally {
            setPlacing(false);
        }
    };

    const formatPrice = (price) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(price);
    };

    if (loading) {
        return (
            <View style={styles.loader}>
                <ActivityIndicator size="large" color={colors.primary} />
            </View>
        );
    }

    return (
        <View style={styles.container}>
            <StatusBar barStyle="light-content" backgroundColor={colors.background} />
            <ScrollView contentContainerStyle={styles.scroll} showsVerticalScrollIndicator={false}>

                <Text style={styles.screenTitle}>Checkout</Text>

                {/* Order Summary */}
                <Animated.View entering={FadeInUp.delay(100).springify()} style={styles.section}>
                    <Text style={styles.sectionTitle}>Order Summary</Text>
                    <View style={styles.summaryCard}>
                        {cartItems.map((item, index) => (
                            <View key={item.id} style={[styles.summaryItem, index === cartItems.length - 1 && styles.lastItem]}>
                                <View style={styles.itemRow}>
                                    <Text style={styles.itemQty}>{item.quantity}x</Text>
                                    <Text style={styles.itemName} numberOfLines={1}>{item.menu_name}</Text>
                                </View>
                                <Text style={styles.itemTotal}>{formatPrice(item.subtotal)}</Text>
                            </View>
                        ))}
                    </View>
                </Animated.View>

                {/* Payment Method */}
                <Animated.View entering={FadeInUp.delay(200).springify()} style={styles.section}>
                    <Text style={styles.sectionTitle}>Payment Method</Text>
                    <View style={styles.paymentContainer}>
                        {['cash', 'transfer', 'qris'].map((method) => {
                            const isActive = paymentMethod === method;
                            return (
                                <TouchableOpacity
                                    key={method}
                                    style={[
                                        styles.paymentOption,
                                        isActive && styles.paymentOptionActive,
                                    ]}
                                    onPress={() => setPaymentMethod(method)}
                                    activeOpacity={0.8}
                                >
                                    <View style={[styles.iconContainer, isActive && styles.iconActive]}>
                                        <Icon
                                            name={
                                                method === 'cash' ? 'cash-outline' :
                                                    method === 'transfer' ? 'card-outline' : 'qr-code-outline'
                                            }
                                            size={24}
                                            color={isActive ? colors.black : colors.textSecondary}
                                        />
                                    </View>
                                    <View style={styles.paymentTextContainer}>
                                        <Text
                                            style={[
                                                styles.paymentTitle,
                                                isActive && styles.paymentTitleActive,
                                            ]}
                                        >
                                            {method === 'cash' ? 'Cash' : method === 'transfer' ? 'Bank Transfer' : 'QRIS'}
                                        </Text>
                                        <Text style={styles.paymentSubtitle}>
                                            {method === 'cash' ? 'Pay at cashier' : 'Instant verification'}
                                        </Text>
                                    </View>

                                    <View style={[styles.radioOuter, isActive && styles.radioActive]}>
                                        {isActive && <View style={styles.radioInner} />}
                                    </View>
                                </TouchableOpacity>
                            );
                        })}
                    </View>
                </Animated.View>

                {/* Total */}
                <Animated.View entering={FadeInUp.delay(300).springify()} style={styles.section}>
                    <View style={styles.totalCard}>
                        <View style={styles.totalRow}>
                            <Text style={styles.totalLabel}>Subtotal</Text>
                            <Text style={styles.totalValue}>{formatPrice(total)}</Text>
                        </View>
                        <View style={styles.totalRow}>
                            <Text style={styles.totalLabel}>Tax (10%)</Text>
                            <Text style={styles.totalValue}>{formatPrice(total * 0.1)}</Text>
                        </View>
                        <View style={styles.divider} />
                        <View style={styles.totalRow}>
                            <Text style={styles.grandTotalLabel}>Total Amount</Text>
                            <Text style={styles.grandTotalValue}>{formatPrice(total * 1.1)}</Text>
                        </View>
                    </View>
                </Animated.View>

                <View style={{ height: 100 }} />
            </ScrollView>

            {/* Place Order Button */}
            <Animated.View entering={SlideInDown.springify()} style={styles.bottomBar}>
                <TouchableOpacity
                    style={styles.placeOrderBtnWrapper}
                    onPress={handlePlaceOrder}
                    disabled={placing}
                    activeOpacity={0.8}
                >
                    {placing ? (
                        <View style={styles.loaderBtn}>
                            <ActivityIndicator color={colors.black} />
                        </View>
                    ) : (
                        <LinearGradient
                            colors={[colors.primary, colors.primaryDim]}
                            start={{ x: 0, y: 0 }} end={{ x: 1, y: 0 }}
                            style={styles.placeOrderGradient}
                        >
                            <Text style={styles.placeOrderText}>Confirm Order</Text>
                            <Icon name="checkmark-circle" size={24} color={colors.black} />
                        </LinearGradient>
                    )}
                </TouchableOpacity>
            </Animated.View>
        </View>
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
    scroll: {
        padding: spacing.lg,
    },
    screenTitle: {
        fontSize: fontSize.xxxl,
        fontWeight: 'bold',
        color: colors.primary,
        fontFamily: 'serif',
        marginBottom: spacing.xl,
        marginTop: spacing.md,
    },
    section: {
        marginBottom: spacing.xl,
    },
    sectionTitle: {
        fontSize: fontSize.md,
        fontWeight: 'bold',
        color: colors.textSecondary,
        marginBottom: spacing.md,
        textTransform: 'uppercase',
        letterSpacing: 1,
    },
    summaryCard: {
        backgroundColor: colors.surface,
        borderRadius: borderRadius.lg,
        padding: spacing.md,
        borderWidth: 1,
        borderColor: colors.border,
    },
    summaryItem: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingVertical: spacing.sm,
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
    },
    lastItem: {
        borderBottomWidth: 0,
    },
    itemRow: {
        flexDirection: 'row',
        alignItems: 'center',
        flex: 1,
    },
    itemQty: {
        fontSize: fontSize.md,
        color: colors.primary,
        fontWeight: 'bold',
        marginRight: spacing.sm,
        width: 30,
    },
    itemName: {
        fontSize: fontSize.md,
        color: colors.text,
        flex: 1,
    },
    itemTotal: {
        fontSize: fontSize.md,
        color: colors.text,
        fontWeight: '600',
    },
    paymentContainer: {
        gap: spacing.sm,
    },
    paymentOption: {
        flexDirection: 'row',
        alignItems: 'center',
        padding: spacing.md,
        backgroundColor: colors.surface,
        borderRadius: borderRadius.lg,
        borderWidth: 1,
        borderColor: colors.border,
    },
    paymentOptionActive: {
        borderColor: colors.primary,
        backgroundColor: colors.surfaceLight,
    },
    iconContainer: {
        width: 40,
        height: 40,
        borderRadius: 20,
        backgroundColor: colors.background,
        justifyContent: 'center',
        alignItems: 'center',
    },
    iconActive: {
        backgroundColor: colors.primary,
    },
    paymentTextContainer: {
        flex: 1,
        marginLeft: spacing.md,
    },
    paymentTitle: {
        fontSize: fontSize.md,
        color: colors.text,
        fontWeight: '500',
    },
    paymentTitleActive: {
        fontWeight: 'bold',
        color: colors.text,
    },
    paymentSubtitle: {
        fontSize: fontSize.xs,
        color: colors.textSecondary,
    },
    radioOuter: {
        width: 20,
        height: 20,
        borderRadius: 10,
        borderWidth: 2,
        borderColor: colors.textSecondary,
        justifyContent: 'center',
        alignItems: 'center',
    },
    radioActive: {
        borderColor: colors.primary,
    },
    radioInner: {
        width: 10,
        height: 10,
        borderRadius: 5,
        backgroundColor: colors.primary,
    },
    totalCard: {
        backgroundColor: colors.surface,
        borderRadius: borderRadius.lg,
        padding: spacing.lg,
        borderWidth: 1,
        borderColor: colors.border,
    },
    totalRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        paddingVertical: 4,
    },
    totalLabel: {
        fontSize: fontSize.md,
        color: colors.textSecondary,
    },
    totalValue: {
        fontSize: fontSize.md,
        color: colors.text,
        fontWeight: '500',
    },
    divider: {
        height: 1,
        backgroundColor: colors.border,
        marginVertical: spacing.md,
    },
    grandTotalLabel: {
        fontSize: fontSize.lg,
        fontWeight: 'bold',
        color: colors.text,
    },
    grandTotalValue: {
        fontSize: fontSize.xl,
        fontWeight: 'bold',
        color: colors.primary,
    },
    bottomBar: {
        position: 'absolute',
        bottom: 0,
        left: 0,
        right: 0,
        padding: spacing.lg,
        paddingBottom: spacing.xl,
        backgroundColor: colors.surface,
        borderTopWidth: 1,
        borderTopColor: colors.border,
        ...shadows.lg,
    },
    placeOrderBtnWrapper: {
        borderRadius: borderRadius.full,
        overflow: 'hidden',
        ...shadows.lg,
    },
    loaderBtn: {
        backgroundColor: colors.primary,
        paddingVertical: spacing.md,
        alignItems: 'center',
    },
    placeOrderGradient: {
        flexDirection: 'row',
        justifyContent: 'center',
        alignItems: 'center',
        paddingVertical: spacing.md,
        gap: spacing.md,
    },
    placeOrderText: {
        color: colors.black,
        fontSize: fontSize.lg,
        fontWeight: 'bold',
        textTransform: 'uppercase',
        letterSpacing: 1,
    },
});

export default CheckoutScreen;
