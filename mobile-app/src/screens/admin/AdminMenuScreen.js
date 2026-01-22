import React, { useState, useCallback } from 'react';
import {
    View,
    Text,
    StyleSheet,
    FlatList,
    Image,
    TouchableOpacity,
    Alert,
    RefreshControl,
    TextInput
} from 'react-native';
import { useFocusEffect } from '@react-navigation/native';
import Icon from 'react-native-vector-icons/Ionicons';
import { colors, spacing, borderRadius, fontSize } from '../../theme/colors';
import { adminAPI } from '../../api/client';
import LinearGradient from 'react-native-linear-gradient';
const AdminMenuScreen = ({ navigation }) => {
    const [menus, setMenus] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');
    useFocusEffect(
        useCallback(() => {
            loadMenus();
        }, [])
    );
    const loadMenus = async () => {
        try {
            const response = await adminAPI.getMenus();
            const data = response.data.data || response.data;
            setMenus(Array.isArray(data) ? data : []);
        } catch (error) {
            console.error('Load menus error:', error);
            Alert.alert('Error', 'Gagal memuat data menu');
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };
    const handleDelete = (menu) => {
        Alert.alert(
            'Hapus Menu',
            `Apakah Anda yakin ingin menghapus "${menu.name}"?`,
            [
                { text: 'Batal', style: 'cancel' },
                {
                    text: 'Hapus',
                    style: 'destructive',
                    onPress: async () => {
                        try {
                            await adminAPI.deleteMenu(menu.slug || menu.id);
                            loadMenus();
                            Alert.alert('Sukses', 'Menu berhasil dihapus');
                        } catch (error) {
                            Alert.alert('Error', 'Gagal menghapus menu');
                        }
                    }
                }
            ]
        );
    };
    const filteredMenus = menus.filter(item =>
        item.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        item.category.toLowerCase().includes(searchQuery.toLowerCase())
    );
    const renderItem = ({ item }) => (
        <View style={styles.card}>
            <Image
                source={{ uri: item.image_url || 'https://via.placeholder.com/100' }}
                style={styles.image}
            />
            <View style={styles.content}>
                <View style={styles.headerRow}>
                    <Text style={styles.name} numberOfLines={1}>{item.name}</Text>
                    <View style={[styles.statusBadge, { backgroundColor: item.is_available ? '#4CD96420' : '#FF3B3020' }]}>
                        <Text style={[styles.statusText, { color: item.is_available ? '#4CD964' : '#FF3B30' }]}>
                            {item.is_available ? 'Active' : 'Sold Out'}
                        </Text>
                    </View>
                </View>
                <Text style={styles.price}>Rp {parseInt(item.price).toLocaleString('id-ID')}</Text>
                <Text style={styles.category}>{item.category}</Text>
                <View style={styles.actionRow}>
                    <TouchableOpacity
                        style={[styles.actionBtn, styles.editBtn]}
                        onPress={() => Alert.alert('Info', 'Fitur Edit akan segera hadir di update berikutnya!')}
                    >
                        <Icon name="create-outline" size={18} color="#D4AF37" />
                        <Text style={styles.editBtnText}>Edit</Text>
                    </TouchableOpacity>
                    <TouchableOpacity
                        style={[styles.actionBtn, styles.deleteBtn]}
                        onPress={() => handleDelete(item)}
                    >
                        <Icon name="trash-outline" size={18} color="#FF3B30" />
                        <Text style={styles.deleteBtnText}>Delete</Text>
                    </TouchableOpacity>
                </View>
            </View>
        </View>
    );
    return (
        <View style={styles.container}>
            {}
            <View style={styles.header}>
                <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
                    <Icon name="arrow-back" size={24} color="#FFF" />
                </TouchableOpacity>
                <Text style={styles.headerTitle}>Manage Menu</Text>
                <View style={{ width: 24 }} />
            </View>
            {}
            <View style={styles.searchContainer}>
                <View style={styles.searchBar}>
                    <Icon name="search" size={20} color={colors.textSecondary} />
                    <TextInput
                        style={styles.searchInput}
                        placeholder="Search menu..."
                        placeholderTextColor={colors.textSecondary}
                        value={searchQuery}
                        onChangeText={setSearchQuery}
                    />
                </View>
            </View>
            <FlatList
                data={filteredMenus}
                keyExtractor={(item) => item.id.toString()}
                renderItem={renderItem}
                contentContainerStyle={styles.list}
                refreshControl={
                    <RefreshControl
                        refreshing={refreshing}
                        onRefresh={() => { setRefreshing(true); loadMenus(); }}
                        tintColor="#D4AF37"
                    />
                }
                ListEmptyComponent={
                    !loading && (
                        <View style={styles.emptyContainer}>
                            <Icon name="restaurant-outline" size={64} color="#333" />
                            <Text style={styles.emptyText}>No menu items found</Text>
                        </View>
                    )
                }
            />
            {}
            <TouchableOpacity
                style={styles.fab}
                onPress={() => Alert.alert('Info', 'Fitur Tambah Menu akan segera hadir di update berikutnya!')}
            >
                <LinearGradient
                    colors={['#D4AF37', '#B8860B']}
                    style={styles.fabGradient}
                >
                    <Icon name="add" size={32} color="#FFF" />
                </LinearGradient>
            </TouchableOpacity>
        </View>
    );
};
const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#0F0F0F',
    },
    header: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        paddingTop: 50,
        paddingHorizontal: spacing.lg,
        paddingBottom: spacing.md,
        backgroundColor: '#0F0F0F',
        borderBottomWidth: 1,
        borderBottomColor: '#333',
    },
    headerTitle: {
        fontSize: 20,
        fontWeight: 'bold',
        color: '#FFF',
        letterSpacing: 1,
    },
    searchContainer: {
        padding: spacing.md,
        backgroundColor: '#0F0F0F',
    },
    searchBar: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: '#1A1A1A',
        borderRadius: borderRadius.md,
        paddingHorizontal: spacing.md,
        height: 45,
        borderWidth: 1,
        borderColor: '#333',
    },
    searchInput: {
        flex: 1,
        marginLeft: spacing.sm,
        color: '#FFF',
        fontSize: fontSize.md,
    },
    list: {
        padding: spacing.md,
        paddingBottom: 100,
    },
    card: {
        flexDirection: 'row',
        backgroundColor: '#1A1A1A',
        borderRadius: borderRadius.lg,
        padding: spacing.sm,
        marginBottom: spacing.md,
        borderWidth: 1,
        borderColor: '#333',
    },
    image: {
        width: 100,
        height: 100,
        borderRadius: borderRadius.md,
        backgroundColor: '#333',
    },
    content: {
        flex: 1,
        marginLeft: spacing.md,
        justifyContent: 'space-between',
        paddingVertical: 2,
    },
    headerRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'flex-start',
    },
    name: {
        flex: 1,
        fontSize: fontSize.md,
        fontWeight: 'bold',
        color: '#FFF',
        marginRight: 8,
    },
    statusBadge: {
        paddingHorizontal: 8,
        paddingVertical: 2,
        borderRadius: 4,
    },
    statusText: {
        fontSize: 10,
        fontWeight: 'bold',
    },
    price: {
        fontSize: fontSize.md,
        color: '#D4AF37',
        fontWeight: '600',
    },
    category: {
        fontSize: fontSize.xs,
        color: colors.textSecondary,
        marginBottom: 4,
    },
    actionRow: {
        flexDirection: 'row',
        gap: spacing.sm,
    },
    actionBtn: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingVertical: 4,
        paddingHorizontal: 8,
        borderRadius: 4,
        borderWidth: 1,
    },
    editBtn: {
        borderColor: '#D4AF37',
        backgroundColor: 'rgba(212, 175, 55, 0.1)',
    },
    deleteBtn: {
        borderColor: '#FF3B30',
        backgroundColor: 'rgba(255, 59, 48, 0.1)',
    },
    editBtnText: {
        fontSize: 12,
        color: '#D4AF37',
        marginLeft: 4,
        fontWeight: '600',
    },
    deleteBtnText: {
        fontSize: 12,
        color: '#FF3B30',
        marginLeft: 4,
        fontWeight: '600',
    },
    fab: {
        position: 'absolute',
        bottom: 30,
        right: 30,
        shadowColor: "#D4AF37",
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.3,
        shadowRadius: 4.65,
        elevation: 8,
    },
    fabGradient: {
        width: 60,
        height: 60,
        borderRadius: 30,
        justifyContent: 'center',
        alignItems: 'center',
    },
    emptyContainer: {
        alignItems: 'center',
        marginTop: 60,
    },
    emptyText: {
        color: '#666',
        marginTop: 16,
        fontSize: 16,
    },
});
export default AdminMenuScreen;