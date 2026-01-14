import React from 'react';
import {
    View,
    TouchableOpacity,
    StyleSheet,
    Dimensions,
} from 'react-native';
import Animated, {
    useSharedValue,
    useAnimatedStyle,
    withSpring,
    withTiming,
    interpolate,
    Extrapolate,
} from 'react-native-reanimated';
import Icon from 'react-native-vector-icons/Ionicons';
import { colors, spacing, borderRadius } from '../theme/colors';

const { width } = Dimensions.get('window');
const TAB_WIDTH = (width - 48) / 4;

const AnimatedTouchable = Animated.createAnimatedComponent(TouchableOpacity);

const TabBarIcon = ({ route, isFocused, onPress, index, animatedIndex }) => {
    const getIconName = () => {
        switch (route.name) {
            case 'Home':
                return isFocused ? 'home' : 'home-outline';
            case 'Menu':
                return isFocused ? 'restaurant' : 'restaurant-outline';
            case 'Cart':
                return isFocused ? 'cart' : 'cart-outline';
            case 'Profile':
                return isFocused ? 'person' : 'person-outline';
            default:
                return 'ellipse';
        }
    };

    const animatedIconStyle = useAnimatedStyle(() => {
        const scale = interpolate(
            animatedIndex.value,
            [index - 1, index, index + 1],
            [1, 1.3, 1],
            Extrapolate.CLAMP
        );
        const translateY = interpolate(
            animatedIndex.value,
            [index - 1, index, index + 1],
            [0, -12, 0],
            Extrapolate.CLAMP
        );
        return {
            transform: [{ scale }, { translateY }],
        };
    });

    const animatedBgStyle = useAnimatedStyle(() => {
        const opacity = interpolate(
            animatedIndex.value,
            [index - 1, index, index + 1],
            [0, 1, 0],
            Extrapolate.CLAMP
        );
        const scale = interpolate(
            animatedIndex.value,
            [index - 1, index, index + 1],
            [0.5, 1, 0.5],
            Extrapolate.CLAMP
        );
        return {
            opacity,
            transform: [{ scale }],
        };
    });

    return (
        <AnimatedTouchable
            onPress={onPress}
            style={styles.tabButton}
            activeOpacity={0.7}
        >
            {/* Animated background glow */}
            <Animated.View style={[styles.iconBg, animatedBgStyle]} />

            {/* Icon */}
            <Animated.View style={animatedIconStyle}>
                <Icon
                    name={getIconName()}
                    size={26}
                    color={isFocused ? colors.accent : colors.textSecondary}
                />
            </Animated.View>
        </AnimatedTouchable>
    );
};

const FloatingTabBar = ({ state, descriptors, navigation }) => {
    const animatedIndex = useSharedValue(state.index);

    React.useEffect(() => {
        animatedIndex.value = withSpring(state.index, {
            damping: 15,
            stiffness: 150,
        });
    }, [state.index]);

    // Animated indicator style
    const indicatorStyle = useAnimatedStyle(() => {
        return {
            transform: [
                {
                    translateX: withSpring(animatedIndex.value * TAB_WIDTH, {
                        damping: 15,
                        stiffness: 150,
                    }),
                },
            ],
        };
    });

    return (
        <View style={styles.container}>
            {/* Floating bar */}
            <View style={styles.tabBar}>
                {/* Animated indicator */}
                <Animated.View style={[styles.indicator, indicatorStyle]}>
                    <View style={styles.indicatorInner} />
                </Animated.View>

                {/* Tab buttons */}
                {state.routes.map((route, index) => {
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

                    return (
                        <TabBarIcon
                            key={route.key}
                            route={route}
                            isFocused={isFocused}
                            onPress={onPress}
                            index={index}
                            animatedIndex={animatedIndex}
                        />
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
        left: 0,
        right: 0,
        alignItems: 'center',
        paddingHorizontal: spacing.md,
    },
    tabBar: {
        flexDirection: 'row',
        backgroundColor: colors.surface,
        borderRadius: borderRadius.xl,
        paddingVertical: spacing.sm,
        paddingHorizontal: spacing.xs,
        shadowColor: colors.black,
        shadowOffset: { width: 0, height: 8 },
        shadowOpacity: 0.3,
        shadowRadius: 16,
        elevation: 12,
        borderWidth: 1,
        borderColor: colors.border,
    },
    tabButton: {
        width: TAB_WIDTH,
        height: 56,
        justifyContent: 'center',
        alignItems: 'center',
    },
    iconBg: {
        position: 'absolute',
        width: 50,
        height: 50,
        borderRadius: 25,
        backgroundColor: `${colors.accent}20`,
    },
    indicator: {
        position: 'absolute',
        bottom: 6,
        left: spacing.xs,
        width: TAB_WIDTH,
        alignItems: 'center',
    },
    indicatorInner: {
        width: 4,
        height: 4,
        borderRadius: 2,
        backgroundColor: colors.accent,
    },
});

export default FloatingTabBar;
