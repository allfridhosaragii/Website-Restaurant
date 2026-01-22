import React, { useState, useEffect } from 'react';
import {
    View,
    Text,
    ScrollView,
    StyleSheet,
    TouchableOpacity,
    ActivityIndicator,
    StatusBar,
    Image,
    Dimensions,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import LinearGradient from 'react-native-linear-gradient';
import { colors, spacing, fontSize, borderRadius } from '../../theme/colors';
import { menuAPI, cartAPI, favoritesAPI } from '../../api/client';
import { useSettings } from '../../context/SettingsContext';
const { width, height } = Dimensions.get('window');
const MenuDetailScreen = ({ route, navigation }) => {
    const slug = route?.params?.slug;
    const { isDarkMode, colors, t } = useSettings();
    React.useLayoutEffect(() => {
        navigation.setOptions({ headerShown: false });
    }, [navigation]);
    const [menu, setMenu] = useState(null);
    const [quantity, setQuantity] = useState(1);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [addingToCart, setAddingToCart] = useState(false);
    const [isFavorite, setIsFavorite] = useState(false);
    useEffect(() => {
        if (!slug) {
            setError('Invalid Item');
            setLoading(false);
            return;
        }
        loadMenu();
    }, [slug]);
    const loadMenu = async () => {
        try {
            setLoading(true);
            const response = await menuAPI.getBySlug(slug);
            if (response.data?.menu) {
                setMenu(response.data.menu);
                setIsFavorite(response.data.menu.is_favorite || false);
            } else {
                setError('Item not found');
            }
        } catch (err) {
            setError('Network Error');
        } finally {
            setLoading(false);
        }
    };
    const handleAddToCart = async () => {
        if (!menu) return;
        try {
            setAddingToCart(true);
            await cartAPI.add(menu.id, quantity);
            navigation.navigate('Cart');
        } catch (err) { } finally {
            setAddingToCart(false);
        }
    };
    const handleToggleFavorite = async () => {
        if (!menu) return;
        try {
            await favoritesAPI.toggle(menu.id);
            setIsFavorite(!isFavorite);
        } catch (err) { }
    };
    const formatPrice = (price) => {
        if (price == null) return 'Rp 0';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(price);
    };
    if (loading) {
        return (
            <View style={[styles.center, { backgroundColor: colors.background }]}>
                <StatusBar barStyle={isDarkMode ? "light-content" : "dark-content"} />
                <ActivityIndicator size="large" color={colors.primary} />
            </View>
        );
    }
    if (error || !menu) {
        return (
            <View style={[styles.center, { backgroundColor: colors.background }]}>
                <StatusBar barStyle={isDarkMode ? "light-content" : "dark-content"} />
                <Text style={{ color: colors.text }}>{error || 'Unavailable'}</Text>
                <TouchableOpacity onPress={() => navigation.goBack()} style={{ marginTop: 20 }}>
                    <Text style={{ color: colors.primary }}>Go Back</Text>
                </TouchableOpacity>
            </View>
        );
    }
    return (
        <View style={[styles.container, { backgroundColor: colors.background }]}>
            <StatusBar barStyle="light-content" backgroundColor="transparent" translucent />
            {}
            <View style={styles.imageContainer}>
                <Image
                    source={{ uri: menu.image_url || 'https://via.placeholder.com/800' }}
                    style={styles.image}
                    resizeMode="cover"
                />
                <LinearGradient
                    colors={['rgba(0,0,0,0.4)', 'transparent', isDarkMode ? 'rgba(0,0,0,0.9)' : 'rgba(255,255,255,0.9)', isDarkMode ? '#000' : colors.background]}
                    style={styles.gradientOverlay}
                />
            </View>
            {}
            <TouchableOpacity
                style={[styles.floatingBtn, { top: 50, left: 20 }]}
                onPress={() => navigation.goBack()}
            >
                <Icon name="arrow-back" size={24} color={colors.white} />
            </TouchableOpacity>
            <TouchableOpacity
                style={[styles.floatingBtn, { top: 50, right: 20 }]}
                onPress={handleToggleFavorite}
            >
                <Icon name={isFavorite ? "heart" : "heart-outline"} size={24} color={isFavorite ? colors.error : colors.white} />
            </TouchableOpacity>
            {}
            <ScrollView
                showsVerticalScrollIndicator={false}
                contentContainerStyle={styles.scrollContent}
            >
                <View style={styles.contentSpacer} />
                <View style={[styles.contentBody, { backgroundColor: colors.background }]}>
                    {}
                    <View style={[styles.glassLine, { backgroundColor: isDarkMode ? '#333' : '#CCC' }]} />
                    <Text style={styles.category}>{menu.category || 'SIGNATURE'}</Text>
                    <Text style={[styles.title, { color: colors.text }]}>{menu.name}</Text>
                    <Text style={[styles.price, { color: colors.textSecondary }]}>{formatPrice(menu.price)}</Text>
                    <View style={[styles.separator, { backgroundColor: colors.border }]} />
                    <Text style={[styles.sectionHeader, { color: colors.textSecondary }]}>{t('description').toUpperCase()}</Text>
                    <Text style={[styles.description, { color: colors.text }]}>
                        {menu.description || 'A masterpiece of culinary art, prepared with passion and the finest ingredients to delight your senses.'}
                    </Text>
                    <View style={{ height: 100 }} />
                </View>
            </ScrollView>
            {}
            <View style={styles.bottomBarContainer}>
                <View style={[styles.bottomBarBlur, { backgroundColor: isDarkMode ? '#111' : '#FFF', borderColor: colors.border }]}>
                    <View style={styles.counterContainer}>
                        <TouchableOpacity onPress={() => quantity > 1 && setQuantity(q => q - 1)}>
                            <Icon name="remove-circle-outline" size={28} color={colors.text} />
                        </TouchableOpacity>
                        <Text style={[styles.counterText, { color: colors.text }]}>{quantity}</Text>
                        <TouchableOpacity onPress={() => quantity < 20 && setQuantity(q => q + 1)}>
                            <Icon name="add-circle-outline" size={28} color={colors.text} />
                        </TouchableOpacity>
                    </View>
                    <TouchableOpacity
                        style={styles.addToCartBtn}
                        onPress={handleAddToCart}
                        disabled={addingToCart}
                    >
                        {addingToCart ? (
                            <ActivityIndicator color={colors.black} />
                        ) : (
                            <Text style={styles.addToCartText}>{t('addToCart')} • {formatPrice(menu.price * quantity)}</Text>
                        )}
                    </TouchableOpacity>
                </View>
            </View>
        </View>
    );
};
const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#000',
    },
    center: {
        flex: 1,
        backgroundColor: '#000',
        justifyContent: 'center',
        alignItems: 'center',
    },
    imageContainer: {
        position: 'absolute',
        top: 0,
        left: 0,
        width: width,
        height: height * 0.7, 
    },
    image: {
        width: '100%',
        height: '100%',
    },
    gradientOverlay: {
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
    },
    floatingBtn: {
        position: 'absolute',
        width: 44,
        height: 44,
        borderRadius: 22,
        backgroundColor: 'rgba(0,0,0,0.3)', 
        justifyContent: 'center',
        alignItems: 'center',
        zIndex: 50,
    },
    scrollContent: {
        paddingBottom: 0,
    },
    contentSpacer: {
        height: height * 0.6,
    },
    contentBody: {
        backgroundColor: '#000',
        borderTopLeftRadius: 32,
        borderTopRightRadius: 32,
        padding: 24,
        minHeight: height * 0.5,
    },
    glassLine: {
        width: 40,
        height: 4,
        backgroundColor: '#333',
        borderRadius: 2,
        alignSelf: 'center',
        marginBottom: 24,
    },
    category: {
        color: colors.primary,
        fontSize: 12,
        fontWeight: 'bold',
        letterSpacing: 2,
        marginBottom: 8,
        textTransform: 'uppercase',
    },
    title: {
        fontSize: 36,
        color: '#FFF',
        fontFamily: 'serif', 
        marginBottom: 8,
    },
    price: {
        fontSize: 24,
        color: '#CCC',
        fontWeight: '300',
        marginBottom: 24,
    },
    separator: {
        height: 1,
        backgroundColor: '#222',
        marginBottom: 24,
    },
    sectionHeader: {
        color: '#666',
        fontSize: 12,
        fontWeight: 'bold',
        letterSpacing: 1.5,
        marginBottom: 8,
    },
    description: {
        color: '#999',
        fontSize: 16,
        lineHeight: 26,
        fontWeight: '300',
    },
    bottomBarContainer: {
        position: 'absolute',
        bottom: 30,
        left: 20,
        right: 20,
    },
    bottomBarBlur: {
        flexDirection: 'row',
        backgroundColor: '#111',
        borderRadius: 30,
        padding: 8,
        borderColor: '#333',
        borderWidth: 1,
        alignItems: 'center',
    },
    counterContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: 16,
        gap: 12,
    },
    counterText: {
        color: '#FFF',
        fontSize: 18,
        fontWeight: 'bold',
        minWidth: 20,
        textAlign: 'center',
    },
    addToCartBtn: {
        flex: 1,
        backgroundColor: colors.primary,
        borderRadius: 22,
        height: 44,
        justifyContent: 'center',
        alignItems: 'center',
    },
    addToCartText: {
        color: '#000',
        fontWeight: 'bold',
        fontSize: 16,
    }
});
export default MenuDetailScreen;