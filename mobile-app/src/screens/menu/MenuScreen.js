import React, { useState, useEffect } from 'react';
import {
    View,
    Text,
    FlatList,
    StyleSheet,
    TextInput,
    TouchableOpacity,
    ActivityIndicator,
    RefreshControl,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { colors, spacing, fontSize, borderRadius } from '../../theme/colors';
import { menuAPI } from '../../api/client';
import { MOCK_MENUS, MOCK_CATEGORIES } from '../../data/mockData';
import MenuCard from '../../components/MenuCard';

const MenuScreen = ({ navigation }) => {
    const [menus, setMenus] = useState([]);
    const [categories, setCategories] = useState([]);
    const [selectedCategory, setSelectedCategory] = useState('all');
    const [search, setSearch] = useState('');
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);

    useEffect(() => {
        loadMenus();
    }, [selectedCategory, search]);

    const loadMenus = async () => {
        try {
            const params = {};
            if (selectedCategory !== 'all') {
                params.category = selectedCategory;
            }
            if (search) {
                params.search = search;
            }

            const response = await menuAPI.getAll(params);

            if (response.data && response.data.menus && response.data.menus.length > 0) {
                setMenus(response.data.menus);
                if (response.data.categories) {
                    setCategories(['all', ...response.data.categories]);
                }
            } else {
                throw new Error('Empty data from API');
            }
        } catch (error) {
            console.log('API Error, using Mock Data:', error);
            // Fallback to Mock Data
            let filteredMenus = MOCK_MENUS;
            if (selectedCategory !== 'all') {
                filteredMenus = filteredMenus.filter(m => m.category === selectedCategory);
            }
            if (search) {
                const term = search.toLowerCase();
                filteredMenus = filteredMenus.filter(m =>
                    m.name.toLowerCase().includes(term) ||
                    m.description.toLowerCase().includes(term)
                );
            }
            setMenus(filteredMenus);
            setCategories(['all', ...MOCK_CATEGORIES]);
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };

    const onRefresh = () => {
        setRefreshing(true);
        loadMenus();
    };

    const renderMenuItem = ({ item }) => (
        <MenuCard
            menu={item}
            onPress={() => navigation.navigate('MenuDetail', { slug: item.slug })}
        />
    );

    return (
        <View style={styles.container}>
            {/* Search Bar */}
            <View style={styles.searchContainer}>
                <View style={styles.searchBar}>
                    <Icon name="search-outline" size={20} color={colors.textSecondary} />
                    <TextInput
                        style={styles.searchInput}
                        placeholder="Cari menu..."
                        placeholderTextColor={colors.textHint}
                        value={search}
                        onChangeText={setSearch}
                    />
                    {search ? (
                        <TouchableOpacity onPress={() => setSearch('')}>
                            <Icon name="close-circle" size={20} color={colors.textSecondary} />
                        </TouchableOpacity>
                    ) : null}
                </View>
            </View>

            {/* Categories */}
            <View style={styles.categoriesWrapper}>
                <FlatList
                    horizontal
                    data={categories}
                    keyExtractor={(item) => item}
                    showsHorizontalScrollIndicator={false}
                    contentContainerStyle={styles.categories}
                    renderItem={({ item }) => (
                        <TouchableOpacity
                            style={[
                                styles.categoryChip,
                                selectedCategory === item && styles.categoryChipActive,
                            ]}
                            onPress={() => setSelectedCategory(item)}
                        >
                            <Text
                                style={[
                                    styles.categoryText,
                                    selectedCategory === item && styles.categoryTextActive,
                                ]}
                            >
                                {item === 'all' ? 'Semua' : item}
                            </Text>
                        </TouchableOpacity>
                    )}
                />
            </View>

            {/* Menu List */}
            {loading ? (
                <View style={styles.loader}>
                    <ActivityIndicator size="large" color={colors.accent} />
                </View>
            ) : (
                <FlatList
                    data={menus}
                    keyExtractor={(item) => item.id.toString()}
                    renderItem={renderMenuItem}
                    numColumns={2}
                    contentContainerStyle={styles.menuList}
                    columnWrapperStyle={styles.menuRow}
                    refreshControl={
                        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
                    }
                    ListEmptyComponent={
                        <View style={styles.empty}>
                            <Icon name="restaurant-outline" size={48} color={colors.textSecondary} />
                            <Text style={styles.emptyText}>Tidak ada menu ditemukan</Text>
                        </View>
                    }
                />
            )}
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.background,
    },
    searchContainer: {
        padding: spacing.md,
        paddingTop: spacing.xl,
    },
    searchBar: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: colors.surface,
        borderRadius: borderRadius.md,
        paddingHorizontal: spacing.md,
        borderWidth: 1,
        borderColor: colors.border,
    },
    searchInput: {
        flex: 1,
        paddingVertical: spacing.md,
        paddingHorizontal: spacing.sm,
        color: colors.text,
        fontSize: fontSize.md,
    },
    categoriesWrapper: {
        marginBottom: spacing.sm,
    },
    categories: {
        paddingHorizontal: spacing.md,
        gap: spacing.sm,
    },
    categoryChip: {
        paddingVertical: spacing.sm,
        paddingHorizontal: spacing.md,
        backgroundColor: colors.surface,
        borderRadius: borderRadius.full,
        marginRight: spacing.sm,
    },
    categoryChipActive: {
        backgroundColor: colors.accent,
    },
    categoryText: {
        fontSize: fontSize.md,
        color: colors.textSecondary,
        textTransform: 'capitalize',
    },
    categoryTextActive: {
        color: colors.background,
        fontWeight: '600',
    },
    menuList: {
        padding: spacing.md,
    },
    menuRow: {
        justifyContent: 'space-between',
    },
    loader: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
    },
    empty: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        paddingTop: spacing.xxl,
    },
    emptyText: {
        fontSize: fontSize.md,
        color: colors.textSecondary,
        marginTop: spacing.md,
    },
});

export default MenuScreen;
