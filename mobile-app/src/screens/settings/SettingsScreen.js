import React from 'react';
import {
    View,
    Text,
    StyleSheet,
    TouchableOpacity,
    Switch,
    StatusBar,
    ScrollView
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { useSettings } from '../../context/SettingsContext';
import { APP_VERSION } from '../../config/app';
import { spacing, fontSize } from '../../theme/colors';

const SettingsScreen = ({ navigation }) => {
    const { isDarkMode, toggleTheme, language, toggleLanguage, colors, t } = useSettings();

    // Toggle Switch Custom Colors
    const trackColor = { false: "#767577", true: '#B8860B' };
    const thumbColor = isDarkMode ? '#D4AF37' : "#f4f3f4";

    return (
        <View style={[styles.container, { backgroundColor: colors.background }]}>
            <StatusBar
                barStyle={isDarkMode ? "light-content" : "dark-content"}
                backgroundColor={colors.background}
            />

            {/* Custom Header */}
            <View style={[styles.header, { backgroundColor: colors.background }]}>
                <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
                    <Icon name="arrow-back" size={24} color={colors.text} />
                </TouchableOpacity>
                <Text style={[styles.headerTitle, { color: colors.text }]}>
                    {language === 'en' ? 'Settings' : 'Pengaturan'}
                </Text>
                <View style={{ width: 40 }} />
            </View>

            <ScrollView contentContainerStyle={styles.content}>

                {/* Section: Appearance */}
                <Text style={styles.sectionHeader}>
                    {language === 'en' ? 'APPEARANCE' : 'TAMPILAN'}
                </Text>
                <View style={[styles.row, { borderBottomColor: colors.border }]}>
                    <View style={styles.rowLeft}>
                        <View style={styles.iconContainer}>
                            <Icon name="moon-outline" size={20} color={colors.text} />
                        </View>
                        <Text style={[styles.rowLabel, { color: colors.text }]}>
                            {language === 'en' ? 'Dark Mode' : 'Mode Gelap'}
                        </Text>
                    </View>
                    <Switch
                        trackColor={trackColor}
                        thumbColor={thumbColor}
                        ios_backgroundColor="#3e3e3e"
                        onValueChange={toggleTheme}
                        value={isDarkMode}
                    />
                </View>

                {/* Section: Preferences */}
                <Text style={styles.sectionHeader}>
                    {language === 'en' ? 'PREFERENCES' : 'PREFERENSI'}
                </Text>
                <TouchableOpacity
                    style={[styles.row, { borderBottomColor: colors.border }]}
                    onPress={toggleLanguage}
                    activeOpacity={0.7}
                >
                    <View style={styles.rowLeft}>
                        <View style={styles.iconContainer}>
                            <Icon name="language-outline" size={20} color={colors.text} />
                        </View>
                        <Text style={[styles.rowLabel, { color: colors.text }]}>
                            {language === 'en' ? 'Language' : 'Bahasa'}
                        </Text>
                    </View>
                    <View style={styles.rowRight}>
                        <Text style={[styles.valueText, { color: colors.textSecondary }]}>
                            {language === 'en' ? 'English' : 'Bahasa Indonesia'}
                        </Text>
                        <Icon name="chevron-forward" size={16} color={colors.textSecondary} />
                    </View>
                </TouchableOpacity>

                {/* Section: About */}
                <Text style={styles.sectionHeader}>
                    {language === 'en' ? 'ABOUT' : 'TENTANG'}
                </Text>
                <View style={[styles.row, { borderBottomColor: colors.border }]}>
                    <View style={styles.rowLeft}>
                        <View style={styles.iconContainer}>
                            <Icon name="information-circle-outline" size={20} color={colors.text} />
                        </View>
                        <Text style={[styles.rowLabel, { color: colors.text }]}>
                            {language === 'en' ? 'Version' : 'Versi'}
                        </Text>
                    </View>
                    <Text style={[styles.valueText, { color: colors.textSecondary }]}>
                        v{APP_VERSION} (Luxury Build)
                    </Text>
                </View>

            </ScrollView>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
    },
    header: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        paddingHorizontal: spacing.lg,
        paddingTop: 50,
        paddingBottom: spacing.lg,
    },
    backBtn: {
        width: 40,
        height: 40,
        justifyContent: 'center',
        alignItems: 'flex-start',
    },
    headerTitle: {
        fontSize: fontSize.lg,
        fontWeight: 'bold',
        letterSpacing: 1,
        textTransform: 'uppercase',
    },
    content: {
        padding: spacing.lg,
    },
    sectionHeader: {
        color: '#D4AF37', // Always Gold
        fontSize: 12,
        fontWeight: 'bold',
        letterSpacing: 1.5,
        marginTop: spacing.xl,
        marginBottom: spacing.md,
    },
    row: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        paddingVertical: spacing.md,
        borderBottomWidth: 1,
        marginBottom: spacing.xs,
    },
    rowLeft: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    rowRight: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: 8,
    },
    iconContainer: {
        width: 32,
        alignItems: 'center',
        marginRight: spacing.sm,
    },
    rowLabel: {
        fontSize: 16,
        fontWeight: '400',
    },
    valueText: {
        fontSize: 14,
    }
});

export default SettingsScreen;
