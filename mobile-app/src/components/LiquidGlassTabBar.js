import React, { useEffect, useState } from 'react';
import {
    View,
    Text,
    StyleSheet,
    Dimensions,
    TouchableOpacity
} from 'react-native';
import Animated, {
    useSharedValue,
    useAnimatedStyle,
    useAnimatedGestureHandler,
    withSpring,
    runOnJS,
    withTiming
} from 'react-native-reanimated';
import { PanGestureHandler } from 'react-native-gesture-handler';
import Icon from 'react-native-vector-icons/Ionicons';
import LinearGradient from 'react-native-linear-gradient';

const { width: SCREEN_WIDTH } = Dimensions.get('window');
const TAB_COUNT = 5;
const MARGIN_H = 12; // 12px margin left/right
const CONTAINER_WIDTH = Math.min(SCREEN_WIDTH - (MARGIN_H * 2), 400);
const TAB_WIDTH = CONTAINER_WIDTH / TAB_COUNT;
const BAR_HEIGHT = 72;

const SPRING_CONFIG = {
    damping: 15,
    mass: 1,
    stiffness: 120,
    overshootClamping: false,
    restDisplacementThreshold: 0.01,
    restSpeedThreshold: 2,
};

const COLORS = {
    brand: '#8B1538',
    activeIcon: '#8B1538',
    inactiveIcon: 'rgba(0,0,0,0.4)',
    pill: 'rgba(0,0,0,0.06)',
    bg: 'rgba(255, 255, 255, 0.95)',
};

const LiquidGlassTabBar = ({ state, descriptors, navigation }) => {
    // Shared Value for Pill Position X (UI Thread)
    const translateX = useSharedValue(state.index * TAB_WIDTH);
    const isDragging = useSharedValue(0);

    // Sync external state (e.g. Back button press)
    useEffect(() => {
        translateX.value = withSpring(state.index * TAB_WIDTH, SPRING_CONFIG);
    }, [state.index]);

    const handleNavigate = (index) => {
        const route = state.routes[index];
        const event = navigation.emit({
            type: 'tabPress',
            target: route.key,
            canPreventDefault: true,
        });

        if (state.index !== index && !event.defaultPrevented) {
            navigation.navigate(route.name);
        }
    };

    // UI Thread Gesture Handler
    const gestureHandler = useAnimatedGestureHandler({
        onStart: (_, ctx) => {
            isDragging.value = 1;
            ctx.startX = translateX.value;
        },
        onActive: (event, ctx) => {
            let nextPos = ctx.startX + event.translationX;
            // Clamp
            if (nextPos < 0) nextPos = 0;
            if (nextPos > CONTAINER_WIDTH - TAB_WIDTH) nextPos = CONTAINER_WIDTH - TAB_WIDTH;

            translateX.value = nextPos;
        },
        onEnd: (event, _) => {
            isDragging.value = 0;
            // Snap to nearest index
            const targetIndex = Math.round(translateX.value / TAB_WIDTH);
            const snapPos = targetIndex * TAB_WIDTH;

            translateX.value = withSpring(snapPos, {
                ...SPRING_CONFIG,
                velocity: event.velocityX
            });

            // Navigate on JS Thread
            runOnJS(handleNavigate)(targetIndex);
        },
    });

    const pillStyle = useAnimatedStyle(() => {
        return {
            transform: [
                { translateX: translateX.value },
                { scale: withTiming(isDragging.value ? 0.9 : 1, { duration: 100 }) }
            ],
        };
    });

    const handleTap = (index) => {
        // Instant feedback for tap
        translateX.value = withSpring(index * TAB_WIDTH, SPRING_CONFIG);
        handleNavigate(index);
    };

    const getTabConfig = (routeName, isFocused) => {
        const configs = {
            Home: { icon: 'home', label: 'Beranda' },
            Menu: { icon: 'fast-food', label: 'Menu' },
            Reservations: { icon: 'calendar', label: 'Reservasi' },
            Orders: { icon: 'time', label: 'Histori' },
            Profile: { icon: 'person', label: 'Profil' },
        };
        const cfg = configs[routeName];
        let iconName = cfg?.icon || 'square';
        if (!isFocused) iconName += '-outline';
        return { icon: iconName, label: cfg?.label || routeName };
    };

    return (
        <View style={styles.positionWrapper} pointerEvents="box-none">
            <View style={styles.containerShadow} />

            <View style={styles.mainContainer}>
                {/* 
                    activeOffsetX={[-10, 10]} means:
                    - Movement within -10px to 10px is IGNORED by PanHandler (passes to children TouchableOpacity)
                    - Movement > 10px or < -10px ACTIVATES PanHandler (Cancels children touches)
                    This enables pure Taps AND pure Drag!
                */}
                <PanGestureHandler
                    onGestureEvent={gestureHandler}
                    activeOffsetX={[-10, 10]}
                >
                    <Animated.View style={styles.glassBg}>
                        <Animated.View style={[styles.pill, pillStyle]} />

                        <View style={styles.tabsContainer}>
                            {state.routes.map((route, index) => {
                                const isFocused = state.index === index;
                                const { icon, label } = getTabConfig(route.name, isFocused);

                                return (
                                    <TouchableOpacity
                                        key={route.key}
                                        style={styles.tabBtn}
                                        onPress={() => handleTap(index)}
                                        activeOpacity={1}
                                    >
                                        <Icon
                                            name={icon}
                                            size={26}
                                            color={isFocused ? COLORS.activeIcon : COLORS.inactiveIcon}
                                            style={styles.iconStyle}
                                        />
                                        <Text style={[
                                            styles.label,
                                            {
                                                color: isFocused ? COLORS.activeIcon : COLORS.inactiveIcon,
                                                fontWeight: isFocused ? '600' : '400'
                                            }
                                        ]}>
                                            {label}
                                        </Text>
                                    </TouchableOpacity>
                                );
                            })}
                        </View>

                        <LinearGradient
                            colors={['rgba(255,255,255,0)', 'rgba(255,255,255,0.8)', 'rgba(255,255,255,0)']}
                            start={{ x: 0, y: 0 }} end={{ x: 1, y: 0 }}
                            style={styles.topBorder}
                            pointerEvents="none"
                        />
                    </Animated.View>
                </PanGestureHandler>
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    positionWrapper: {
        position: 'absolute',
        bottom: 20,
        width: SCREEN_WIDTH,
        alignItems: 'center',
        justifyContent: 'center',
    },
    mainContainer: {
        width: CONTAINER_WIDTH,
        height: BAR_HEIGHT,
        alignItems: 'center',
        justifyContent: 'center',
    },
    containerShadow: {
        position: 'absolute',
        width: CONTAINER_WIDTH - 20,
        height: BAR_HEIGHT - 20,
        bottom: 10,
        backgroundColor: 'black',
        borderRadius: 40,
        opacity: 0.15,
        transform: [{ scale: 1.1 }]
    },
    glassBg: {
        width: '100%',
        height: '100%',
        borderRadius: 36,
        backgroundColor: COLORS.bg,
        borderWidth: 1,
        borderColor: 'rgba(255,255,255,0.6)',
        overflow: 'hidden',
    },
    pill: {
        position: 'absolute',
        top: 6,
        left: 0,
        width: TAB_WIDTH,
        height: BAR_HEIGHT - 12,
        backgroundColor: COLORS.pill,
        borderRadius: 30,
        zIndex: 0,
    },
    tabsContainer: {
        flexDirection: 'row',
        width: '100%',
        height: '100%',
        zIndex: 1,
        position: 'absolute',
        top: 0, left: 0,
    },
    tabBtn: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
        paddingTop: 4,
    },
    iconStyle: {
        marginBottom: 2,
    },
    label: {
        fontSize: 10,
        letterSpacing: 0.2,
    },
    topBorder: {
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        height: 1,
        opacity: 0.5,
    }
});

export default LiquidGlassTabBar;
