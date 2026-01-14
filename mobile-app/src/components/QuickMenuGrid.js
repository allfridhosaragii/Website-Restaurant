import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import { colors } from '../theme/colors';

const MENU_ITEMS = [
    { id: 1, label: 'Pesan', icon: 'food', color: colors.primary },
    { id: 2, label: 'Reservasi', icon: 'calendar-clock', color: colors.accent.orange },
    { id: 3, label: 'Promo', icon: 'ticket-percent', color: colors.accent.purple },
    { id: 4, label: 'Voucher', icon: 'wallet-giftcard', color: colors.secondary },
];

const QuickMenuGrid = () => {
    return (
        <View style={styles.container}>
            {MENU_ITEMS.map((item) => (
                <TouchableOpacity key={item.id} style={styles.item}>
                    <View style={[styles.iconContainer, { backgroundColor: item.color + '20' }]}>
                        <Icon name={item.icon} size={28} color={item.color} />
                    </View>
                    <Text style={styles.label}>{item.label}</Text>
                </TouchableOpacity>
            ))}
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        paddingHorizontal: 20,
        marginBottom: 20,
    },
    item: {
        alignItems: 'center',
    },
    iconContainer: {
        width: 60,
        height: 60,
        borderRadius: 16,
        justifyContent: 'center',
        alignItems: 'center',
        marginBottom: 8,
    },
    label: {
        fontSize: 12,
        color: colors.text,
        fontWeight: '500',
    },
});

export default QuickMenuGrid;
