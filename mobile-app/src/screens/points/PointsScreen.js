import React, { useState, useEffect, useCallback } from 'react';
import {
    View,
    Text,
    ScrollView,
    StyleSheet,
    RefreshControl,
    ActivityIndicator,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { colors, spacing, fontSize, borderRadius } from '../../theme/colors';
import { dashboardAPI } from '../../api/client';
import { useAuth } from '../../context/AuthContext';

const PointsScreen = ({ navigation }) => {
    const { user, isGuest } = useAuth();
    const [stats, setStats] = useState(null);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);

    const loadStats = useCallback(async () => {
        if (isGuest) {
            setLoading(false);
            return;
        }
        try {
            const response = await dashboardAPI.getStats();
            setStats(response.data);
        } catch (error) {
            console.log('Load stats error:', error);
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    }, [isGuest]);

    useEffect(() => {
        loadStats();
    }, [loadStats]);

    const onRefresh = () => {
        setRefreshing(true);
        loadStats();
    };

    const formatNumber = (num) => {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    };

    if (isGuest) {
        return (
            <View style={styles.emptyContainer}>
                <Icon name="star-outline" size={64} color={colors.textSecondary} />
                <Text style={styles.emptyTitle}>Login untuk Melihat Poin</Text>
                <Text style={styles.emptyText}>Kumpulkan poin dari setiap pesanan dan reservasi!</Text>
            </View>
        );
    }

    if (loading) {
        return (
            <View style={styles.loader}>
                <ActivityIndicator size="large" color={colors.accent} />
            </View>
        );
    }

    const orderPoints = (stats?.total_orders || 0) * 1000;
    const reservationPoints = (stats?.total_reservations || 0) * 10000;
    const totalPoints = stats?.points || (orderPoints + reservationPoints);

    return (
        <ScrollView
            style={styles.container}
            refreshControl={
                <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
            }
        >
            {/* Header */}
            <View style={styles.header}>
                <Text style={styles.headerTitle}>Poin Saya</Text>
            </View>

            {/* Points Card */}
            <View style={styles.pointsCard}>
                <Icon name="star" size={48} color={colors.accent} />
                <Text style={styles.pointsValue}>{formatNumber(totalPoints)}</Text>
                <Text style={styles.pointsLabel}>Total Poin</Text>
            </View>

            {/* How Points Work */}
            <View style={styles.section}>
                <Text style={styles.sectionTitle}>Cara Mendapatkan Poin</Text>

                <View style={styles.infoCard}>
                    <View style={styles.infoRow}>
                        <Icon name="receipt-outline" size={24} color={colors.accent} />
                        <View style={styles.infoContent}>
                            <Text style={styles.infoTitle}>Setiap Pesanan</Text>
                            <Text style={styles.infoValue}>+1.000 poin</Text>
                        </View>
                    </View>
                </View>

                <View style={styles.infoCard}>
                    <View style={styles.infoRow}>
                        <Icon name="calendar-outline" size={24} color={colors.accent} />
                        <View style={styles.infoContent}>
                            <Text style={styles.infoTitle}>Reservasi Diterima</Text>
                            <Text style={styles.infoValue}>+10.000 poin</Text>
                        </View>
                    </View>
                </View>
            </View>

            {/* Summary */}
            <View style={styles.section}>
                <Text style={styles.sectionTitle}>Ringkasan</Text>

                <View style={styles.summaryCard}>
                    <View style={styles.summaryRow}>
                        <Text style={styles.summaryLabel}>Total Pesanan</Text>
                        <Text style={styles.summaryValue}>{stats?.total_orders || 0}x</Text>
                    </View>
                    <View style={styles.summaryRow}>
                        <Text style={styles.summaryLabel}>Poin dari Pesanan</Text>
                        <Text style={styles.summaryValueAccent}>{formatNumber(orderPoints)} poin</Text>
                    </View>
                    <View style={styles.divider} />
                    <View style={styles.summaryRow}>
                        <Text style={styles.summaryLabel}>Total Reservasi</Text>
                        <Text style={styles.summaryValue}>{stats?.total_reservations || 0}x</Text>
                    </View>
                    <View style={styles.summaryRow}>
                        <Text style={styles.summaryLabel}>Poin dari Reservasi</Text>
                        <Text style={styles.summaryValueAccent}>{formatNumber(reservationPoints)} poin</Text>
                    </View>
                </View>
            </View>

            <View style={{ height: spacing.xxl }} />
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
        backgroundColor: colors.background,
    },
    header: {
        padding: spacing.lg,
        paddingTop: spacing.xxl,
    },
    headerTitle: {
        fontSize: fontSize.xxl,
        fontWeight: 'bold',
        color: colors.text,
    },
    pointsCard: {
        backgroundColor: colors.surface,
        margin: spacing.lg,
        marginTop: 0,
        padding: spacing.xl,
        borderRadius: borderRadius.xl,
        alignItems: 'center',
        borderWidth: 2,
        borderColor: colors.accent,
    },
    pointsValue: {
        fontSize: 48,
        fontWeight: 'bold',
        color: colors.accent,
        marginTop: spacing.md,
    },
    pointsLabel: {
        fontSize: fontSize.lg,
        color: colors.textSecondary,
        marginTop: spacing.xs,
    },
    section: {
        padding: spacing.lg,
        paddingTop: 0,
    },
    sectionTitle: {
        fontSize: fontSize.lg,
        fontWeight: '600',
        color: colors.text,
        marginBottom: spacing.md,
    },
    infoCard: {
        backgroundColor: colors.surface,
        borderRadius: borderRadius.lg,
        padding: spacing.md,
        marginBottom: spacing.sm,
    },
    infoRow: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    infoContent: {
        marginLeft: spacing.md,
        flex: 1,
    },
    infoTitle: {
        fontSize: fontSize.md,
        color: colors.text,
    },
    infoValue: {
        fontSize: fontSize.lg,
        fontWeight: '600',
        color: colors.accent,
    },
    summaryCard: {
        backgroundColor: colors.surface,
        borderRadius: borderRadius.lg,
        padding: spacing.lg,
    },
    summaryRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        marginBottom: spacing.sm,
    },
    summaryLabel: {
        fontSize: fontSize.md,
        color: colors.textSecondary,
    },
    summaryValue: {
        fontSize: fontSize.md,
        color: colors.text,
        fontWeight: '500',
    },
    summaryValueAccent: {
        fontSize: fontSize.md,
        color: colors.accent,
        fontWeight: '600',
    },
    divider: {
        height: 1,
        backgroundColor: colors.border,
        marginVertical: spacing.md,
    },
    emptyContainer: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        padding: spacing.xl,
        backgroundColor: colors.background,
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
        textAlign: 'center',
        marginTop: spacing.sm,
    },
});

export default PointsScreen;
