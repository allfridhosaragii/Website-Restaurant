import React, { useState, useEffect, useCallback } from 'react';
import {
    View,
    Text,
    FlatList,
    StyleSheet,
    TouchableOpacity,
    ActivityIndicator,
    RefreshControl,
} from 'react-native';
import { useFocusEffect } from '@react-navigation/native';
import Icon from 'react-native-vector-icons/Ionicons';
import { colors, spacing, fontSize, borderRadius } from '../../theme/colors';
import { favoritesAPI } from '../../api/client';
const FavoritesScreen = ({ navigation }) => {
    const [favorites, setFavorites] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    useFocusEffect(
        useCallback(() => {
            loadFavorites();
        }, [])
    );
    const loadFavorites = async () => {
        try {
            const response = await favoritesAPI.getAll();
            setFavorites(response.data.favorites || []);
        } catch (error) {
            console.log('Load favorites error:', error);
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };
    const formatPrice = (price) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(price);
    };
    const renderItem = ({ item }) => (
        <TouchableOpacity
            style={styles.card}
            onPress={() => navigation.navigate('MenuDetail', { slug: item.menu?.slug })}
        >
            <View style={styles.cardContent}>
                <Text style={styles.name} numberOfLines={1}>{item.menu?.name}</Text>
                <Text style={styles.category}>{item.menu?.category}</Text>
                <Text style={styles.price}>{formatPrice(item.menu?.price)}</Text>
            </View>
            <Icon name="heart" size={24} color={colors.error} />
        </TouchableOpacity>
    );
    if (loading) {
        return (
            <View style={styles.loader}>
                <ActivityIndicator size="large" color={colors.accent} />
            </View>
        );
    }
    return (
        <View style={styles.container}>
            <FlatList
                data={favorites}
                keyExtractor={(item) => item.id.toString()}
                renderItem={renderItem}
                contentContainerStyle={styles.list}
                refreshControl={
                    <RefreshControl refreshing={refreshing} onRefresh={loadFavorites} />
                }
                ListEmptyComponent={
                    <View style={styles.empty}>
                        <Icon name="heart-outline" size={64} color={colors.textSecondary} />
                        <Text style={styles.emptyTitle}>Belum Ada Favorit</Text>
                        <Text style={styles.emptyText}>Tambahkan menu favorit Anda</Text>
                    </View>
                }
            />
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
    },
    list: {
        padding: spacing.md,
    },
    card: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: colors.surface,
        borderRadius: borderRadius.lg,
        padding: spacing.md,
        marginBottom: spacing.md,
    },
    cardContent: {
        flex: 1,
    },
    name: {
        fontSize: fontSize.lg,
        fontWeight: '600',
        color: colors.text,
    },
    category: {
        fontSize: fontSize.sm,
        color: colors.accent,
        textTransform: 'capitalize',
        marginTop: spacing.xs,
    },
    price: {
        fontSize: fontSize.md,
        color: colors.textSecondary,
        marginTop: spacing.xs,
    },
    empty: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        paddingTop: spacing.xxl,
    },
    emptyTitle: {
        fontSize: fontSize.xl,
        fontWeight: '600',
        color: colors.text,
        marginTop: spacing.lg,
    },
    emptyText: {
        fontSize: fontSize.md,
        color: colors.textSecondary,
        marginTop: spacing.sm,
    },
});
export default FavoritesScreen;