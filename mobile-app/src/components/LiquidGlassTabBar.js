import React, { useEffect, useRef } from 'react';
import {
    View,
    TouchableOpacity,
    StyleSheet,
    Dimensions,
    Animated,
    Platform,
    Text,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { colors } from '../theme/colors';

const { width } = Dimensions.get('window');
const TAB_WIDTH = (width - 40) / 4; // 4 tabs, margin horizontal 20

const LiquidGlassTabBar = ({ state, descriptors, navigation }) => {
    const animatedValue = useRef(new Animated.Value(0)).current;

    useEffect(() => {
        Animated.spring(animatedValue, {
            toValue: state.index * TAB_WIDTH,
            useNativeDriver: true,
            friction: 12,
            tension: 60,
        }).start();
    }, [state.index]);

    return (
        <View style={styles.container}>
            <View style={styles.blurContainer}>
                {/* Active Indicator (Liquid Glow) */}
                <Animated.View
                    style={[
                        styles.activeIndicator,
                        {
                            transform: [{ translateX: animatedValue }],
                        },
                    ]}
                >
                    <View style={styles.glow} />
                    <View style={styles.indicatorCore} />
                </Animated.View>

                {state.routes.map((route, index) => {
                    const { options } = descriptors[route.key];
                    const isFocused = state.index === index;

                    const onPress = () => {
                        const event = navigation.emit({
                            type: 'tabPress',
                            target: route.key,
                            canPreventDefault: true,
                        });

                        if (!isFocused && !event.defaultPrevented) {
                            navigation.navigate(route.name);
                        }
                    };

                    // Icons per route
                    let iconName = 'home-outline';
                    if (route.name === 'Home') iconName = isFocused ? 'home' : 'home-outline';
                    else if (route.name === 'Menu') iconName = isFocused ? 'restaurant' : 'restaurant-outline';
                    else if (route.name === 'Cart') iconName = isFocused ? 'cart' : 'cart-outline';
                    else if (route.name === 'Profile') iconName = isFocused ? 'person' : 'person-outline';

                    return (
                        <TouchableOpacity
                            key={index}
                            onPress={onPress}
                            style={styles.tabButton}
                            activeOpacity={0.8}
                        >
                            <Icon
                                name={iconName}
                                size={24}
                                color={isFocused ? colors.accent : 'rgba(255,255,255,0.6)'}
                                style={[
                                    styles.icon,
                                    isFocused && styles.activeIcon
                                ]}
                            />
                            {isFocused && (
                                <Text style={styles.label}>
                                    {options.title}
                                </Text>
                            )}
                        </TouchableOpacity>
                    );
                })}
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        position: 'absolute',
        bottom: 20,
        left: 20,
        right: 20,
        alignItems: 'center',
    },
    blurContainer: {
        flexDirection: 'row',
        width: '100%',
        height: 70,
        borderRadius: 35,
        backgroundColor: 'rgba(20, 20, 20, 0.85)', // Dark Glass
        borderWidth: 1,
        borderColor: 'rgba(255, 255, 255, 0.1)',
        alignItems: 'center',
        justifyContent: 'space-around',
        overflow: 'hidden',
        // Shadow for depth
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 10 },
        shadowOpacity: 0.5,
        shadowRadius: 20,
        elevation: 15,
    },
    activeIndicator: {
        position: 'absolute',
        left: 0,
        top: 0,
        height: '100%',
        width: TAB_WIDTH,
        justifyContent: 'center',
        alignItems: 'center',
        zIndex: -1,
    },
    glow: {
        width: 50,
        height: 50,
        borderRadius: 25,
        backgroundColor: 'rgba(212, 175, 55, 0.15)', // Accent Gold Glow
        position: 'absolute',
    },
    indicatorCore: {
        width: 6,
        height: 6,
        borderRadius: 3,
        backgroundColor: colors.accent,
        position: 'absolute',
        bottom: 15,
    },
    tabButton: {
        flex: 1,
        height: '100%',
        justifyContent: 'center',
        alignItems: 'center',
    },
    icon: {
        marginBottom: 2,
    },
    activeIcon: {
        marginBottom: 5, // Lift up slightly
        shadowColor: colors.accent,
        shadowOffset: { width: 0, height: 0 },
        shadowOpacity: 0.8,
        shadowRadius: 10,
    },
    label: {
        fontSize: 10,
        color: colors.accent,
        fontWeight: '600',
        position: 'absolute',
        bottom: 12,
    },
});

export default LiquidGlassTabBar;
