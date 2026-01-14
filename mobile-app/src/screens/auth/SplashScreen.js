import React from 'react';
import { View, Text, ActivityIndicator, StyleSheet } from 'react-native';
import { colors } from '../../theme/colors';

const SplashScreen = () => {
    return (
        <View style={styles.container}>
            <Text style={styles.logo}>Culinaire</Text>
            <Text style={styles.tagline}>Premium Indonesian Cuisine</Text>
            <ActivityIndicator
                size="large"
                color={colors.accent}
                style={styles.loader}
            />
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.background,
        justifyContent: 'center',
        alignItems: 'center',
    },
    logo: {
        fontSize: 40,
        fontWeight: 'bold',
        color: colors.accent,
        fontFamily: 'serif',
    },
    tagline: {
        fontSize: 14,
        color: colors.textSecondary,
        marginTop: 8,
    },
    loader: {
        marginTop: 40,
    },
});

export default SplashScreen;
