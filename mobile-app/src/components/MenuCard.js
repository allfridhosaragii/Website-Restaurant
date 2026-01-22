import React from 'react';
import { View, Text, Image, TouchableOpacity, StyleSheet } from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { colors, spacing, fontSize, borderRadius } from '../theme/colors';
const MenuCard = ({ menu, onPress, horizontal = false }) => {
    const formatPrice = (price) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(price);
    };
    if (horizontal) {
        return (
            <TouchableOpacity style={styles.cardHorizontal} onPress={onPress}>
                <Image
                    source={{ uri: menu.image_url || 'https://via.placeholder.com/150' }}
                    style={styles.imageHorizontal}
                    resizeMode="cover"
                />
                <View style={styles.infoHorizontal}>
                    <Text style={styles.name} numberOfLines={1}>{menu.name}</Text>
                    <Text style={styles.price}>{formatPrice(menu.price)}</Text>
                </View>
                {menu.is_favorite && (
                    <Icon name="heart" size={16} color={colors.error} style={styles.favoriteIcon} />
                )}
            </TouchableOpacity>
        );
    }
    return (
        <TouchableOpacity style={styles.card} onPress={onPress}>
            <Image
                source={{ uri: menu.image_url || 'https://via.placeholder.com/150' }}
                style={styles.image}
                resizeMode="cover"
            />
            <View style={styles.info}>
                <Text style={styles.category}>{menu.category}</Text>
                <Text style={styles.name} numberOfLines={2}>{menu.name}</Text>
                <Text style={styles.price}>{formatPrice(menu.price)}</Text>
            </View>
            {menu.is_favorite && (
                <View style={styles.favoriteBadge}>
                    <Icon name="heart" size={14} color={colors.white} />
                </View>
            )}
        </TouchableOpacity>
    );
};
const styles = StyleSheet.create({
    card: {
        width: '100%', 
        backgroundColor: '#1E1E1E', 
        borderRadius: 24, 
        marginBottom: spacing.md,
        overflow: 'hidden',
    },
    image: {
        width: '100%',
        height: 160, 
    },
    info: {
        padding: 12,
        alignItems: 'center', 
    },
    category: {
        fontSize: 10,
        color: colors.primary,
        fontWeight: 'bold',
        textTransform: 'uppercase',
        letterSpacing: 1,
        marginBottom: 4,
        textAlign: 'center',
    },
    name: {
        fontSize: 16, 
        fontWeight: 'bold',
        color: colors.white,
        marginBottom: 4,
        textAlign: 'center',
        fontFamily: 'serif',
        height: 44, 
    },
    price: {
        fontSize: 16,
        fontWeight: '500',
        color: colors.primary,
        textAlign: 'center',
    },
    favoriteBadge: {
        position: 'absolute',
        top: spacing.sm,
        right: spacing.sm,
        backgroundColor: colors.error,
        borderRadius: 12,
        width: 24,
        height: 24,
        justifyContent: 'center',
        alignItems: 'center',
    },
    cardHorizontal: {
        width: 160,
        backgroundColor: colors.surface,
        borderRadius: borderRadius.lg,
        overflow: 'hidden',
        borderWidth: 1,
        borderColor: colors.border,
    },
    imageHorizontal: {
        width: '100%',
        height: 100,
    },
    infoHorizontal: {
        padding: spacing.sm,
    },
    favoriteIcon: {
        position: 'absolute',
        top: spacing.sm,
        right: spacing.sm,
    },
});
export default MenuCard;