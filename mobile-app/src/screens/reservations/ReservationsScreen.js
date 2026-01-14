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
    ActivityIndicator,
    Image
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
import { launchImageLibrary } from 'react-native-image-picker';
import RNFS from 'react-native-fs';
import { CameraRoll } from '@react-native-camera-roll/camera-roll';
import { check, request, PERMISSIONS, RESULTS } from 'react-native-permissions';

// QRIS Image for deposit
const QRIS_IMAGE = require('../../assets/qris_deposit.jpg');
const DEPOSIT_AMOUNT = 150000;

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
    // Deposit Payment State
    const [paymentProof, setPaymentProof] = useState(null);
    const [isUploading, setIsUploading] = useState(false);
    const [uploadProgress, setUploadProgress] = useState(0);
    const [reservationId, setReservationId] = useState(null);

    // Fetch tables from API
    const fetchTables = useCallback(async (date) => {
        try {
            setLoading(true);
            const token = await AsyncStorage.getItem('auth_token');
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
        else if (step === 2) {
            // Validate form first
            if (!formData.name || !formData.phone || !formData.date || !formData.time) {
                Alert.alert("Mohon Lengkapi Data", "Semua kolom form harus diisi.");
                return;
            }
            setStep(3); // Go to payment step
        }
    };

    // Create reservation (called at step 3 when confirming payment)
    const createReservation = async () => {
        try {
            setSubmitting(true);
            const token = await AsyncStorage.getItem('auth_token'); // FIXED: userToken -> auth_token
            const response = await axios.post(`${BASE_URL}/reservations`, {
                date: formData.date,
                time: formData.time,
                guests: selectedTable.capacity,
                name: formData.name,
                email: formData.email,
                phone: formData.phone,
                table_id: selectedTable.id,
                notes: null,
            }, {
                headers: { Authorization: `Bearer ${token}` }
            });

            if (response.data.success) {
                return response.data.reservation.id;
            }
            return null;
        } catch (error) {
            console.log('Reservation error:', error.response?.data || error.message);
            const msg = error.response?.data?.message || 'Gagal membuat reservasi';
            Alert.alert('Error', msg);
            return null;
        } finally {
            setSubmitting(false);
        }
    };

    // Pick payment proof image
    const pickPaymentProof = () => {
        launchImageLibrary({
            mediaType: 'photo',
            maxWidth: 1024,
            maxHeight: 1024,
            quality: 0.8,
        }, (response) => {
            if (response.didCancel) return;
            if (response.errorCode) {
                Alert.alert('Error', response.errorMessage || 'Gagal memilih gambar');
                return;
            }
            if (response.assets && response.assets[0]) {
                setPaymentProof(response.assets[0]);
            }
        });
    };

    // Confirm payment & upload proof
    const handleConfirmPayment = async () => {
        if (!paymentProof) {
            Alert.alert('Upload Bukti', 'Silakan upload bukti pembayaran QRIS terlebih dahulu.');
            return;
        }

        setIsUploading(true);
        setUploadProgress(0);

        // Create reservation first
        const reservId = await createReservation();
        if (!reservId) {
            setIsUploading(false);
            return;
        }

        // Simulate upload progress
        const progressInterval = setInterval(() => {
            setUploadProgress(prev => {
                if (prev >= 90) {
                    clearInterval(progressInterval);
                    return 90;
                }
                return prev + 10;
            });
        }, 200);

        try {
            const token = await AsyncStorage.getItem('auth_token'); // FIXED: userToken -> auth_token
            const formDataUpload = new FormData();
            formDataUpload.append('deposit_proof', {
                uri: paymentProof.uri,
                type: paymentProof.type || 'image/jpeg',
                name: paymentProof.fileName || 'deposit_proof.jpg',
            });

            await axios.post(`${BASE_URL}/reservations/${reservId}/upload-proof`, formDataUpload, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    'Content-Type': 'multipart/form-data',
                }
            });

            clearInterval(progressInterval);
            setUploadProgress(100);

            // Show receipt
            const receipt = {
                id: `RSV-${reservId}`,
                table: selectedTable.number,
                guests: selectedTable.capacity,
                date: formData.date,
                time: formData.time,
                name: formData.name,
                depositAmount: DEPOSIT_AMOUNT,
                depositStatus: 'PAID',
            };
            setReceiptData(receipt);

            setTimeout(() => {
                setIsUploading(false);
                setShowReceipt(true);
            }, 500);

        } catch (error) {
            clearInterval(progressInterval);
            console.log('Upload error:', error.response?.data || error.message);
            Alert.alert('Error', 'Gagal mengupload bukti pembayaran');
            setIsUploading(false);
        }
    };

    const closeReceipt = () => {
        setShowReceipt(false);
        setStep(1);
        setSelectedTableId(null);
        setFormData({ name: '', phone: '', email: '', date: '', time: '' });
        setPaymentProof(null);
        setUploadProgress(0);
        // Refresh tables to show updated availability
        fetchTables(selectedDate);
    };

    const handleDownloadQRIS = async () => {
        try {
            // Permission check for Android
            if (Platform.OS === 'android') {
                if (Platform.Version >= 33) {
                    // Android 13+ doesn't need WRITE_EXTERNAL_STORAGE for saving photos via CameraRoll?
                    // Actually CameraRoll handles it, but let's be safe.
                    // It typically needs READ_MEDIA_IMAGES (or VISUAL_USER_SELECTED_PHOTOS) to *read*, but *write* is often implicit for own app media.
                    // However, requesting READ_MEDIA_IMAGES is good practice if we were reading.
                    // For 'saving', usually no permission is needed if using MediaStore API which CameraRoll uses.
                } else {
                    const granted = await request(PERMISSIONS.ANDROID.WRITE_EXTERNAL_STORAGE);
                    if (granted !== RESULTS.GRANTED) {
                        Alert.alert('Izin Ditolak', 'Mohon izinkan akses penyimpanan untuk menyimpan QRIS.');
                        return;
                    }
                }
            }

            setLoading(true);
            const source = Image.resolveAssetSource(QRIS_IMAGE);
            let filePath = source.uri;

            // Handle remote URL (Development Mode)
            if (source.uri.startsWith('http') || source.uri.startsWith('https')) {
                const destPath = `${RNFS.CachesDirectoryPath}/qris_deposit.jpg`;
                const download = RNFS.downloadFile({
                    fromUrl: source.uri,
                    toFile: destPath,
                });
                await download.promise;
                filePath = destPath;
            }
            // Handle Local Asset (Release/Offline Mode)
            else if (Platform.OS === 'android' && !source.uri.startsWith('file://')) {
                const destPath = `${RNFS.CachesDirectoryPath}/qris_deposit.jpg`;
                try {
                    if (await RNFS.existsRes('src_assets_qris_deposit')) {
                        await RNFS.copyFileRes('src_assets_qris_deposit', destPath);
                        filePath = destPath;
                    }
                    else if (await RNFS.existsAssets('qris_deposit.jpg')) {
                        await RNFS.copyFileAssets('qris_deposit.jpg', destPath);
                        filePath = destPath;
                    }
                    else {
                        // Fallback to res with simple name
                        await RNFS.copyFileRes('qris_deposit', destPath);
                        filePath = destPath;
                    }
                } catch (err) {
                    console.log('Failed to copy asset:', err);
                    throw new Error(`Gagal menyalin aset: ${err.message}`);
                }
            }

            // Save to Gallery
            await CameraRoll.save(filePath, { type: 'photo', album: 'Culinaire' });
            Alert.alert('Berhasil', 'QRIS berhasil disimpan ke Galeri.');

        } catch (error) {
            console.log('Download Error:', error);
            Alert.alert('Gagal', 'Gagal menyimpan gambar. Pastikan izin diberikan.');
        } finally {
            setLoading(false);
        }
    };

    // --- RENDERERS ---

    const renderMap = () => (
        <ScrollView horizontal contentContainerStyle={{ paddingHorizontal: 16, paddingBottom: 20 }}>
            <View style={styles.mapContainerFrame}>
                {/* Floor Decor */}
                {[...Array(6)].map((_, i) => (
                    <View key={i} style={[styles.floorPlank, { top: i * 100 }]} />
                ))}

                {/* Legend */}
                <View style={[styles.legendContainer, { marginTop: 16 }]}>
                    <View style={styles.legendItem}>
                        <View style={[styles.legendDot, { backgroundColor: 'white', borderColor: '#DDD' }]} />
                        <Text style={styles.legendText}>Tersedia</Text>
                    </View>
                    <View style={styles.legendItem}>
                        <View style={[styles.legendDot, { backgroundColor: 'rgba(239, 68, 68, 0.1)', borderColor: 'rgba(239, 68, 68, 0.3)' }]} />
                        <Text style={styles.legendText}>Terisi</Text>
                    </View>
                    <View style={styles.legendItem}>
                        <View style={[styles.legendDot, { backgroundColor: '#FFFBEB', borderColor: COLORS.gold }]} />
                        <Text style={styles.legendText}>Premium</Text>
                    </View>
                    <View style={styles.legendItem}>
                        <View style={[styles.legendDot, { backgroundColor: COLORS.blue, borderColor: COLORS.blue }]} />
                        <Text style={[styles.legendText, { color: COLORS.blue, fontWeight: 'bold' }]}>Pilihanmu</Text>
                    </View>
                </View>

                {/* Grid Container */}
                <View style={{ width: GRID_SIZE * 6, height: GRID_SIZE * 9, position: 'relative', marginBottom: 20 }}>
                    {loading ? (
                        <ActivityIndicator size="large" color={COLORS.maroon} style={{ marginTop: 100 }} />
                    ) : (
                        tables.map(table => (
                            <RestaurantTable
                                key={table.id}
                                table={table}
                                isSelected={selectedTableId === table.id}
                                onSelect={setSelectedTableId}
                            />
                        ))
                    )}
                </View>
            </View>
        </ScrollView>
    );

    const renderHeader = () => (
        <LinearGradient
            colors={[COLORS.maroon, '#6B0F2A']}
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
                        <Text style={styles.headerTitle}>Reservasi Meja (NEW)</Text>
                        <Text style={styles.headerSubtitle}>Pesan tempat Anda sekarang</Text>
                    </View>
                </View>

                {/* Progress - 3 Steps */}
                <View style={styles.progressContainer}>
                    <View style={styles.progressItem}>
                        <View style={[styles.progressBar, { backgroundColor: COLORS.white }]} />
                        <Text style={[styles.progressLabel, { color: COLORS.white }]}>1. Pilih Meja</Text>
                    </View>
                    <View style={styles.progressItem}>
                        <View style={[styles.progressBar, { backgroundColor: step >= 2 ? COLORS.white : 'rgba(255,255,255,0.3)' }]} />
                        <Text style={[styles.progressLabel, { opacity: step >= 2 ? 1 : 0.5 }]}>2. Isi Data</Text>
                    </View>
                    <View style={styles.progressItem}>
                        <View style={[styles.progressBar, { backgroundColor: step >= 3 ? COLORS.white : 'rgba(255,255,255,0.3)' }]} />
                        <Text style={[styles.progressLabel, { opacity: step >= 3 ? 1 : 0.5 }]}>3. Pembayaran</Text>
                    </View>
                </View>
            </View>
        </LinearGradient>
    );

    const renderSelectedTableCard = (showChangeButton = false) => (
        <View style={styles.mejaCard}>
            <View>
                <Text style={styles.mejaCardLabel}>Meja Terpilih</Text>
                <Text style={styles.mejaCardValue}>Meja {selectedTable?.number} - {selectedTable?.capacity} Orang</Text>
            </View>
            {showChangeButton && (
                <TouchableOpacity style={styles.mejaChangeBtn} onPress={() => setStep(1)}>
                    <Text style={styles.mejaChangeBtnText}>Ganti</Text>
                </TouchableOpacity>
            )}
        </View>
    );

    const renderInfoPenting = () => (
        <View style={styles.infoPentingContainer}>
            <Text style={styles.infoPentingTitle}>Informasi Penting</Text>
            <View style={styles.bulletItem}>
                <Text style={styles.bulletPoint}>•</Text>
                <Text style={styles.bulletText}>Reservasi dapat dilakukan minimal 2 jam sebelum waktu kedatangan</Text>
            </View>
            <View style={styles.bulletItem}>
                <Text style={styles.bulletPoint}>•</Text>
                <Text style={styles.bulletText}>Konfirmasi akan dikirim melalui email dan WhatsApp</Text>
            </View>
            <View style={styles.bulletItem}>
                <Text style={styles.bulletPoint}>•</Text>
                <Text style={styles.bulletText}>Harap datang tepat waktu, meja akan ditahan maksimal 15 menit</Text>
            </View>
            <View style={styles.bulletItem}>
                <Text style={styles.bulletPoint}>•</Text>
                <Text style={styles.bulletText}>Untuk reservasi grup (10+ orang), hubungi kami langsung</Text>
            </View>
        </View>
    );

    const renderForm = () => (
        <View style={{ paddingBottom: 40 }}>
            {renderSelectedTableCard(true)}

            <View style={styles.formCard}>
                <Text style={styles.sectionTitle}>Informasi Reservasi</Text>

                <View style={styles.inputGroup}>
                    <Text style={styles.inputLabel}>Nama Lengkap</Text>
                    <TextInput
                        style={styles.simpleInput}
                        placeholder="Masukkan nama lengkap"
                        value={formData.name}
                        onChangeText={t => setFormData({ ...formData, name: t })}
                    />
                </View>

                <View style={styles.inputGroup}>
                    <Text style={styles.inputLabel}>Nomor Telepon</Text>
                    <TextInput
                        style={styles.simpleInput}
                        placeholder="08xx-xxxx-xxxx"
                        keyboardType="phone-pad"
                        value={formData.phone}
                        onChangeText={t => setFormData({ ...formData, phone: t })}
                    />
                </View>

                <View style={styles.inputGroup}>
                    <Text style={styles.inputLabel}>Email</Text>
                    <TextInput
                        style={styles.simpleInput}
                        placeholder="email@example.com"
                        keyboardType="email-address"
                        value={formData.email}
                        onChangeText={t => setFormData({ ...formData, email: t })}
                    />
                </View>

                <View style={styles.rowInputs}>
                    <View style={[styles.inputGroup, { flex: 1, marginRight: 8 }]}>
                        <Text style={styles.inputLabel}>Tanggal Reservasi</Text>
                        <View style={styles.iconInputWrapper}>
                            <Icon name="calendar-outline" size={20} color="#666" style={styles.inputInnerIcon} />
                            <TextInput
                                style={[styles.simpleInput, { paddingLeft: 44 }]}
                                value={formData.date}
                                editable={false} // Disable manual edit for now, typically needs DatePicker
                            />
                            <Icon name="chevron-down" size={16} color="#666" style={styles.inputArrowIcon} />
                        </View>
                    </View>
                    <View style={[styles.inputGroup, { flex: 1, marginLeft: 8 }]}>
                        <Text style={styles.inputLabel}>Waktu</Text>
                        <TouchableOpacity onPress={() => {/* Show Time Picker logic */ }}>
                            <View style={styles.iconInputWrapper}>
                                <Icon name="time-outline" size={20} color="#666" style={styles.inputInnerIcon} />
                                <TextInput
                                    style={[styles.simpleInput, { paddingLeft: 44 }]}
                                    placeholder="Pilih waktu"
                                    value={formData.time}
                                    onChangeText={t => setFormData({ ...formData, time: t })}
                                />
                            </View>
                        </TouchableOpacity>
                    </View>
                </View>

                <View style={styles.actionButtons}>
                    <TouchableOpacity style={styles.btnBack} onPress={() => setStep(1)}>
                        <Icon name="arrow-back" size={18} color="#333" />
                        <Text style={styles.btnBackText}>Kembali</Text>
                    </TouchableOpacity>
                    <TouchableOpacity style={styles.btnPrimary} onPress={handleNext}>
                        <Text style={styles.btnPrimaryText}>Pembayaran</Text>
                    </TouchableOpacity>
                </View>
            </View>

            {renderInfoPenting()}
        </View>
    );

    // Step 3: Payment
    const renderStep3 = () => (
        <View style={{ paddingBottom: 40 }}>
            <View style={styles.blueBanner}>
                <View style={{ flexDirection: 'row', alignItems: 'flex-start' }}>
                    <Text style={{ fontSize: 16 }}>💰</Text>
                    <View style={{ marginLeft: 8 }}>
                        <Text style={styles.blueBannerTitle}>Deposit Reservasi</Text>
                        <Text style={styles.blueBannerText}>Untuk mengkonfirmasi reservasi Anda, diperlukan pembayaran deposit sebesar:</Text>
                        <Text style={styles.blueBannerAmount}>Rp {DEPOSIT_AMOUNT.toLocaleString('id-ID')}</Text>
                        <View style={{ marginTop: 8 }}>
                            <Text style={styles.blueBannerCheck}>✓ Deposit akan dikembalikan setelah check-in</Text>
                            <Text style={styles.blueBannerCheck}>✓ Berlaku untuk semua tipe meja</Text>
                            <Text style={styles.blueBannerCheck}>✓ Transfer ke QRIS di bawah</Text>
                        </View>
                    </View>
                </View>
            </View>

            {renderSelectedTableCard(false)}

            <View style={styles.paymentCard}>
                <Text style={[styles.sectionTitle, { textAlign: 'center' }]}>Scan QRIS untuk{'\n'}Pembayaran</Text>

                <View style={styles.qrisWrapper}>
                    <Image source={QRIS_IMAGE} style={styles.qrisImageLarge} resizeMode="cover" />
                </View>

                <View style={{ flexDirection: 'row', justifyContent: 'center', alignItems: 'center', gap: 8, marginTop: 12 }}>
                    <Text style={{ fontSize: 16 }}>💳</Text>
                    <Text style={{ fontSize: 14, color: '#666' }}>Scan QRIS  Rp {DEPOSIT_AMOUNT.toLocaleString('id-ID')}</Text>
                </View>
                <Text style={{ textAlign: 'center', fontSize: 12, color: '#999', marginTop: 4 }}>Gunakan aplikasi mobile banking Anda</Text>

                <TouchableOpacity style={styles.downloadBtn} onPress={handleDownloadQRIS}>
                    <Icon name="download-outline" size={18} color="#333" />
                    <Text style={styles.downloadBtnText}>Download QRIS (Simpan ke Galeri)</Text>
                </TouchableOpacity>

                <View style={styles.dividerDashed} />

                <Text style={[styles.sectionTitle, { fontSize: 16, marginBottom: 16 }]}>📷 Upload Bukti Pembayaran</Text>

                <TouchableOpacity style={styles.uploadBox} onPress={pickPaymentProof}>
                    {paymentProof ? (
                        <View style={{ width: '100%', alignItems: 'center' }}>
                            <Image source={{ uri: paymentProof.uri }} style={{ width: 100, height: 100, borderRadius: 8, marginBottom: 8 }} />
                            <Text style={{ color: COLORS.green, fontWeight: 'bold' }}>✓ File terpilih</Text>
                        </View>
                    ) : (
                        <>
                            <Icon name="cloud-upload-outline" size={32} color="#999" />
                            <Text style={{ fontSize: 12, color: '#666', marginTop: 8 }}>Klik untuk upload bukti transfer</Text>
                            <Text style={{ fontSize: 10, color: '#999', marginTop: 4 }}>PNG, JPG (MAX. 5MB)</Text>
                        </>
                    )}
                </TouchableOpacity>
                {/* Progress Bar */}
                {isUploading && (
                    <View style={styles.progressBarContainer}>
                        <View style={[styles.progressBarFill, { width: `${uploadProgress}%` }]} />
                        <Text style={styles.progressText}>{uploadProgress}%</Text>
                    </View>
                )}

                <View style={styles.actionButtons}>
                    <TouchableOpacity style={styles.btnBack} onPress={() => setStep(2)}>
                        <Icon name="arrow-back" size={18} color="#333" />
                        <Text style={styles.btnBackText}>Kembali</Text>
                    </TouchableOpacity>
                    <TouchableOpacity
                        style={[styles.btnPrimary, { backgroundColor: (!paymentProof || isUploading) ? '#ccc' : COLORS.maroon }]}
                        onPress={handleConfirmPayment}
                        disabled={!paymentProof || isUploading}
                    >
                        {isUploading ? (
                            <ActivityIndicator color="white" size="small" />
                        ) : (
                            <Text style={styles.btnPrimaryText}>Konfirmasi{'\n'}Pembayaran</Text>
                        )}
                    </TouchableOpacity>
                </View>
            </View>

            {renderInfoPenting()}
        </View>
    );

    return (
        <View style={styles.container}>
            <StatusBar barStyle="light-content" backgroundColor={COLORS.maroon} />
            <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={{ paddingBottom: 100 }}>
                {renderHeader()}

                <View style={styles.content}>
                    {step === 1 && (
                        <Animated.View entering={FadeIn}>
                            {renderMap()}
                            {selectedTable && (
                                <Animated.View entering={SlideInDown} style={{ marginTop: 20 }}>
                                    {renderSelectedTableCard(false)}
                                    <TouchableOpacity style={[styles.btnPrimary, { marginTop: 16 }]} onPress={handleNext}>
                                        <Text style={styles.btnPrimaryText}>Lanjut Isi Data</Text>
                                    </TouchableOpacity>
                                </Animated.View>
                            )}
                        </Animated.View>
                    )}
                    {step === 2 && renderForm()}
                    {step === 3 && renderStep3()}
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

                        {/* Deposit Section */}
                        <View style={styles.depositSection}>
                            <Text style={styles.depositSectionTitle}>💰 Deposit Information</Text>
                            <View style={styles.depositSectionRow}>
                                <Text style={styles.depositSectionLabel}>Deposit Paid</Text>
                                <Text style={styles.depositSectionValue}>Rp {(receiptData?.depositAmount || 150000).toLocaleString('id-ID')}</Text>
                            </View>
                            <View style={styles.depositSectionRow}>
                                <Text style={styles.depositSectionLabel}>Status</Text>
                                <Text style={[styles.depositSectionValue, { color: '#10B981' }]}>✓ {receiptData?.depositStatus || 'PAID'}</Text>
                            </View>
                            <View style={styles.depositSectionRow}>
                                <Text style={styles.depositSectionLabel}>Refund</Text>
                                <Text style={styles.depositSectionValue}>After Check-in</Text>
                            </View>
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
    container: { flex: 1, backgroundColor: '#F8F6F4' }, // Premium cream background
    header: { padding: 24, paddingBottom: 32, borderBottomLeftRadius: 0, borderBottomRightRadius: 0 },
    headerDecorCircle1: { position: 'absolute', top: -30, right: -30, width: 200, height: 200, borderRadius: 100, backgroundColor: 'rgba(255,255,255,0.05)' },
    headerDecorCircle2: { position: 'absolute', bottom: -40, left: -20, width: 150, height: 150, borderRadius: 75, backgroundColor: 'rgba(255,255,255,0.05)' },
    headerContent: { zIndex: 1 },
    titleRow: { flexDirection: 'row', alignItems: 'center', marginBottom: 24 },
    iconBox: { width: 48, height: 48, borderRadius: 16, backgroundColor: 'rgba(255,255,255,0.15)', alignItems: 'center', justifyContent: 'center', marginRight: 16 },
    headerTitle: { fontSize: 24, fontWeight: 'bold', color: COLORS.white, fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif' },
    headerSubtitle: { color: 'rgba(255,255,255,0.8)', fontSize: 13 },
    progressContainer: { flexDirection: 'row', gap: 8 },
    progressItem: { flex: 1 },
    progressBar: { height: 4, borderRadius: 2, backgroundColor: 'rgba(255,255,255,0.3)', marginBottom: 6 },
    progressLabel: { fontSize: 10, color: COLORS.white, fontWeight: '500' },

    content: { padding: 16, paddingTop: 24 },

    // Components
    mejaCard: { backgroundColor: 'white', flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', padding: 16, borderRadius: 16, marginBottom: 20, shadowColor: "#000", shadowOpacity: 0.05, shadowRadius: 5, elevation: 2 },
    mejaCardLabel: { fontSize: 12, color: '#666', marginBottom: 4 },
    mejaCardValue: { fontSize: 16, fontWeight: 'bold', color: '#333' },
    mejaChangeBtn: { backgroundColor: '#334155', paddingHorizontal: 16, paddingVertical: 8, borderRadius: 8 },
    mejaChangeBtnText: { color: 'white', fontSize: 12, fontWeight: '600' },

    formCard: { backgroundColor: 'white', borderRadius: 24, padding: 24, shadowColor: "#000", shadowOpacity: 0.05, shadowRadius: 10, elevation: 3, marginBottom: 24 },
    sectionTitle: { fontSize: 18, fontWeight: 'bold', color: '#1F2937', marginBottom: 20 },

    inputGroup: { marginBottom: 16 },
    inputLabel: { fontSize: 13, color: '#374151', marginBottom: 8, fontWeight: '500' },
    simpleInput: { backgroundColor: 'white', borderWidth: 1, borderColor: '#E5E7EB', borderRadius: 12, padding: 12, fontSize: 14, color: '#333' },
    iconInputWrapper: { position: 'relative' },
    inputInnerIcon: { position: 'absolute', left: 12, top: 12, zIndex: 1 },
    inputArrowIcon: { position: 'absolute', right: 12, top: 14 },
    rowInputs: { flexDirection: 'row', width: '100%' },

    actionButtons: { flexDirection: 'row', gap: 12, marginTop: 12 },
    btnBack: { flex: 1, backgroundColor: '#E2E8F0', padding: 14, borderRadius: 12, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8 },
    btnBackText: { color: '#334155', fontWeight: 'bold', fontSize: 14 },
    btnPrimary: { flex: 1.5, backgroundColor: COLORS.maroon, padding: 14, borderRadius: 12, alignItems: 'center', justifyContent: 'center' },
    btnPrimaryText: { color: 'white', fontWeight: 'bold', fontSize: 14, textAlign: 'center' },

    // Info Penting
    infoPentingContainer: { backgroundColor: 'white', padding: 24, borderRadius: 16, marginTop: 0 },
    infoPentingTitle: { fontSize: 16, fontWeight: 'bold', color: '#1F2937', marginBottom: 12, fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif' },
    bulletItem: { flexDirection: 'row', gap: 8, marginBottom: 8 },
    bulletPoint: { color: COLORS.maroon, fontSize: 16, lineHeight: 20 },
    bulletText: { flex: 1, fontSize: 12, color: '#4B5563', lineHeight: 20 },

    // Payment specific
    blueBanner: { backgroundColor: '#EFF6FF', padding: 16, borderRadius: 16, marginBottom: 20, borderWidth: 1, borderColor: '#DBEAFE' },
    blueBannerTitle: { fontSize: 14, fontWeight: 'bold', color: '#1E40AF', marginBottom: 4 },
    blueBannerText: { fontSize: 12, color: '#1E3A8A', marginBottom: 8, lineHeight: 18 },
    blueBannerAmount: { fontSize: 24, fontWeight: 'bold', color: '#2563EB', marginBottom: 8 },
    blueBannerCheck: { fontSize: 11, color: '#3B82F6', marginBottom: 2 },

    paymentCard: { backgroundColor: 'white', borderRadius: 24, padding: 24, shadowColor: "#000", shadowOpacity: 0.05, shadowRadius: 10, elevation: 3, marginBottom: 24 },
    qrisWrapper: { alignItems: 'center', marginVertical: 12 },
    qrisImageLarge: { width: 200, height: 260, borderRadius: 12 },
    downloadBtn: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8, backgroundColor: '#F3F4F6', padding: 12, borderRadius: 12, marginTop: 16, width: '100%' },
    downloadBtnText: { color: '#374151', fontWeight: '600', fontSize: 14 },

    dividerDashed: { height: 1, borderWidth: 1, borderColor: '#E5E7EB', borderStyle: 'dashed', marginVertical: 24, width: '100%' },

    uploadBox: { borderRadius: 16, borderWidth: 1.5, borderColor: '#D1D5DB', borderStyle: 'dashed', padding: 24, alignItems: 'center', justifyContent: 'center', backgroundColor: '#F9FAFB' },
    progressBarContainer: { marginTop: 12, height: 6, backgroundColor: '#E5E7EB', borderRadius: 3, overflow: 'hidden' },
    progressBarFill: { height: '100%', backgroundColor: COLORS.maroon },
    progressText: { textAlign: 'right', fontSize: 10, color: '#666', marginTop: 4 },

    // Old map styles (keep necessary ones)
    statsGrid: { flexDirection: 'row', gap: 8, marginBottom: 24 },
    statCard: { flex: 1, padding: 8, borderRadius: 12, borderWidth: 1, alignItems: 'center' },
    statVal: { fontSize: 18, fontWeight: 'bold' },
    statLabel: { fontSize: 10, fontWeight: '600' },
    legendContainer: { flexDirection: 'row', justifyContent: 'center', gap: 16, marginBottom: 16, paddingBottom: 16, borderBottomWidth: 1, borderBottomColor: '#E5E7EB' },
    legendItem: { flexDirection: 'row', alignItems: 'center', gap: 6 },
    legendDot: { width: 10, height: 10, borderRadius: 5, borderWidth: 1 },
    legendText: { fontSize: 12, color: COLORS.grayDark },
    mapContainerFrame: { width: '100%', alignItems: 'center', marginVertical: 0, overflow: 'hidden', borderRadius: 24, backgroundColor: '#FFFBF5', borderWidth: 1, borderColor: '#F3F4F6', minHeight: 600 },
    floorPlank: { position: 'absolute', width: '200%', height: 120, backgroundColor: 'rgba(0,0,0,0.02)', transform: [{ rotate: '-5deg' }], left: '-50%', borderWidth: 0, borderBottomWidth: 1, borderColor: 'rgba(0,0,0,0.01)' },

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
    closeReceiptText: { fontWeight: 'bold', color: COLORS.grayDark },
    depositSection: { width: '100%', backgroundColor: '#F0FDF4', padding: 16, borderRadius: 12, marginTop: 16, marginBottom: 8 },
    depositSectionTitle: { fontSize: 14, fontWeight: '600', color: '#065F46', marginBottom: 8 },
    depositSectionRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 4 },
    depositSectionLabel: { fontSize: 13, color: '#047857' },
    depositSectionValue: { fontSize: 13, fontWeight: '600', color: '#065F46' },
});

export default ReservationsScreen;
