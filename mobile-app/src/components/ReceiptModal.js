import React, { useEffect, useRef } from 'react';
import {
    View,
    Text,
    TouchableOpacity,
    StyleSheet,
    Modal,
    Animated,
    ScrollView,
    Dimensions,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import MaterialIcon from 'react-native-vector-icons/MaterialCommunityIcons';
import LinearGradient from 'react-native-linear-gradient';
const { height } = Dimensions.get('window');
const ReceiptModal = ({ visible, onClose, receiptData }) => {
    const scaleAnim = useRef(new Animated.Value(0.8)).current;
    const opacityAnim = useRef(new Animated.Value(0)).current;
    useEffect(() => {
        if (visible) {
            Animated.parallel([
                Animated.spring(scaleAnim, {
                    toValue: 1,
                    tension: 100,
                    friction: 10,
                    useNativeDriver: true,
                }),
                Animated.timing(opacityAnim, {
                    toValue: 1,
                    duration: 200,
                    useNativeDriver: true,
                }),
            ]).start();
        } else {
            scaleAnim.setValue(0.8);
            opacityAnim.setValue(0);
        }
    }, [visible]);
    const formatPrice = (price) => {
        return `Rp ${parseInt(price || 0).toLocaleString('id-ID')}`;
    };
    if (!visible || !receiptData) return null;
    return (
        <Modal transparent visible={visible} animationType="none">
            {}
            <View style={styles.backdrop}>
                <TouchableOpacity style={styles.backdropTouch} onPress={onClose} />
            </View>
            {}
            <Animated.View
                style={[
                    styles.container,
                    {
                        opacity: opacityAnim,
                        transform: [{ scale: scaleAnim }],
                    },
                ]}
            >
                {}
                <View style={styles.successHeader}>
                    <View style={styles.successIconContainer}>
                        <Icon name="checkmark-circle" size={60} color="#10B981" />
                    </View>
                    <Text style={styles.successTitle}>Pesanan Berhasil!</Text>
                    <Text style={styles.successSubtitle}>Terima kasih atas pesanan Anda</Text>
                </View>
                {}
                <View style={styles.receiptSection}>
                    {}
                    <View style={styles.orderInfo}>
                        <MaterialIcon name="receipt" size={20} color="#8B1538" />
                        <Text style={styles.orderNumber}>{receiptData.receiptNumber}</Text>
                    </View>
                    {}
                    <View style={styles.dateTimeRow}>
                        <View style={styles.dateTimeItem}>
                            <Icon name="calendar-outline" size={16} color="#6B7280" />
                            <Text style={styles.dateTimeText}>{receiptData.date}</Text>
                        </View>
                        <View style={styles.dateTimeItem}>
                            <Icon name="time-outline" size={16} color="#6B7280" />
                            <Text style={styles.dateTimeText}>{receiptData.time}</Text>
                        </View>
                    </View>
                    {}
                    <View style={styles.divider} />
                    {}
                    <Text style={styles.itemsTitle}>Detail Pesanan</Text>
                    <ScrollView style={styles.itemsList} showsVerticalScrollIndicator={false}>
                        {receiptData.items.map((item, index) => (
                            <View key={index} style={styles.itemRow}>
                                <View style={styles.itemLeft}>
                                    <Text style={styles.itemName}>{item.name}</Text>
                                    <Text style={styles.itemQty}>x{item.quantity}</Text>
                                </View>
                                <Text style={styles.itemTotal}>{formatPrice(item.price)}</Text>
                            </View>
                        ))}
                    </ScrollView>
                    {}
                    <View style={styles.totalSection}>
                        <Text style={styles.totalLabel}>Total Pembayaran</Text>
                        <Text style={styles.totalAmount}>{formatPrice(receiptData.total)}</Text>
                    </View>
                </View>
                {}
                <TouchableOpacity onPress={onClose} activeOpacity={0.8}>
                    <LinearGradient
                        colors={['#8B1538', '#6B0F2A']}
                        style={styles.closeButton}
                    >
                        <Text style={styles.closeButtonText}>Selesai</Text>
                    </LinearGradient>
                </TouchableOpacity>
            </Animated.View>
        </Modal>
    );
};
const styles = StyleSheet.create({
    backdrop: {
        ...StyleSheet.absoluteFillObject,
        backgroundColor: 'rgba(0,0,0,0.6)',
        justifyContent: 'center',
        alignItems: 'center',
    },
    backdropTouch: {
        ...StyleSheet.absoluteFillObject,
    },
    container: {
        position: 'absolute',
        top: '10%',
        left: 20,
        right: 20,
        backgroundColor: '#FFF',
        borderRadius: 24,
        maxHeight: height * 0.8,
        elevation: 20,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 10 },
        shadowOpacity: 0.25,
        shadowRadius: 20,
    },
    successHeader: {
        alignItems: 'center',
        paddingTop: 32,
        paddingBottom: 24,
        borderBottomWidth: 1,
        borderBottomColor: '#E5E7EB',
    },
    successIconContainer: {
        marginBottom: 16,
    },
    successTitle: {
        fontSize: 24,
        fontWeight: 'bold',
        color: '#10B981',
        marginBottom: 4,
    },
    successSubtitle: {
        fontSize: 14,
        color: '#6B7280',
    },
    receiptSection: {
        padding: 20,
    },
    orderInfo: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        marginBottom: 12,
    },
    orderNumber: {
        fontSize: 18,
        fontWeight: 'bold',
        color: '#8B1538',
        marginLeft: 8,
    },
    dateTimeRow: {
        flexDirection: 'row',
        justifyContent: 'center',
        gap: 24,
    },
    dateTimeItem: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    dateTimeText: {
        fontSize: 13,
        color: '#6B7280',
        marginLeft: 6,
    },
    divider: {
        height: 1,
        backgroundColor: '#E5E7EB',
        marginVertical: 20,
    },
    itemsTitle: {
        fontSize: 16,
        fontWeight: '600',
        color: '#1F2937',
        marginBottom: 12,
    },
    itemsList: {
        maxHeight: 200,
    },
    itemRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingVertical: 10,
        borderBottomWidth: 1,
        borderBottomColor: '#F3F4F6',
    },
    itemLeft: {
        flex: 1,
    },
    itemName: {
        fontSize: 14,
        color: '#374151',
        marginBottom: 2,
    },
    itemQty: {
        fontSize: 12,
        color: '#9CA3AF',
    },
    itemTotal: {
        fontSize: 14,
        fontWeight: '600',
        color: '#1F2937',
    },
    totalSection: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginTop: 16,
        paddingTop: 16,
        borderTopWidth: 2,
        borderTopColor: '#8B1538',
    },
    totalLabel: {
        fontSize: 16,
        fontWeight: '600',
        color: '#374151',
    },
    totalAmount: {
        fontSize: 22,
        fontWeight: 'bold',
        color: '#8B1538',
    },
    closeButton: {
        marginHorizontal: 20,
        marginBottom: 20,
        paddingVertical: 16,
        borderRadius: 12,
        alignItems: 'center',
    },
    closeButtonText: {
        fontSize: 16,
        fontWeight: 'bold',
        color: '#FFF',
    },
});
export default ReceiptModal;