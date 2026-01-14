import React, { useState, useEffect, useMemo, useRef } from 'react';
import {
    View,
    Text,
    StyleSheet,
    Dimensions,
    TouchableOpacity,
    ScrollView,
    Animated,
    RefreshControl,
    Alert
} from 'react-native';
import LinearGradient from 'react-native-linear-gradient';
import Icon from 'react-native-vector-icons/Ionicons';
import { useNavigation, useIsFocused } from '@react-navigation/native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';
import { BASE_URL } from '../../config/app';

const { width: SCREEN_WIDTH } = Dimensions.get('window');

// --- COLORS ---
const COLORS = {
    maroon: '#8B1538',
    maroonDark: '#6B0F2A',
    maroonLight: '#4A0A1C',
    gold: '#D4AF37',
    goldLight: '#FCD34D',
    gray: '#9CA3AF',
    white: '#FFFFFF',
    bronze: '#CD7F32',
    silver: '#C0C0C0',
    platinum: '#E5E4E2',
    diamond: '#B9F2FF',
};

// --- HELPER FUNCTIONS ---
const getUserLevel = (points) => {
    if (points >= 100000) return { level: 'Diamond', color: COLORS.diamond, next: 'Max Level', progress: 100 };
    if (points >= 50000) return { level: 'Platinum', color: COLORS.platinum, next: 100000, progress: ((points - 50000) / 50000) * 100 };
    if (points >= 25000) return { level: 'Gold', color: COLORS.gold, next: 50000, progress: ((points - 25000) / 25000) * 100 };
    if (points >= 10000) return { level: 'Silver', color: COLORS.silver, next: 25000, progress: ((points - 10000) / 15000) * 100 };
    return { level: 'Bronze', color: COLORS.bronze, next: 10000, progress: (points / 10000) * 100 };
};

const REWARDS = [
    { id: 1, name: 'Diskon 10%', points: 5000, icon: 'ticket-outline', description: 'Diskon 10% untuk pesanan berikutnya' },
    { id: 2, name: 'Gratis Minuman', points: 3000, icon: 'wine-outline', description: 'Minuman gratis pilihan' },
    { id: 3, name: 'Gratis Dessert', points: 4000, icon: 'ice-cream-outline', description: 'Dessert gratis pilihan' },
    { id: 4, name: 'Diskon 25%', points: 15000, icon: 'pricetag-outline', description: 'Diskon 25% untuk pesanan berikutnya' },
    { id: 5, name: 'Voucher Rp 100K', points: 25000, icon: 'card-outline', description: 'Voucher senilai Rp 100.000' },
    { id: 6, name: 'Priority Seating', points: 20000, icon: 'star-outline', description: 'Prioritas pemilihan meja selama 1 bulan' },
];

const PointsScreen = () => {
    const navigation = useNavigation();
    const isFocused = useIsFocused();
    const scrollY = useRef(new Animated.Value(0)).current;

    // State
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [points, setPoints] = useState(0);
    const [transactions, setTransactions] = useState([]);
    const [selectedTab, setSelectedTab] = useState('overview'); // 'overview', 'history', 'rewards'

    // Derived State
    const userLevel = useMemo(() => getUserLevel(points), [points]);

    const fetchPointsData = async () => {
        try {
            const token = await AsyncStorage.getItem('userToken');
            const response = await axios.get(`${BASE_URL}/points`, {
                headers: { Authorization: `Bearer ${token}` }
            });

            if (response.data.success) {
                setPoints(response.data.points);
                setFrames(response.data.transactions.data); // Pagination data
            }
        } catch (error) {
            console.error('Fetch points error:', error);
            // Fallback for demo if backend not ready yet
            // setPoints(0);
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };

    // Helper to safely set transactions since pagination structure might differ
    const setFrames = (data) => {
        if (Array.isArray(data)) {
            setTransactions(data);
        } else {
            setTransactions([]);
        }
    };

    useEffect(() => {
        if (isFocused) {
            fetchPointsData();
        }
    }, [isFocused]);

    const onRefresh = () => {
        setRefreshing(true);
        fetchPointsData();
    };

    // --- RENDERERS ---

    const renderHeader = () => {
        const headerOpacity = scrollY.interpolate({
            inputRange: [0, 100],
            outputRange: [0, 1],
            extrapolate: 'clamp',
        });

        return (
            <Animated.View style={[styles.header, { opacity: headerOpacity }]}>
                <LinearGradient
                    colors={[COLORS.maroonDark, COLORS.maroon]}
                    style={styles.headerGradient}
                    start={{ x: 0, y: 0 }}
                    end={{ x: 1, y: 0 }}
                >
                    <Text style={styles.headerTitle}>My Points</Text>
                </LinearGradient>
            </Animated.View>
        );
    };

    const renderPointsCard = () => (
        <View style={styles.cardContainer}>
            <LinearGradient
                colors={['rgba(255,255,255,0.15)', 'rgba(255,255,255,0.05)']}
                style={styles.glassCard}
                start={{ x: 0, y: 0 }}
                end={{ x: 1, y: 1 }}
            >
                {/* Level Badge */}
                <View style={styles.levelBadge}>
                    <Icon name="ribbon-outline" size={20} color={userLevel.color} />
                    <Text style={[styles.levelText, { color: userLevel.color }]}>
                        {userLevel.level} Member
                    </Text>
                </View>

                {/* Points Display */}
                <View style={styles.pointsDisplay}>
                    <Text style={styles.pointsLabel}>Total Poin</Text>
                    <Text style={styles.pointsValue}>
                        {points.toLocaleString('id-ID')}
                    </Text>
                    <Text style={styles.pointsSubLabel}>Points Available</Text>
                </View>

                {/* Progress Bar */}
                <View style={styles.progressContainer}>
                    <View style={styles.progressHeader}>
                        <Text style={styles.progressLabel}>
                            Next: {typeof userLevel.next === 'number' ? userLevel.next.toLocaleString() : userLevel.next}
                        </Text>
                        <Text style={styles.progressPercentage}>
                            {Math.min(100, Math.round(userLevel.progress))}%
                        </Text>
                    </View>
                    <View style={styles.progressBarBg}>
                        <View
                            style={[
                                styles.progressBarFill,
                                {
                                    width: `${Math.min(100, userLevel.progress)}%`,
                                    backgroundColor: userLevel.color
                                }
                            ]}
                        />
                    </View>
                </View>
            </LinearGradient>
        </View>
    );

    const renderTabs = () => (
        <View style={styles.tabsContainer}>
            {['Overview', 'History', 'Rewards'].map((tab) => {
                const isActive = selectedTab === tab.toLowerCase();
                return (
                    <TouchableOpacity
                        key={tab}
                        style={[styles.tabButton, isActive && styles.activeTabButton]}
                        onPress={() => setSelectedTab(tab.toLowerCase())}
                    >
                        <Text style={[styles.tabText, isActive && styles.activeTabText]}>
                            {tab}
                        </Text>
                    </TouchableOpacity>
                );
            })}
        </View>
    );

    const renderOverview = () => (
        <View style={styles.sectionContainer}>
            <View style={styles.infoCard}>
                <View style={styles.infoHeader}>
                    <Icon name="trending-up-outline" size={24} color={COLORS.gold} />
                    <Text style={styles.sectionTitle}>Cara Mendapatkan Poin</Text>
                </View>

                <View style={styles.earnMethodItem}>
                    <View style={[styles.iconBox, { backgroundColor: 'rgba(16, 185, 129, 0.1)' }]}>
                        <Icon name="bag-handle-outline" size={24} color="#10B981" />
                    </View>
                    <View style={styles.earnMethodText}>
                        <Text style={styles.methodTitle}>Pesan Menu</Text>
                        <Text style={styles.methodSubtitle}>Sekali checkout</Text>
                    </View>
                    <View style={styles.earnPoints}>
                        <Text style={[styles.pointsEarned, { color: '#10B981' }]}>+1.000</Text>
                    </View>
                </View>

                <View style={[styles.earnMethodItem, { marginTop: 12 }]}>
                    <View style={[styles.iconBox, { backgroundColor: 'rgba(59, 130, 246, 0.1)' }]}>
                        <Icon name="calendar-outline" size={24} color="#3B82F6" />
                    </View>
                    <View style={styles.earnMethodText}>
                        <Text style={styles.methodTitle}>Reservasi Meja</Text>
                        <Text style={styles.methodSubtitle}>Sekali reservasi</Text>
                    </View>
                    <View style={styles.earnPoints}>
                        <Text style={[styles.pointsEarned, { color: '#3B82F6' }]}>+10.000</Text>
                    </View>
                </View>
            </View>

            <View style={[styles.infoCard, { marginTop: 20 }]}>
                <View style={styles.infoHeader}>
                    <Icon name="star-outline" size={24} color={COLORS.gold} />
                    <Text style={styles.sectionTitle}>Keuntungan Member</Text>
                </View>
                <View style={styles.benefitItem}>
                    <Icon name="checkmark-circle" size={16} color={COLORS.gold} />
                    <Text style={styles.benefitText}>Tukar poin dengan diskon & voucher</Text>
                </View>
                <View style={styles.benefitItem}>
                    <Icon name="checkmark-circle" size={16} color={COLORS.gold} />
                    <Text style={styles.benefitText}>Akses ke menu eksklusif</Text>
                </View>
                <View style={styles.benefitItem}>
                    <Icon name="checkmark-circle" size={16} color={COLORS.gold} />
                    <Text style={styles.benefitText}>Prioritas reservasi (Platinum+)</Text>
                </View>
            </View>
        </View>
    );

    const renderHistory = () => (
        <View style={styles.sectionContainer}>
            {loading ? (
                <Text style={styles.loadingText}>Memuat riwayat...</Text>
            ) : transactions.length === 0 ? (
                <View style={styles.emptyState}>
                    <Text style={styles.emptyEmoji}>🏆</Text>
                    <Text style={styles.emptyTitle}>Belum ada riwayat</Text>
                    <Text style={styles.emptySubtitle}>Mulai pesan untuk dapatkan poin!</Text>
                </View>
            ) : (
                transactions.map((item) => (
                    <View key={item.id} style={styles.historyItem}>
                        <View style={[
                            styles.iconBox,
                            { backgroundColor: item.type === 'order' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(59, 130, 246, 0.1)' }
                        ]}>
                            <Icon
                                name={item.type === 'order' ? 'bag-handle-outline' : 'calendar-outline'}
                                size={24}
                                color={item.type === 'order' ? '#10B981' : '#3B82F6'}
                            />
                        </View>
                        <View style={styles.historyContent}>
                            <Text style={styles.historyTitle}>{item.description}</Text>
                            <Text style={styles.historyDate}>
                                {new Date(item.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })}
                            </Text>
                        </View>
                        <Text style={[styles.historyPoints, { color: item.points > 0 ? '#10B981' : COLORS.maroon }]}>
                            {item.points > 0 ? '+' : ''}{item.points}
                        </Text>
                    </View>
                ))
            )}
        </View>
    );

    const renderRewards = () => (
        <View style={styles.sectionContainer}>
            {REWARDS.map((reward) => {
                const canRedeem = points >= reward.points;
                return (
                    <View key={reward.id} style={[styles.rewardCard, !canRedeem && styles.rewardDisabled]}>
                        <View style={styles.rewardHeader}>
                            <Icon name={reward.icon} size={32} color={COLORS.maroon} />
                            <View style={styles.rewardInfo}>
                                <Text style={styles.rewardName}>{reward.name}</Text>
                                <Text style={styles.rewardDesc}>{reward.description}</Text>
                            </View>
                        </View>
                        <View style={styles.rewardFooter}>
                            <View style={styles.pointsTag}>
                                <Icon name="gift-outline" size={14} color={COLORS.gold} />
                                <Text style={styles.pointsTagText}>{reward.points.toLocaleString()} Poin</Text>
                            </View>
                            {canRedeem ? (
                                <TouchableOpacity
                                    style={styles.redeemButton}
                                    onPress={() => Alert.alert('Coming Soon', 'Fitur redeem akan segera hadir!')}
                                >
                                    <Text style={styles.redeemText}>Tukar</Text>
                                    <Icon name="chevron-forward" size={14} color="white" />
                                </TouchableOpacity>
                            ) : (
                                <Text style={styles.shortfallText}>
                                    Kurang {(reward.points - points).toLocaleString()} poin
                                </Text>
                            )}
                        </View>
                    </View>
                );
            })}
        </View>
    );

    return (
        <View style={styles.container}>
            {renderHeader()}
            <ScrollView
                showsVerticalScrollIndicator={false}
                contentContainerStyle={styles.scrollContent}
                refreshControl={
                    <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor="white" />
                }
                onScroll={Animated.event(
                    [{ nativeEvent: { contentOffset: { y: scrollY } } }],
                    { useNativeDriver: false }
                )}
                scrollEventThrottle={16}
            >
                {/* Background Gradient */}
                <LinearGradient
                    colors={[COLORS.maroonDark, COLORS.maroon, COLORS.maroonLight]}
                    style={styles.background}
                />

                {/* Main Content */}
                <View style={styles.content}>
                    <View style={styles.headerSpacer} />
                    <View style={styles.titleRow}>
                        <View style={styles.iconContainer}>
                            <Icon name="trophy-outline" size={24} color={COLORS.gold} />
                        </View>
                        <View>
                            <Text style={styles.screenTitle}>My Points</Text>
                            <Text style={styles.screenSubtitle}>Rewards & Benefits</Text>
                        </View>
                    </View>

                    {renderPointsCard()}
                    {renderTabs()}

                    {selectedTab === 'overview' && renderOverview()}
                    {selectedTab === 'history' && renderHistory()}
                    {selectedTab === 'rewards' && renderRewards()}
                </View>
            </ScrollView>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: COLORS.maroonDark,
    },
    background: {
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        height: 400,
    },
    scrollContent: {
        paddingBottom: 100,
    },
    header: {
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        height: 80,
        zIndex: 100,
    },
    headerGradient: {
        flex: 1,
        justifyContent: 'flex-end',
        paddingBottom: 15,
        paddingHorizontal: 20,
    },
    headerTitle: {
        color: 'white',
        fontSize: 18,
        fontWeight: 'bold',
        textAlign: 'center',
    },
    content: {
        paddingHorizontal: 20,
    },
    headerSpacer: {
        height: 60,
    },
    titleRow: {
        flexDirection: 'row',
        alignItems: 'center',
        marginBottom: 24,
        marginTop: 20,
    },
    iconContainer: {
        width: 48,
        height: 48,
        borderRadius: 16,
        backgroundColor: 'rgba(255,255,255,0.1)',
        alignItems: 'center',
        justifyContent: 'center',
        marginRight: 12,
    },
    screenTitle: {
        fontSize: 28,
        fontWeight: 'bold',
        color: 'white',
        fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
    },
    screenSubtitle: {
        color: 'rgba(255,255,255,0.8)',
        fontSize: 14,
    },
    cardContainer: {
        marginBottom: 24,
        borderRadius: 24,
        overflow: 'hidden',
        borderWidth: 1,
        borderColor: 'rgba(255,255,255,0.2)',
    },
    glassCard: {
        padding: 24,
    },
    levelBadge: {
        flexDirection: 'row',
        alignItems: 'center',
        marginBottom: 16,
    },
    levelText: {
        fontWeight: 'bold',
        textTransform: 'uppercase',
        letterSpacing: 1,
        marginLeft: 8,
        fontSize: 14,
    },
    pointsDisplay: {
        marginBottom: 20,
    },
    pointsLabel: {
        color: 'rgba(255,255,255,0.6)',
        fontSize: 14,
        marginBottom: 4,
    },
    pointsValue: {
        fontSize: 48,
        fontWeight: 'bold',
        color: COLORS.gold, // Gradient text fallback
        letterSpacing: -1,
    },
    pointsSubLabel: {
        color: 'rgba(255,255,255,0.4)',
        fontSize: 12,
    },
    progressContainer: {
        marginTop: 4,
    },
    progressHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        marginBottom: 8,
    },
    progressLabel: {
        color: 'rgba(255,255,255,0.6)',
        fontSize: 12,
    },
    progressPercentage: {
        color: 'rgba(255,255,255,0.9)',
        fontWeight: 'bold',
        fontSize: 12,
    },
    progressBarBg: {
        height: 12,
        backgroundColor: 'rgba(255,255,255,0.1)',
        borderRadius: 6,
        overflow: 'hidden',
    },
    progressBarFill: {
        height: '100%',
        borderRadius: 6,
    },
    tabsContainer: {
        flexDirection: 'row',
        backgroundColor: 'white',
        borderRadius: 16,
        padding: 4,
        marginBottom: 24,
    },
    tabButton: {
        flex: 1,
        paddingVertical: 12,
        alignItems: 'center',
        borderRadius: 12,
    },
    activeTabButton: {
        backgroundColor: COLORS.maroon,
    },
    tabText: {
        fontSize: 14,
        fontWeight: '600',
        color: COLORS.gray,
    },
    activeTabText: {
        color: 'white',
    },
    sectionContainer: {
        paddingBottom: 20,
    },
    infoCard: {
        backgroundColor: 'white',
        borderRadius: 16,
        padding: 20,
        elevation: 2,
    },
    infoHeader: {
        flexDirection: 'row',
        alignItems: 'center',
        marginBottom: 16,
    },
    sectionTitle: {
        fontSize: 18,
        fontWeight: 'bold',
        color: '#1F2937',
        marginLeft: 8,
    },
    earnMethodItem: {
        flexDirection: 'row',
        alignItems: 'center',
        padding: 12,
        borderRadius: 12,
        borderWidth: 1,
        borderColor: 'rgba(0,0,0,0.05)',
    },
    iconBox: {
        width: 48,
        height: 48,
        borderRadius: 12,
        alignItems: 'center',
        justifyContent: 'center',
    },
    earnMethodText: {
        flex: 1,
        marginLeft: 12,
    },
    methodTitle: {
        fontSize: 16,
        fontWeight: '600',
        color: '#1F2937',
    },
    methodSubtitle: {
        fontSize: 12,
        color: '#6B7280',
    },
    earnPoints: {
        alignItems: 'flex-end',
    },
    pointsEarned: {
        fontSize: 16,
        fontWeight: 'bold',
    },
    benefitItem: {
        flexDirection: 'row',
        marginBottom: 12,
    },
    benefitText: {
        marginLeft: 12,
        color: '#4B5563',
        fontSize: 14,
    },
    emptyState: {
        alignItems: 'center',
        paddingVertical: 40,
    },
    emptyEmoji: {
        fontSize: 64,
        marginBottom: 16,
    },
    emptyTitle: {
        fontSize: 18,
        fontWeight: 'bold',
        color: 'white',
    },
    emptySubtitle: {
        color: 'rgba(255,255,255,0.6)',
        marginTop: 4,
    },
    historyItem: {
        backgroundColor: 'white',
        borderRadius: 16,
        padding: 16,
        marginBottom: 12,
        flexDirection: 'row',
        alignItems: 'center',
    },
    historyContent: {
        flex: 1,
        marginLeft: 12,
    },
    historyTitle: {
        fontSize: 16,
        fontWeight: '600',
        color: '#1F2937',
    },
    historyDate: {
        fontSize: 12,
        color: '#6B7280',
    },
    historyPoints: {
        fontSize: 16,
        fontWeight: 'bold',
    },
    loadingText: {
        color: 'white',
        textAlign: 'center',
        marginTop: 20,
    },
    rewardCard: {
        backgroundColor: 'white',
        borderRadius: 16,
        padding: 20,
        marginBottom: 16,
        elevation: 2,
    },
    rewardDisabled: {
        opacity: 0.6,
    },
    rewardHeader: {
        flexDirection: 'row',
        marginBottom: 12,
    },
    rewardInfo: {
        flex: 1,
        marginLeft: 16,
    },
    rewardName: {
        fontSize: 18,
        fontWeight: 'bold',
        color: '#1F2937',
    },
    rewardDesc: {
        fontSize: 14,
        color: '#6B7280',
        marginTop: 4,
    },
    rewardFooter: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginTop: 8,
        paddingTop: 12,
        borderTopWidth: 1,
        borderTopColor: '#F3F4F6',
    },
    pointsTag: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    pointsTagText: {
        fontSize: 14,
        fontWeight: 'bold',
        color: COLORS.gold,
        marginLeft: 4,
    },
    redeemButton: {
        backgroundColor: COLORS.maroon,
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: 16,
        paddingVertical: 8,
        borderRadius: 8,
    },
    redeemText: {
        color: 'white',
        fontWeight: '600',
        fontSize: 14,
        marginRight: 4,
    },
    shortfallText: {
        fontSize: 12,
        color: '#9CA3AF',
        fontStyle: 'italic',
    },
});

export default PointsScreen;
