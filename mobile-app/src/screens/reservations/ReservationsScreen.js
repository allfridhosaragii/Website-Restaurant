import React, { useState, useEffect, useMemo, useCallback } from 'react';
import {
    View,
    Text,
    StyleSheet,
    Dimensions,
    TouchableOpacity,
    ScrollView,
    TextInput,
    Modal,
    Platform,
    StatusBar,
    Alert,
    ActivityIndicator
} from 'react-native';
import Animated, {
    useSharedValue,
    useAnimatedStyle,
    withSpring,
    FadeIn,
    SlideInDown,
    SlideOutDown,
    ZoomIn
} from 'react-native-reanimated';
import LinearGradient from 'react-native-linear-gradient';
import Icon from 'react-native-vector-icons/Ionicons';
import { useNavigation } from '@react-navigation/native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const { width: SCREEN_WIDTH } = Dimensions.get('window');
const BASE_URL = 'https://website-restaurant.up.railway.app/api';

// --- COLORS ---
const COLORS = {
    maroon: '#8B1538',
    maroonDark: '#6B0F2A',
    maroonDarker: '#4A0A1C',
    gold: '#D97706',
    goldLight: '#FCD34D',
    grayLight: '#E5E7EB',
    grayDark: '#374151',
    blue: '#2563EB',
    blueLight: '#60A5FA',
    green: '#10B981',
    red: '#EF4444',
    white: '#FFFFFF',
    cream: '#FAF9F6' // Premium background
};

// --- Helper: Map API table to visual position ---
const mapTableToPosition = (table) => {
    // Map database tables to visual grid positions
    const positions = {
        // VIP Zone (1-5)
        1: { row: 0, col: 1 }, 2: { row: 0, col: 4 },
        3: { row: 1, col: 2 }, 4: { row: 1, col: 3 }, 5: { row: 2, col: 2 },
        // Main Zone (6-21)
        6: { row: 3, col: 0 }, 7: { row: 3, col: 1 }, 8: { row: 3, col: 4 }, 9: { row: 3, col: 5 },
        10: { row: 4, col: 0 }, 11: { row: 4, col: 1 }, 12: { row: 4, col: 4 }, 13: { row: 4, col: 5 },
        14: { row: 5, col: 0 }, 15: { row: 5, col: 1 }, 16: { row: 5, col: 4 }, 17: { row: 5, col: 5 },
        18: { row: 6, col: 0 }, 19: { row: 6, col: 1 }, 20: { row: 6, col: 4 }, 21: { row: 6, col: 5 },
        // Window Zone (22-27)
        22: { row: 7, col: 0 }, 23: { row: 7, col: 5 },
        24: { row: 8, col: 1 }, 25: { row: 8, col: 2 }, 26: { row: 8, col: 3 }, 27: { row: 8, col: 4 },
    };
    const pos = positions[table.number] || { row: 0, col: 0 };
    return {
        ...table,
        row: pos.row,
        col: pos.col,
        isPremium: table.is_premium,
    };
};

// --- COMPONENTS ---

const GRID_SIZE = (SCREEN_WIDTH - 32) / 6; // Responsive Grid

const Chair = ({ position, color }) => {
    // Round Premium Chairs
    const getStyle = () => {
        const offset = -12;
        switch (position) {
            case 'top': return { top: offset, left: '50%', marginLeft: -8 };
            case 'bottom': return { bottom: offset, left: '50%', marginLeft: -8 };
            case 'left': return { left: offset, top: '50%', marginTop: -8 };
            case 'right': return { right: offset, top: '50%', marginTop: -8 };
            default: return {};
        }
    };

    return (
        <View style={[
            {
                position: 'absolute',
                backgroundColor: color,
                width: 16, height: 16, borderRadius: 8,
                shadowColor: "#000", shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.2, shadowRadius: 1, elevation: 2
            },
            getStyle()
        ]} />
    );
};

const RestaurantTable = ({ table, isSelected, onSelect }) => {
    // Visual Logic
    const isDisabled = table.status === 'booked';

    // Premium Glass Colors
    let bgColor = 'rgba(255,255,255,0.9)';
    let borderColor = 'rgba(255,255,255,1)';
    let chairColor = '#D1D5DB';

    if (isDisabled) {
        bgColor = 'rgba(239, 68, 68, 0.1)'; // Red Tint
        borderColor = 'rgba(239, 68, 68, 0.3)';
        chairColor = '#FECACA';
    } else if (isSelected) {
        bgColor = COLORS.blue;
        borderColor = '#60A5FA';
        chairColor = COLORS.blue;
    } else if (table.isPremium) {
        bgColor = 'rgba(255, 251, 235, 0.95)'; // Amber Tint
        borderColor = COLORS.gold; // Gold Border
        chairColor = '#FDE68A';
    }

    // Shape & Size Responsive
    const getSize = () => {
        const base = GRID_SIZE - 18; // Padding
        if (table.shape === 'round') return { width: base, height: base, borderRadius: base / 2 };
        if (table.shape === 'square') return { width: base, height: base, borderRadius: 12 };
        if (table.shape === 'rectangle') return { width: base * 1.5, height: base, borderRadius: 12 };
        return { width: base, height: base };
    };

    const sizeStyle = getSize();

    // Chair Logic
    const renderChairs = () => {
        // Simplified visually pleasing layout
        return ['top', 'bottom', 'left', 'right'].slice(0, table.capacity <= 2 ? 2 : 4).map(pos => (
            <Chair key={pos} position={pos} color={chairColor} />
        ));
    };

    // Animation
    const scale = useSharedValue(1);
    useEffect(() => { scale.value = withSpring(isSelected ? 1.1 : 1); }, [isSelected]);
    const animatedStyle = useAnimatedStyle(() => ({ transform: [{ scale: scale.value }], zIndex: isSelected ? 20 : 10 }));

    return (
        <TouchableOpacity
            activeOpacity={0.8}
            onPress={() => !isDisabled && onSelect(table.id)}
            disabled={isDisabled}
            style={{
                position: 'absolute',
                left: table.col * GRID_SIZE + 16,
                top: table.row * GRID_SIZE + 20,
                alignItems: 'center', justifyContent: 'center',
                zIndex: isSelected ? 100 : 10
            }}
        >
            <Animated.View style={[animatedStyle, { alignItems: 'center', justifyContent: 'center' }]}>
                {/* Chairs Container */}
                <View style={[sizeStyle, { position: 'absolute', backgroundColor: 'transparent' }]}>
                    {renderChairs()}
                </View>

                {/* Table Surface */}
                <View style={[
                    sizeStyle,
                    {
                        backgroundColor: isSelected ? COLORS.blue : (table.isPremium ? '#FFFBEB' : '#FFFFFF'),
                        borderWidth: 1, borderColor: borderColor,
                        alignItems: 'center', justifyContent: 'center',
                        shadowColor: "#000", shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.1, shadowRadius: 8, elevation: 4
                    }
                ]}>
                    {/* Inner Glass Shine */}
                    {!isSelected && !isDisabled && (
                        <View style={{ position: 'absolute', top: 2, left: 2, right: 2, height: '40%', backgroundColor: 'rgba(255,255,255,0.6)', borderTopLeftRadius: 10, borderTopRightRadius: 10 }} />
                    )}

                    {table.isPremium && !isSelected && !isDisabled && (
                        <Icon name="star" size={10} color={COLORS.gold} style={{ position: 'absolute', top: -4, right: -4 }} />
                    )}

                    {isSelected ? (
                        <Icon name="checkmark" size={20} color="white" />
                    ) : (
                        !isDisabled && <Text style={{ fontSize: 10, fontWeight: 'bold', color: table.isPremium ? COLORS.gold : COLORS.grayDark }}>{table.number}</Text>
                    )}
                    {isDisabled && <Icon name="close" size={16} color={COLORS.red} opacity={0.6} />}
                </View>
            </Animated.View>
        </TouchableOpacity>
    );
};

const ReservationsScreen = () => {
    const navigation = useNavigation();

    // State
    const [step, setStep] = useState(1);
    const [tables, setTables] = useState([]);
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const [selectedTableId, setSelectedTableId] = useState(null);
    const [selectedDate, setSelectedDate] = useState(new Date().toISOString().split('T')[0]); // YYYY-MM-DD
    const [formData, setFormData] = useState({ name: '', phone: '', email: '', date: '', time: '' });
    const [showReceipt, setShowReceipt] = useState(false);
    const [receiptData, setReceiptData] = useState(null);

    // Fetch tables from API
    const fetchTables = useCallback(async (date) => {
        try {
            setLoading(true);
            const token = await AsyncStorage.getItem('userToken');
            const response = await axios.get(`${BASE_URL}/tables`, {
                params: { date },
                headers: { Authorization: `Bearer ${token}` }
            });
            if (response.data.success) {
                const mappedTables = response.data.tables.map(mapTableToPosition);
                setTables(mappedTables);
            }
        } catch (error) {
            console.log('Error fetching tables:', error);
            Alert.alert('Error', 'Gagal memuat data meja');
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchTables(selectedDate);
    }, [selectedDate, fetchTables]);

    // Derived
    const selectedTable = useMemo(() => tables.find(t => t.id === selectedTableId), [selectedTableId, tables]);
    const stats = useMemo(() => {
        return {
            available: tables.filter(t => t.status === 'available').length,
            booked: tables.filter(t => t.status === 'booked').length,
            vip: tables.filter(t => t.isPremium && t.status === 'available').length,
            capacity: tables.filter(t => t.status === 'available').reduce((acc, t) => acc + t.capacity, 0)
        };
    }, [tables]);

    const handleNext = () => {
        if (step === 1 && selectedTable) {
            // Pre-fill date with selectedDate
            setFormData(prev => ({ ...prev, date: selectedDate }));
            setStep(2);
        }
        else if (step === 2) handleSubmit();
    };

    const handleSubmit = async () => {
        // Validation simple
        if (!formData.name || !formData.phone || !formData.date || !formData.time) {
            Alert.alert("Mohon Lengkapi Data", "Semua kolom form harus diisi.");
            return;
        }

        try {
            setSubmitting(true);
            const token = await AsyncStorage.getItem('userToken');
            const response = await axios.post(`${BASE_URL}/reservations`, {
                date: formData.date,
                time: formData.time,
                guests: selectedTable.capacity,
                name: formData.name,
                phone: formData.phone,
                table_id: selectedTable.id,
                notes: formData.email || null,
            }, {
                headers: { Authorization: `Bearer ${token}` }
            });

            if (response.data.success) {
                const receipt = {
                    id: `RSV-${response.data.reservation.id}`,
                    table: selectedTable.number,
                    guests: selectedTable.capacity,
                    date: formData.date,
                    time: formData.time,
                    name: formData.name
                };
                setReceiptData(receipt);
                setShowReceipt(true);
            }
        } catch (error) {
            console.log('Reservation error:', error.response?.data || error.message);
            const msg = error.response?.data?.message || 'Gagal membuat reservasi';
            Alert.alert('Error', msg);
        } finally {
            setSubmitting(false);
        }
    };

    const closeReceipt = () => {
        setShowReceipt(false);
        setStep(1);
        setSelectedTableId(null);
        setFormData({ name: '', phone: '', email: '', date: '', time: '' });
        // Refresh tables to show updated availability
        fetchTables(selectedDate);
    };

    // --- RENDERERS ---

    const renderHeader = () => (
        <LinearGradient
            colors={[COLORS.maroon, COLORS.maroonDark, COLORS.maroonDarker]}
            start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}
            style={styles.header}
        >
            <View style={styles.headerDecorCircle1} />
            <View style={styles.headerDecorCircle2} />

            <View style={styles.headerContent}>
                <View style={styles.titleRow}>
                    <View style={styles.iconBox}>
                        <Icon name="calendar" size={24} color="white" />
                    </View>
                    <View>
                        <Text style={styles.headerTitle}>Reservasi Meja</Text>
                        <Text style={styles.headerSubtitle}>Pesan tempat spesial Anda</Text>
                    </View>
                </View>

                {/* Progress */}
                <View style={styles.progressContainer}>
                    <View style={styles.progressItem}>
                        <View style={[styles.progressBar, { backgroundColor: COLORS.white }]} />
                        <Text style={[styles.progressLabel, { color: COLORS.white }]}>1. Pilih Meja</Text>
                    </View>
                    <View style={styles.progressItem}>
                        <View style={[styles.progressBar, { backgroundColor: step === 2 ? COLORS.white : 'rgba(255,255,255,0.3)' }]} />
                        <Text style={[styles.progressLabel, { opacity: step === 2 ? 1 : 0.7 }]}>2. Isi Data</Text>
                    </View>
                </View>
            </View>
        </LinearGradient>
    );

    const renderStats = () => (
        <View style={styles.statsGrid}>
            {[
                { label: 'Tersedia', val: stats.available, color: COLORS.green, bg: '#ECFDF5', border: '#A7F3D0' },
                { label: 'Terboking', val: stats.booked, color: COLORS.red, bg: '#FEF2F2', border: '#FECACA' },
                { label: 'VIP', val: stats.vip, color: COLORS.gold, bg: '#FFFBEB', border: '#FDE68A' },
                { label: 'Kapasitas', val: stats.capacity, color: COLORS.blue, bg: '#EFF6FF', border: '#BFDBFE' },
            ].map((stat, i) => (
                <View key={i} style={[styles.statCard, { backgroundColor: stat.bg, borderColor: stat.border }]}>
                    <Text style={[styles.statVal, { color: stat.color }]}>{stat.val}</Text>
                    <Text style={[styles.statLabel, { color: stat.color }]}>{stat.label}</Text>
                </View>
            ))}
        </View>
    );

    const renderMap = () => (
        <View style={{ marginTop: 16 }}>
            {/* Legend */}
            <View style={styles.legendContainer}>
                {[
                    { label: 'Available', color: '#FFF', border: '#E5E7EB' },
                    { label: 'Selected', color: COLORS.blue, border: COLORS.blue },
                    { label: 'Booked', color: '#FEE2E2', border: '#FCA5A5' },
                    { label: 'Premium', color: '#FFFBEB', border: COLORS.gold }
                ].map((l, i) => (
                    <View key={i} style={styles.legendItem}>
                        <View style={[styles.legendDot, { backgroundColor: l.color, borderColor: l.border, borderWidth: 1 }]} />
                        <Text style={styles.legendText}>{l.label}</Text>
                    </View>
                ))}
            </View>

            {/* Vertical Scroll Map container */}
            <View style={styles.mapContainerFrame}>
                {/* Floor Texture Background */}
                <View style={styles.floorPlank} />
                <View style={[styles.floorPlank, { top: 100 }]} />
                <View style={[styles.floorPlank, { top: 200 }]} />
                <View style={[styles.floorPlank, { top: 300 }]} />
                <View style={[styles.floorPlank, { top: 400 }]} />
                <View style={[styles.floorPlank, { top: 500 }]} />

                {/* Tables Layer */}
                <View style={{ height: 650, width: '100%', position: 'relative' }}>
                    {tables.map(table => (
                        <RestaurantTable
                            key={table.id}
                            table={table}
                            isSelected={selectedTableId === table.id}
                            onSelect={setSelectedTableId}
                        />
                    ))}
                </View>
            </View>

            {/* Selected Info Card */}
            {selectedTable && (
                <Animated.View entering={SlideInDown} exiting={SlideOutDown} style={styles.selectionCard}>
                    <LinearGradient
                        colors={['#EFF6FF', '#DBEAFE']}
                        start={{ x: 0, y: 0 }} end={{ x: 1, y: 0 }}
                        style={styles.selectionGradient}
                    >
                        <View style={styles.selectionIconBox}>
                            <Icon name="restaurant" size={20} color={COLORS.blue} />
                        </View>
                        <View style={{ flex: 1 }}>
                            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 6 }}>
                                <Text style={styles.selectionTitle}>Meja Terpilih</Text>
                                {selectedTable.isPremium && <Icon name="star" size={14} color={COLORS.gold} />}
                            </View>
                            <Text style={styles.selectionDetail}>Meja {selectedTable.number} • Kapasitas {selectedTable.capacity} Orang</Text>
                        </View>
                        <TouchableOpacity style={styles.nextBtnSmall} onPress={handleNext}>
                            <Text style={styles.nextBtnText}>Lanjut</Text>
                            <Icon name="arrow-forward" size={16} color="white" />
                        </TouchableOpacity>
                    </LinearGradient>
                </Animated.View>
            )}
        </View>
    );

    const renderForm = () => (
        <Animated.View entering={FadeIn} style={styles.formContainer}>
            <Text style={styles.formTitle}>Lengkapi Data Reservasi</Text>

            <View style={styles.inputGroup}>
                <Text style={styles.inputLabel}>Nama Lengkap</Text>
                <View style={styles.inputWrapper}>
                    <Icon name="person-outline" size={20} color="#9CA3AF" style={styles.inputIcon} />
                    <TextInput
                        style={styles.input}
                        placeholder="Nama Pemesan"
                        value={formData.name}
                        onChangeText={t => setFormData({ ...formData, name: t })}
                    />
                </View>
            </View>

            <View style={styles.inputGroup}>
                <Text style={styles.inputLabel}>Nomor Telepon</Text>
                <View style={styles.inputWrapper}>
                    <Icon name="call-outline" size={20} color="#9CA3AF" style={styles.inputIcon} />
                    <TextInput
                        style={styles.input}
                        placeholder="08xx-xxxx-xxxx"
                        keyboardType="phone-pad"
                        value={formData.phone}
                        onChangeText={t => setFormData({ ...formData, phone: t })}
                    />
                </View>
            </View>

            <View style={styles.rowInputs}>
                <View style={[styles.inputGroup, { flex: 1, marginRight: 8 }]}>
                    <Text style={styles.inputLabel}>Tanggal</Text>
                    <View style={styles.inputWrapper}>
                        <Icon name="calendar-outline" size={20} color="#9CA3AF" style={styles.inputIcon} />
                        <TextInput
                            style={styles.input}
                            placeholder="YYYY-MM-DD"
                            value={formData.date}
                            onChangeText={t => setFormData({ ...formData, date: t })}
                        />
                    </View>
                </View>
                <View style={[styles.inputGroup, { flex: 1, marginLeft: 8 }]}>
                    <Text style={styles.inputLabel}>Waktu</Text>
                    <View style={styles.inputWrapper}>
                        <Icon name="time-outline" size={20} color="#9CA3AF" style={styles.inputIcon} />
                        <TextInput
                            style={styles.input}
                            placeholder="19:00"
                            value={formData.time}
                            onChangeText={t => setFormData({ ...formData, time: t })}
                        />
                    </View>
                </View>
            </View>

            <View style={styles.buttonsRow}>
                <TouchableOpacity style={styles.backBtn} onPress={() => setStep(1)}>
                    <Icon name="arrow-back" size={20} color="#374151" />
                </TouchableOpacity>
                <TouchableOpacity style={styles.submitBtn} onPress={handleSubmit}>
                    <LinearGradient
                        colors={[COLORS.maroon, COLORS.maroonDark]}
                        start={{ x: 0, y: 0 }} end={{ x: 1, y: 0 }}
                        style={styles.submitGradient}
                    >
                        <Text style={styles.submitText}>Konfirmasi Reservasi</Text>
                    </LinearGradient>
                </TouchableOpacity>
            </View>
        </Animated.View>
    );

    return (
        <View style={styles.container}>
            <StatusBar barStyle="light-content" backgroundColor={COLORS.maroon} />
            <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={{ paddingBottom: 100 }}>
                {renderHeader()}

                <View style={styles.content}>
                    {step === 1 ? (
                        <Animated.View entering={FadeIn}>
                            {renderStats()}
                            {renderMap()}
                        </Animated.View>
                    ) : (
                        renderForm()
                    )}
                </View>
            </ScrollView>

            {/* Receipt Modal */}
            <Modal visible={showReceipt} transparent animationType="fade">
                <View style={styles.modalOverlay}>
                    <Animated.View entering={ZoomIn} style={styles.receiptCard}>
                        <View style={styles.receiptHeader}>
                            <Icon name="checkmark-circle" size={48} color={COLORS.green} />
                            <Text style={styles.receiptTitle}>Reservasi Berhasil!</Text>
                            <Text style={styles.receiptSubtitle}>Kode: {receiptData?.id}</Text>
                        </View>

                        <View style={styles.receiptDivider} />

                        <View style={styles.receiptRow}>
                            <Text style={styles.receiptLabel}>Nama</Text>
                            <Text style={styles.receiptVal}>{receiptData?.name}</Text>
                        </View>
                        <View style={styles.receiptRow}>
                            <Text style={styles.receiptLabel}>Meja</Text>
                            <Text style={styles.receiptVal}>Meja {receiptData?.table} ({receiptData?.guests} Org)</Text>
                        </View>
                        <View style={styles.receiptRow}>
                            <Text style={styles.receiptLabel}>Waktu</Text>
                            <Text style={styles.receiptVal}>{receiptData?.date}, {receiptData?.time}</Text>
                        </View>

                        <TouchableOpacity style={styles.closeReceiptBtn} onPress={closeReceipt}>
                            <Text style={styles.closeReceiptText}>Selesai</Text>
                        </TouchableOpacity>
                    </Animated.View>
                </View>
            </Modal>
        </View>
    );
};

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: COLORS.cream },
    header: { padding: 24, paddingBottom: 32, borderBottomLeftRadius: 32, borderBottomRightRadius: 32, position: 'relative', overflow: 'hidden' },
    headerDecorCircle1: { position: 'absolute', top: -30, right: -30, width: 200, height: 200, borderRadius: 100, backgroundColor: 'rgba(255,255,255,0.05)' },
    headerDecorCircle2: { position: 'absolute', bottom: -40, left: -20, width: 150, height: 150, borderRadius: 75, backgroundColor: 'rgba(255,255,255,0.05)' },
    headerContent: { zIndex: 1 },
    titleRow: { flexDirection: 'row', alignItems: 'center', marginBottom: 24 },
    iconBox: { width: 48, height: 48, borderRadius: 16, backgroundColor: 'rgba(255,255,255,0.15)', alignItems: 'center', justifyContent: 'center', marginRight: 16 },
    headerTitle: { fontSize: 24, fontWeight: 'bold', color: COLORS.white, fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif' },
    headerSubtitle: { color: 'rgba(255,255,255,0.8)', fontSize: 14 },
    progressContainer: { flexDirection: 'row', gap: 12 },
    progressItem: { flex: 1 },
    progressBar: { height: 6, borderRadius: 3, backgroundColor: 'rgba(255,255,255,0.3)', marginBottom: 8 },
    progressLabel: { fontSize: 12, color: COLORS.white, fontWeight: '600' },

    content: { padding: 16, paddingTop: 24 },

    statsGrid: { flexDirection: 'row', gap: 8, marginBottom: 24 },
    statCard: { flex: 1, padding: 8, borderRadius: 12, borderWidth: 1, alignItems: 'center' },
    statVal: { fontSize: 18, fontWeight: 'bold' },
    statLabel: { fontSize: 10, fontWeight: '600' },

    legendContainer: { flexDirection: 'row', justifyContent: 'center', gap: 16, marginBottom: 16, paddingBottom: 16, borderBottomWidth: 1, borderBottomColor: '#E5E7EB' },
    legendItem: { flexDirection: 'row', alignItems: 'center', gap: 6 },
    legendDot: { width: 10, height: 10, borderRadius: 5, borderWidth: 1 },
    legendText: { fontSize: 12, color: COLORS.grayDark },

    // Map New Styles
    mapContainerFrame: { width: '100%', alignItems: 'center', marginVertical: 0, overflow: 'hidden', borderRadius: 24, backgroundColor: '#FFFBF5', borderWidth: 1, borderColor: '#F3F4F6', minHeight: 600 },
    floorPlank: { position: 'absolute', width: '200%', height: 120, backgroundColor: 'rgba(0,0,0,0.02)', transform: [{ rotate: '-5deg' }], left: '-50%', borderWidth: 0, borderBottomWidth: 1, borderColor: 'rgba(0,0,0,0.01)' },

    areaLabel: { position: 'absolute', top: '40%', alignSelf: 'center', alignItems: 'center', justifyContent: 'center', zIndex: 0 },
    areaBadge: { flexDirection: 'row', alignItems: 'center', gap: 6, backgroundColor: COLORS.white, paddingVertical: 8, paddingHorizontal: 16, borderRadius: 20, borderWidth: 1, borderColor: COLORS.grayLight, shadowColor: "#000", shadowOpacity: 0.05, shadowRadius: 5, elevation: 2 },
    areaGlow: { position: 'absolute', width: 200, height: 200, borderRadius: 100, backgroundColor: COLORS.goldLight, opacity: 0.15 },

    selectionCard: { marginTop: 16, marginHorizontal: 0, borderRadius: 16, overflow: 'hidden', elevation: 5, shadowColor: COLORS.blue, shadowOpacity: 0.2, shadowRadius: 10 },
    selectionGradient: { padding: 16, flexDirection: 'row', alignItems: 'center', gap: 12, borderWidth: 1, borderColor: '#BFDBFE', borderRadius: 16 },
    selectionIconBox: { width: 40, height: 40, borderRadius: 12, backgroundColor: 'rgba(37, 99, 235, 0.1)', alignItems: 'center', justifyContent: 'center' },
    selectionTitle: { fontSize: 14, fontWeight: 'bold', color: '#1E3A8A' },
    selectionDetail: { fontSize: 12, color: '#1E40AF' },
    nextBtnSmall: { backgroundColor: COLORS.blue, paddingVertical: 8, paddingHorizontal: 16, borderRadius: 8, flexDirection: 'row', alignItems: 'center', gap: 4 },
    nextBtnText: { color: 'white', fontWeight: 'bold', fontSize: 12 },

    formContainer: { backgroundColor: COLORS.white, borderRadius: 24, padding: 24, shadowColor: "#000", shadowOpacity: 0.05, shadowRadius: 10, elevation: 3 },
    formTitle: { fontSize: 20, fontWeight: 'bold', color: COLORS.grayDark, marginBottom: 24 },
    inputGroup: { marginBottom: 16 },
    inputLabel: { fontSize: 14, fontWeight: '600', color: COLORS.grayDark, marginBottom: 8 },
    inputWrapper: { position: 'relative' },
    inputIcon: { position: 'absolute', left: 16, top: 14, zIndex: 1 },
    input: { backgroundColor: '#F9FAFB', borderWidth: 1, borderColor: '#E5E7EB', borderRadius: 12, padding: 12, paddingLeft: 48, fontSize: 14, color: COLORS.grayDark },
    rowInputs: { flexDirection: 'row' },
    buttonsRow: { flexDirection: 'row', marginTop: 24, gap: 12 },
    backBtn: { width: 50, height: 50, borderRadius: 12, backgroundColor: '#F3F4F6', alignItems: 'center', justifyContent: 'center' },
    submitBtn: { flex: 1, borderRadius: 12, overflow: 'hidden' },
    submitGradient: { padding: 16, alignItems: 'center', justifyContent: 'center' },
    submitText: { color: 'white', fontWeight: 'bold', fontSize: 16 },

    modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', alignItems: 'center', justifyContent: 'center', padding: 24 },
    receiptCard: { backgroundColor: 'white', width: '100%', borderRadius: 24, padding: 32, alignItems: 'center', elevation: 10 },
    receiptHeader: { alignItems: 'center', marginBottom: 24 },
    receiptTitle: { fontSize: 22, fontWeight: 'bold', color: COLORS.grayDark, marginTop: 16 },
    receiptSubtitle: { fontSize: 14, color: '#6B7280', marginTop: 4 },
    receiptDivider: { width: '100%', height: 1, backgroundColor: '#E5E7EB', marginBottom: 24, borderStyle: 'dashed', borderWidth: 1, borderColor: '#E5E7EB' },
    receiptRow: { flexDirection: 'row', justifyContent: 'space-between', width: '100%', marginBottom: 12 },
    receiptLabel: { color: '#6B7280' },
    receiptVal: { fontWeight: 'bold', color: COLORS.grayDark },
    closeReceiptBtn: { marginTop: 24, width: '100%', backgroundColor: COLORS.grayLight, padding: 16, borderRadius: 12, alignItems: 'center' },
    closeReceiptText: { fontWeight: 'bold', color: COLORS.grayDark }
});

export default ReservationsScreen;
