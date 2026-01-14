import React, { useEffect, useRef } from 'react';
import {
    View,
    Text,
    TouchableOpacity,
    StyleSheet,
    Modal,
    Animated,
    FlatList,
    Image,
    Dimensions,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import LinearGradient from 'react-native-linear-gradient';
import { useCart } from '../context/CartContext';

const { height } = Dimensions.get('window');
const BASE_IMAGE_URL = 'https://website-restaurant.up.railway.app/storage/';

const CartPopup = ({ visible, onClose, onCheckout }) => {
    const { cartItems, cartTotal, updateQuantity, removeItem } = useCart();
    const slideAnim = useRef(new Animated.Value(height)).current;
    const backdropAnim = useRef(new Animated.Value(0)).current;

    useEffect(() => {
        if (visible) {
            Animated.parallel([
                Animated.timing(backdropAnim, {
                    toValue: 1,
                    duration: 300,
                    useNativeDriver: true,
                }),
                Animated.spring(slideAnim, {
                    toValue: 0,
                    tension: 65,
                    friction: 11,
                    useNativeDriver: true,
                }),
            ]).start();
        } else {
            Animated.parallel([
                Animated.timing(backdropAnim, {
                    toValue: 0,
                    duration: 200,
                    useNativeDriver: true,
                }),
                Animated.timing(slideAnim, {
                    toValue: height,
                    duration: 250,
                    useNativeDriver: true,
                }),
            ]).start();
        }
    }, [visible]);

    const getImageUrl = (imagePath) => {
        if (!imagePath) return 'https://via.placeholder.com/80';
        if (imagePath.startsWith('http')) return imagePath;
        const cleanPath = imagePath.replace(/^public\//, '');
        return `${BASE_IMAGE_URL}${cleanPath}`;
    };

    const formatPrice = (price) => {
        return `Rp ${parseInt(price || 0).toLocaleString('id-ID')}`;
    };

    const renderCartItem = ({ item, index }) => (
        <Animated.View style={styles.cartItem}>
            <Image
                source={{ uri: getImageUrl(item.menu_image) }}
                style={styles.itemImage}
            />
            <View style={styles.itemInfo}>
                <Text style={styles.itemName} numberOfLines={1}>{item.menu_name}</Text>
                <Text style={styles.itemPrice}>{formatPrice(item.price)}</Text>

                {/* Quantity Controls */}
                <View style={styles.quantityControls}>
                    <TouchableOpacity
                        style={styles.qtyBtn}
                        onPress={() => updateQuantity(item.id, item.quantity - 1)}
                    >
                        <Icon name="remove" size={16} color="#FFF" />
                    </TouchableOpacity>
                    <Text style={styles.qtyText}>{item.quantity}</Text>
                    <TouchableOpacity
                        style={styles.qtyBtn}
                        onPress={() => updateQuantity(item.id, item.quantity + 1)}
                    >
                        <Icon name="add" size={16} color="#FFF" />
                    </TouchableOpacity>
                </View>
            </View>

            {/* Remove Button */}
            <TouchableOpacity
                style={styles.removeBtn}
                onPress={() => removeItem(item.id)}
            >
                <Icon name="trash-outline" size={20} color="#EF4444" />
            </TouchableOpacity>
        </Animated.View>
    );

    const renderEmpty = () => (
        <View style={styles.emptyContainer}>
            <Text style={styles.emptyEmoji}>🛒</Text>
            <Text style={styles.emptyText}>Keranjang masih kosong</Text>
        </View>
    );

    if (!visible) return null;

    return (
        <Modal transparent visible={visible} animationType="none">
            {/* Backdrop */}
            <Animated.View
                style={[styles.backdrop, { opacity: backdropAnim }]}
            >
                <TouchableOpacity style={styles.backdropTouch} onPress={onClose} />
            </Animated.View>

            {/* Bottom Sheet */}
            <Animated.View
                style={[
                    styles.container,
                    { transform: [{ translateY: slideAnim }] },
                ]}
            >
                {/* Header */}
                <View style={styles.header}>
                    <View style={styles.headerHandle} />
                    <View style={styles.headerRow}>
                        <Text style={styles.headerTitle}>
                            Keranjang ({cartItems.length} item)
                        </Text>
                        <TouchableOpacity style={styles.closeBtn} onPress={onClose}>
                            <Icon name="close" size={24} color="#374151" />
                        </TouchableOpacity>
                    </View>
                </View>

                {/* Items List */}
                <FlatList
                    data={cartItems}
                    keyExtractor={(item) => item.id.toString()}
                    renderItem={renderCartItem}
                    ListEmptyComponent={renderEmpty}
                    contentContainerStyle={styles.listContent}
                    showsVerticalScrollIndicator={false}
                />

                {/* Footer with Total & Checkout */}
                {cartItems.length > 0 && (
                    <View style={styles.footer}>
                        <View style={styles.totalRow}>
                            <Text style={styles.totalLabel}>Total</Text>
                            <Text style={styles.totalValue}>{formatPrice(cartTotal)}</Text>
                        </View>
                        <TouchableOpacity onPress={onCheckout} activeOpacity={0.8}>
                            <LinearGradient
                                colors={['#8B1538', '#6B0F2A']}
                                start={{ x: 0, y: 0 }}
                                end={{ x: 1, y: 0 }}
                                style={styles.checkoutBtn}
                            >
                                <Icon name="cart-outline" size={20} color="#FFF" />
                                <Text style={styles.checkoutText}>Checkout Sekarang</Text>
                            </LinearGradient>
                        </TouchableOpacity>
                    </View>
                )}
            </Animated.View>
        </Modal>
    );
};

const styles = StyleSheet.create({
    backdrop: {
        ...StyleSheet.absoluteFillObject,
        backgroundColor: 'rgba(0,0,0,0.5)',
    },
    backdropTouch: {
        flex: 1,
    },
    container: {
        position: 'absolute',
        bottom: 0,
        left: 0,
        right: 0,
        backgroundColor: '#FFF',
        borderTopLeftRadius: 24,
        borderTopRightRadius: 24,
        maxHeight: height * 0.8,
        elevation: 20,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: -4 },
        shadowOpacity: 0.15,
        shadowRadius: 12,
    },
    header: {
        paddingTop: 8,
        paddingHorizontal: 16,
        paddingBottom: 16,
        borderBottomWidth: 1,
        borderBottomColor: '#E5E7EB',
    },
    headerHandle: {
        width: 40,
        height: 4,
        backgroundColor: '#D1D5DB',
        borderRadius: 2,
        alignSelf: 'center',
        marginBottom: 12,
    },
    headerRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
    },
    headerTitle: {
        fontSize: 20,
        fontWeight: 'bold',
        color: '#1F2937',
    },
    closeBtn: {
        padding: 8,
        borderRadius: 20,
        backgroundColor: '#F3F4F6',
    },
    listContent: {
        padding: 16,
        flexGrow: 1,
    },
    cartItem: {
        flexDirection: 'row',
        padding: 12,
        backgroundColor: '#F9FAFB',
        borderRadius: 12,
        marginBottom: 12,
        alignItems: 'center',
    },
    itemImage: {
        width: 70,
        height: 70,
        borderRadius: 10,
        backgroundColor: '#E5E7EB',
    },
    itemInfo: {
        flex: 1,
        marginLeft: 12,
    },
    itemName: {
        fontSize: 15,
        fontWeight: '600',
        color: '#1F2937',
        marginBottom: 4,
    },
    itemPrice: {
        fontSize: 14,
        fontWeight: 'bold',
        color: '#8B1538',
        marginBottom: 8,
    },
    quantityControls: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    qtyBtn: {
        width: 28,
        height: 28,
        borderRadius: 6,
        backgroundColor: '#8B1538',
        justifyContent: 'center',
        alignItems: 'center',
    },
    qtyText: {
        fontSize: 15,
        fontWeight: 'bold',
        color: '#1F2937',
        marginHorizontal: 12,
        minWidth: 20,
        textAlign: 'center',
    },
    removeBtn: {
        padding: 10,
        borderRadius: 10,
    },
    emptyContainer: {
        alignItems: 'center',
        paddingVertical: 48,
    },
    emptyEmoji: {
        fontSize: 60,
        marginBottom: 16,
    },
    emptyText: {
        fontSize: 16,
        color: '#6B7280',
    },
    footer: {
        padding: 16,
        borderTopWidth: 1,
        borderTopColor: '#E5E7EB',
        backgroundColor: '#FFF',
    },
    totalRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: 12,
    },
    totalLabel: {
        fontSize: 16,
        color: '#6B7280',
    },
    totalValue: {
        fontSize: 24,
        fontWeight: 'bold',
        color: '#1F2937',
    },
    checkoutBtn: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        paddingVertical: 16,
        borderRadius: 12,
    },
    checkoutText: {
        fontSize: 16,
        fontWeight: 'bold',
        color: '#FFF',
        marginLeft: 8,
    },
});

export default CartPopup;
