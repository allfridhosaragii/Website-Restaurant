import React, { useState, useEffect } from 'react';
import {
    View,
    Text,
    ScrollView,
    StyleSheet,
    TouchableOpacity,
    Image,
    Switch,
    StatusBar,
    Dimensions,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import MaterialIcon from 'react-native-vector-icons/MaterialCommunityIcons';
import LinearGradient from 'react-native-linear-gradient';
import { useAuth } from '../../context/AuthContext';
import { useSettings } from '../../context/SettingsContext';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
const BASE_URL = 'https://website-restaurant.up.railway.app/api';
const { width } = Dimensions.get('window');
const ProfileScreen = ({ navigation }) => {
    const { user, logout } = useAuth();
    const { isDarkMode, toggleTheme } = useSettings();
    const [isIndonesian, setIsIndonesian] = useState(true);
    const [stats, setStats] = useState({ points: 0, total_orders: 0, total_favorites: 0 });
    useEffect(() => {
        fetchDashboardStats();
    }, []);
    const fetchDashboardStats = async () => {
        try {
            const token = await AsyncStorage.getItem('auth_token');
            if (!token) return;
            const response = await axios.get(`${BASE_URL}/dashboard`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            setStats(response.data);
        } catch (error) {
            console.log('Dashboard stats error:', error);
        }
    };
    const handleLogout = () => {
        logout();
    };
    return (
        <View style={styles.container}>
            <StatusBar barStyle="light-content" backgroundColor="#8B1538" />
            {}
            <View style={styles.headerBackground}>
                <View style={styles.headerCurve} />
            </View>
            <ScrollView
                showsVerticalScrollIndicator={false}
                contentContainerStyle={styles.scrollContent}
            >
                {}
                <Text style={styles.headerTitle}>Profile</Text>
                {}
                <TouchableOpacity
                    onPress={() => navigation.navigate('Points')}
                    activeOpacity={0.9}
                >
                    <LinearGradient
                        colors={['rgba(255,255,255,0.15)', 'rgba(255,255,255,0.05)']}
                        style={styles.profileCard}
                    >
                        <View style={styles.profileHeader}>
                            <View style={styles.avatarContainer}>
                                <View style={styles.avatar}>
                                    {user?.avatar_url ? (
                                        <Image source={{ uri: user.avatar_url }} style={styles.avatarImage} />
                                    ) : (
                                        <Icon name="person-outline" size={40} color="#FFF" />
                                    )}
                                </View>
                                <View style={styles.crownBadge}>
                                    <MaterialIcon name="crown" size={12} color="#FFF" />
                                </View>
                            </View>
                            <View style={styles.userInfo}>
                                <Text style={styles.userName}>{user?.name || 'Pengguna Culinaire'}</Text>
                                <Text style={styles.userEmail}>{user?.email || 'email@culinaire.com'}</Text>
                                <View style={styles.platinumBadge}>
                                    <Icon name="star" size={10} color="#FFD700" />
                                    <Text style={styles.platinumText}>PLATINUM MEMBER</Text>
                                </View>
                            </View>
                            <TouchableOpacity
                                style={styles.editButton}
                                onPress={() => navigation.navigate('EditProfile')}
                            >
                                <Icon name="create-outline" size={20} color="#FFF" />
                            </TouchableOpacity>
                        </View>
                        <View style={styles.statsRow}>
                            <View style={styles.statItem}>
                                <Text style={styles.statValue}>{(stats.points || 0).toLocaleString()}</Text>
                                <Text style={styles.statLabel}>Poin</Text>
                            </View>
                            <View style={styles.statDivider} />
                            <View style={styles.statItem}>
                                <Text style={styles.statValue}>{stats.total_orders || 0}</Text>
                                <Text style={styles.statLabel}>Pesanan</Text>
                            </View>
                            <View style={styles.statDivider} />
                            <View style={styles.statItem}>
                                <Text style={styles.statValue}>{stats.total_favorites || 0}</Text>
                                <Text style={styles.statLabel}>Favorit</Text>
                            </View>
                        </View>
                    </LinearGradient>
                </TouchableOpacity>
                {}
                <View style={styles.quickActionsRow}>
                    <TouchableOpacity
                        style={styles.quickActionCard}
                        onPress={() => navigation.navigate('Orders')}
                    >
                        <View style={[styles.quickActionIcon, { backgroundColor: '#FCE4EC' }]}>
                            <MaterialIcon name="clipboard-text-clock-outline" size={20} color="#D81B60" />
                        </View>
                        <View>
                            <Text style={styles.quickActionValue}>{stats.total_orders || 0}</Text>
                            <Text style={styles.quickActionLabel}>Histori</Text>
                        </View>
                    </TouchableOpacity>
                    <TouchableOpacity
                        style={styles.quickActionCard}
                        onPress={() => navigation.navigate('Favorites')}
                    >
                        <View style={[styles.quickActionIcon, { backgroundColor: '#FFF3E0' }]}>
                            <Icon name="star-outline" size={20} color="#F57C00" />
                        </View>
                        <View>
                            <Text style={styles.quickActionValue}>{stats.total_favorites || 0}</Text>
                            <Text style={styles.quickActionLabel}>Favorit</Text>
                        </View>
                    </TouchableOpacity>
                </View>
                {}
                <Text style={styles.sectionTitle}>Pengaturan Tampilan</Text>
                <View style={styles.menuCard}>
                    <View style={styles.menuItem}>
                        <View style={styles.menuItemLeft}>
                            <View style={[styles.menuIcon, { backgroundColor: '#FFF3E0' }]}>
                                <Icon name="sunny-outline" size={20} color="#F57C00" />
                            </View>
                            <Text style={styles.menuText}>Mode Terang</Text>
                        </View>
                        <Switch
                            trackColor={{ false: "#E0E0E0", true: "#8B1538" }}
                            thumbColor={"#FFF"}
                            ios_backgroundColor="#E0E0E0"
                            onValueChange={toggleTheme}
                            value={!isDarkMode}
                        />
                    </View>
                    <View style={styles.divider} />
                    <View style={styles.menuItem}>
                        <View style={styles.menuItemLeft}>
                            <View style={[styles.menuIcon, { backgroundColor: '#E3F2FD' }]}>
                                <Icon name="globe-outline" size={20} color="#1976D2" />
                            </View>
                            <View>
                                <Text style={styles.menuText}>Bahasa</Text>
                                <Text style={styles.menuSubText}>Indonesia</Text>
                            </View>
                        </View>
                        <Switch
                            trackColor={{ false: "#E0E0E0", true: "#8B1538" }}
                            thumbColor={"#FFF"}
                            ios_backgroundColor="#E0E0E0"
                            onValueChange={() => setIsIndonesian(!isIndonesian)}
                            value={isIndonesian}
                        />
                    </View>
                </View>
                {}
                <Text style={styles.sectionTitle}>Hubungi Kami</Text>
                <View style={styles.menuCard}>
                    <TouchableOpacity style={styles.menuItem}>
                        <View style={styles.menuItemLeft}>
                            <View style={[styles.menuIcon, { backgroundColor: '#E0F2F1' }]}>
                                <Icon name="call-outline" size={20} color="#00695C" />
                            </View>
                            <View>
                                <Text style={styles.menuText}>Telepon</Text>
                                <Text style={styles.menuSubText}>+62 21 1234 5678</Text>
                            </View>
                        </View>
                        <Icon name="chevron-forward" size={20} color="#CCC" />
                    </TouchableOpacity>
                    <View style={styles.divider} />
                    <TouchableOpacity style={styles.menuItem}>
                        <View style={styles.menuItemLeft}>
                            <View style={[styles.menuIcon, { backgroundColor: '#E3F2FD' }]}>
                                <Icon name="mail-outline" size={20} color="#1565C0" />
                            </View>
                            <View>
                                <Text style={styles.menuText}>Email</Text>
                                <Text style={styles.menuSubText}>info@culinaire.com</Text>
                            </View>
                        </View>
                        <Icon name="chevron-forward" size={20} color="#CCC" />
                    </TouchableOpacity>
                    <View style={styles.divider} />
                    <TouchableOpacity style={styles.menuItem}>
                        <View style={styles.menuItemLeft}>
                            <View style={[styles.menuIcon, { backgroundColor: '#FFEBEE' }]}>
                                <Icon name="location-outline" size={20} color="#C62828" />
                            </View>
                            <View style={{ flex: 1 }}>
                                <Text style={styles.menuText}>Alamat</Text>
                                <Text style={styles.menuSubText} numberOfLines={1}>Jl. Sudirman No. 123, Jakarta</Text>
                            </View>
                        </View>
                        <Icon name="chevron-forward" size={20} color="#CCC" />
                    </TouchableOpacity>
                </View>
                {}
                <View style={[styles.menuCard, { marginTop: 16 }]}>
                    <TouchableOpacity style={styles.menuItem}>
                        <View style={styles.menuItemLeft}>
                            <View style={[styles.menuIcon, { backgroundColor: '#F3E5F5' }]}>
                                <Icon name="information-circle-outline" size={20} color="#7B1FA2" />
                            </View>
                            <Text style={styles.menuText}>Tentang Culinaire</Text>
                        </View>
                        <Icon name="chevron-forward" size={20} color="#CCC" />
                    </TouchableOpacity>
                    <View style={styles.divider} />
                    <TouchableOpacity style={styles.menuItem}>
                        <View style={styles.menuItemLeft}>
                            <View style={[styles.menuIcon, { backgroundColor: '#FFF3E0' }]}>
                                <Icon name="help-circle-outline" size={20} color="#E65100" />
                            </View>
                            <Text style={styles.menuText}>Pusat Bantuan</Text>
                        </View>
                        <Icon name="chevron-forward" size={20} color="#CCC" />
                    </TouchableOpacity>
                    <View style={styles.divider} />
                    <TouchableOpacity style={styles.menuItem}>
                        <View style={styles.menuItemLeft}>
                            <View style={[styles.menuIcon, { backgroundColor: '#E8F5E9' }]}>
                                <Icon name="shield-checkmark-outline" size={20} color="#2E7D32" />
                            </View>
                            <Text style={styles.menuText}>Kebijakan Privasi</Text>
                        </View>
                        <Icon name="chevron-forward" size={20} color="#CCC" />
                    </TouchableOpacity>
                </View>
                {}
                <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
                    <Icon name="log-out-outline" size={20} color="#C62828" />
                    <Text style={styles.logoutText}>Keluar</Text>
                </TouchableOpacity>
                <View style={styles.footer}>
                    <Text style={styles.versionText}>Culinaire v1.0.0</Text>
                    <Text style={styles.sloganText}>Premium Dining Experience</Text>
                </View>
                <View style={{ height: 100 }} />
            </ScrollView>
        </View>
    );
};
const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#F5F5F0',
    },
    headerBackground: {
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        height: 300,
        backgroundColor: '#8B1538', 
        borderBottomLeftRadius: 40,
        borderBottomRightRadius: 40,
    },
    scrollContent: {
        paddingTop: 40,
        paddingHorizontal: 20,
    },
    headerTitle: {
        fontSize: 28,
        fontWeight: 'bold',
        color: '#FFF',
        marginBottom: 20,
        fontFamily: 'serif',
    },
    profileCard: {
        borderRadius: 24,
        padding: 20,
        borderWidth: 1,
        borderColor: 'rgba(255,255,255,0.2)',
        backgroundColor: 'rgba(255,255,255,0.1)', 
        marginBottom: 24,
    },
    profileHeader: {
        flexDirection: 'row',
        alignItems: 'center',
        marginBottom: 24,
    },
    avatarContainer: {
        position: 'relative',
        marginRight: 16,
    },
    avatar: {
        width: 64,
        height: 64,
        borderRadius: 32,
        backgroundColor: '#D4AF37', 
        justifyContent: 'center',
        alignItems: 'center',
        borderWidth: 2,
        borderColor: '#FFF',
        overflow: 'hidden',
    },
    avatarImage: {
        width: 64,
        height: 64,
        borderRadius: 32,
    },
    crownBadge: {
        position: 'absolute',
        bottom: 0,
        right: 0,
        backgroundColor: '#D4AF37',
        width: 20,
        height: 20,
        borderRadius: 10,
        justifyContent: 'center',
        alignItems: 'center',
        borderWidth: 1.5,
        borderColor: '#8B1538',
    },
    userInfo: {
        flex: 1,
    },
    userName: {
        fontSize: 18,
        fontWeight: 'bold',
        color: '#FFF',
        marginBottom: 4,
    },
    userEmail: {
        fontSize: 12,
        color: 'rgba(255,255,255,0.8)',
        marginBottom: 8,
    },
    platinumBadge: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: 'rgba(0,0,0,0.2)',
        alignSelf: 'flex-start',
        paddingHorizontal: 8,
        paddingVertical: 4,
        borderRadius: 12,
        borderWidth: 1,
        borderColor: '#D4AF37',
    },
    platinumText: {
        fontSize: 10,
        color: '#D4AF37',
        marginLeft: 4,
        fontWeight: '600',
    },
    editButton: {
        width: 40,
        height: 40,
        borderRadius: 12,
        borderWidth: 1,
        borderColor: 'rgba(255,255,255,0.3)',
        justifyContent: 'center',
        alignItems: 'center',
    },
    statsRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
    },
    statItem: {
        alignItems: 'center',
        flex: 1,
    },
    statValue: {
        fontSize: 20,
        fontWeight: 'bold',
        color: '#FFF',
        marginBottom: 2,
    },
    statLabel: {
        fontSize: 11,
        color: 'rgba(255,255,255,0.8)',
    },
    statDivider: {
        width: 1,
        height: 30,
        backgroundColor: 'rgba(255,255,255,0.2)',
    },
    quickActionsRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        marginBottom: 24,
    },
    quickActionCard: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: '#FFF',
        paddingVertical: 14,
        paddingHorizontal: 16,
        borderRadius: 16,
        width: (width - 52) / 2,
        elevation: 2,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 2 },
        shadowOpacity: 0.05,
        shadowRadius: 4,
    },
    quickActionIcon: {
        width: 40,
        height: 40,
        borderRadius: 12,
        justifyContent: 'center',
        alignItems: 'center',
        marginRight: 12,
    },
    quickActionValue: {
        fontSize: 16,
        fontWeight: 'bold',
        color: '#333',
    },
    quickActionLabel: {
        fontSize: 11,
        color: '#666',
    },
    sectionTitle: {
        fontSize: 16,
        fontWeight: 'bold',
        color: '#333',
        marginBottom: 12,
        marginTop: 8,
        fontFamily: 'serif',
    },
    menuCard: {
        backgroundColor: '#FFF',
        borderRadius: 16,
        paddingVertical: 8,
        paddingHorizontal: 16,
        marginBottom: 8,
        elevation: 2,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 1 },
        shadowOpacity: 0.05,
        shadowRadius: 2,
    },
    menuItem: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingVertical: 14,
    },
    menuItemLeft: {
        flexDirection: 'row',
        alignItems: 'center',
        flex: 1,
        marginRight: 10,
    },
    menuIcon: {
        width: 36,
        height: 36,
        borderRadius: 10,
        justifyContent: 'center',
        alignItems: 'center',
        marginRight: 14,
    },
    menuText: {
        fontSize: 14,
        color: '#333',
        fontWeight: '500',
    },
    menuSubText: {
        fontSize: 11,
        color: '#888',
        marginTop: 2,
    },
    divider: {
        height: 1,
        backgroundColor: '#F0F0F0',
        marginLeft: 50,
    },
    logoutButton: {
        marginTop: 24,
        marginBottom: 24,
        flexDirection: 'row',
        justifyContent: 'center',
        alignItems: 'center',
        backgroundColor: '#FFF',
        paddingVertical: 14,
        borderRadius: 16,
        borderWidth: 1,
        borderColor: '#FFEBEE',
    },
    logoutText: {
        color: '#C62828',
        fontSize: 14,
        fontWeight: 'bold',
        marginLeft: 8,
    },
    footer: {
        alignItems: 'center',
        marginBottom: 20,
    },
    versionText: {
        color: '#999',
        fontSize: 12,
    },
    sloganText: {
        color: '#BBB',
        fontSize: 10,
        marginTop: 4,
        fontStyle: 'italic',
        fontFamily: 'serif',
    },
});
export default ProfileScreen;