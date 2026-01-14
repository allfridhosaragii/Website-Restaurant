import React, { useEffect, useRef } from 'react';
import {
    View,
    Text,
    TouchableOpacity,
    StyleSheet,
    Animated,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { useCart } from '../context/CartContext';

const FloatingCartButton = ({ onPress }) => {
    const { cartItemCount } = useCart();
    const scaleAnim = useRef(new Animated.Value(0)).current;
    const badgeScaleAnim = useRef(new Animated.Value(1)).current;
    const prevCount = useRef(cartItemCount);

    // Entrance animation when cart becomes non-empty
    useEffect(() => {
        if (cartItemCount > 0) {
            Animated.spring(scaleAnim, {
                toValue: 1,
                tension: 100,
                friction: 8,
                useNativeDriver: true,
            }).start();
        } else {
            Animated.timing(scaleAnim, {
                toValue: 0,
                duration: 200,
                useNativeDriver: true,
            }).start();
        }
    }, [cartItemCount > 0]);

    // Badge pulse when count changes
    useEffect(() => {
        if (cartItemCount !== prevCount.current && cartItemCount > 0) {
            Animated.sequence([
                Animated.timing(badgeScaleAnim, {
                    toValue: 1.3,
                    duration: 150,
                    useNativeDriver: true,
                }),
                Animated.spring(badgeScaleAnim, {
                    toValue: 1,
                    friction: 4,
                    useNativeDriver: true,
                }),
            ]).start();
            prevCount.current = cartItemCount;
        }
    }, [cartItemCount]);

    if (cartItemCount === 0) {
        return null;
    }

    return (
        <Animated.View
            style={[
                styles.container,
                {
                    transform: [
                        { scale: scaleAnim },
                        {
                            rotate: scaleAnim.interpolate({
                                inputRange: [0, 1],
                                outputRange: ['-180deg', '0deg'],
                            }),
                        },
                    ],
                },
            ]}
        >
            <TouchableOpacity
                style={styles.button}
                onPress={onPress}
                activeOpacity={0.8}
            >
                <Icon name="cart" size={26} color="#FFF" />

                {/* Badge */}
                <Animated.View
                    style={[
                        styles.badge,
                        { transform: [{ scale: badgeScaleAnim }] },
                    ]}
                >
                    <Text style={styles.badgeText}>
                        {cartItemCount > 99 ? '99+' : cartItemCount}
                    </Text>
                </Animated.View>
            </TouchableOpacity>
        </Animated.View>
    );
};

const styles = StyleSheet.create({
    container: {
        position: 'absolute',
        bottom: 100, // Above tab bar
        right: 16,
        zIndex: 30,
    },
    button: {
        width: 60,
        height: 60,
        borderRadius: 30,
        backgroundColor: '#8B1538',
        justifyContent: 'center',
        alignItems: 'center',
        elevation: 8,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.3,
        shadowRadius: 8,
    },
    badge: {
        position: 'absolute',
        top: -4,
        right: -4,
        minWidth: 24,
        height: 24,
        borderRadius: 12,
        backgroundColor: '#D4AF37',
        justifyContent: 'center',
        alignItems: 'center',
        paddingHorizontal: 6,
        borderWidth: 2,
        borderColor: '#FFF',
    },
    badgeText: {
        color: '#FFF',
        fontSize: 12,
        fontWeight: 'bold',
    },
});

export default FloatingCartButton;
