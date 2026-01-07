import React from 'react';
import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    TouchableOpacity,
    Linking,
    SafeAreaView,
    Platform,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { colors, spacing, fontSize, borderRadius } from '../../theme/colors';

const ContactScreen = () => {
    const handleCall = () => {
        Linking.openURL('tel:+62318286500');
    };

    const handleEmail = () => {
        Linking.openURL('mailto:info@surabaya.telkomuniversity.ac.id');
    };

    const handleMaps = () => {
        const scheme = Platform.select({ ios: 'maps:0,0?q=', android: 'geo:0,0?q=' });
        const latLng = 'Universitas Telkom Surabaya, Jl. Ketintang No.156, Surabaya';
        const label = 'Culinaire Restaurant';
        const url = Platform.select({
            ios: `${scheme}${label}@${latLng}`,
            android: `${scheme}${latLng}(${label})`
        });
        Linking.openURL(url);
    };

    return (
        <SafeAreaView style={styles.container}>
            <ScrollView showsVerticalScrollIndicator={false}>
                {/* Hero Section */}
                <View style={styles.heroSection}>
                    <Text style={styles.heroPretitle}>GET IN TOUCH</Text>
                    <Text style={styles.heroTitle}>Let's Start a Conversation</Text>
                    <Text style={styles.heroSubtitle}>We invite you to experience the extraordinary.</Text>
                    <View style={styles.separator} />
                </View>

                {/* Contact Options */}
                <View style={styles.section}>
                    <TouchableOpacity style={styles.contactCard} onPress={handleMaps}>
                        <View style={styles.iconContainer}>
                            <Text style={styles.number}>01</Text>
                        </View>
                        <View style={styles.cardContent}>
                            <Text style={styles.cardTitle}>Visit Us</Text>
                            <Text style={styles.cardText}>Jl. Ketintang No.156, Surabaya</Text>
                            <Text style={styles.cardText}>East Java, Indonesia 60231</Text>
                        </View>
                        <Icon name="chevron-forward" size={24} color={colors.accent} />
                    </TouchableOpacity>

                    <TouchableOpacity style={styles.contactCard} onPress={handleCall}>
                        <View style={styles.iconContainer}>
                            <Text style={styles.number}>02</Text>
                        </View>
                        <View style={styles.cardContent}>
                            <Text style={styles.cardTitle}>Call Us</Text>
                            <Text style={styles.cardText}>+62 31 828 6500</Text>
                            <Text style={styles.cardTextSub}>Mon - Sun, 08:00 - 20:00</Text>
                        </View>
                        <Icon name="chevron-forward" size={24} color={colors.accent} />
                    </TouchableOpacity>

                    <TouchableOpacity style={styles.contactCard} onPress={handleEmail}>
                        <View style={styles.iconContainer}>
                            <Text style={styles.number}>03</Text>
                        </View>
                        <View style={styles.cardContent}>
                            <Text style={styles.cardTitle}>Write Us</Text>
                            <Text style={styles.cardText}>info@surabaya.telkomuniversity.ac.id</Text>
                        </View>
                        <Icon name="chevron-forward" size={24} color={colors.accent} />
                    </TouchableOpacity>
                </View>

                {/* Map Placeholder */}
                <View style={styles.mapSection}>
                    <TouchableOpacity style={styles.mapButton} onPress={handleMaps}>
                        <Icon name="map" size={32} color={colors.accent} />
                        <Text style={styles.mapButtonText}>Open in Maps</Text>
                    </TouchableOpacity>
                </View>

                {/* Send Message Form Placeholder */}
                <View style={styles.formSection}>
                    <Text style={styles.formTitle}>Send a Message</Text>
                    <Text style={styles.formSubtitle}>Your feedback and enquiries are paramount to us.</Text>
                    {/* Form fields would go here - placeholder for now */}
                    <View style={styles.inputPlaceholder}><Text style={styles.placeholderText}>Your Name</Text></View>
                    <View style={styles.inputPlaceholder}><Text style={styles.placeholderText}>Email Address</Text></View>
                    <View style={styles.inputPlaceholder}><Text style={styles.placeholderText}>Subject</Text></View>
                    <View style={[styles.inputPlaceholder, { height: 100 }]}><Text style={styles.placeholderText}>Message</Text></View>

                    <TouchableOpacity style={styles.submitButton}>
                        <Text style={styles.submitButtonText}>SEND MESSAGE</Text>
                    </TouchableOpacity>
                </View>

            </ScrollView>
        </SafeAreaView>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#111',
    },
    heroSection: {
        padding: spacing.xl,
        paddingTop: spacing.xxl * 2,
        backgroundColor: '#111',
    },
    heroPretitle: {
        fontSize: 12,
        color: colors.accent,
        letterSpacing: 3,
        marginBottom: spacing.md,
        fontWeight: 'bold',
        textTransform: 'uppercase',
    },
    heroTitle: {
        fontSize: 42,
        fontWeight: 'bold',
        color: colors.text,
        marginBottom: spacing.md,
        fontFamily: 'serif',
        lineHeight: 48,
    },
    heroSubtitle: {
        fontSize: 16,
        color: colors.textSecondary,
        fontWeight: '300',
        marginBottom: spacing.lg,
    },
    separator: {
        width: 50,
        height: 2,
        backgroundColor: colors.accent,
        opacity: 0.5,
    },
    section: {
        padding: spacing.xl,
        backgroundColor: 'rgba(255,255,255,0.02)',
    },
    contactCard: {
        flexDirection: 'row',
        alignItems: 'center',
        padding: spacing.lg,
        marginBottom: spacing.lg,
        borderBottomWidth: 1,
        borderBottomColor: 'rgba(255,255,255,0.1)',
    },
    iconContainer: {
        marginRight: spacing.lg,
    },
    number: {
        fontSize: 24,
        color: colors.accent,
        fontFamily: 'serif',
        fontStyle: 'italic',
        opacity: 0.5,
    },
    cardContent: {
        flex: 1,
    },
    cardTitle: {
        fontSize: 20,
        color: colors.text,
        marginBottom: spacing.xs,
        fontFamily: 'serif',
    },
    cardText: {
        fontSize: 14,
        color: colors.textSecondary,
    },
    cardTextSub: {
        fontSize: 14,
        color: colors.textSecondary,
        marginTop: 2,
    },
    mapSection: {
        height: 200,
        backgroundColor: '#1a1a1a',
        justifyContent: 'center',
        alignItems: 'center',
        borderTopWidth: 1,
        borderBottomWidth: 1,
        borderColor: 'rgba(255,255,255,0.1)',
        marginBottom: spacing.xl,
    },
    mapButton: {
        alignItems: 'center',
    },
    mapButtonText: {
        color: colors.accent,
        marginTop: spacing.sm,
        fontSize: 14,
        letterSpacing: 1,
        textTransform: 'uppercase',
    },
    formSection: {
        padding: spacing.xl,
        paddingBottom: spacing.xxl * 2,
    },
    formTitle: {
        fontSize: 24,
        color: colors.text,
        marginBottom: spacing.xs,
        fontFamily: 'serif',
        textAlign: 'center',
    },
    formSubtitle: {
        fontSize: 14,
        color: colors.textSecondary,
        marginBottom: spacing.xl,
        textAlign: 'center',
    },
    inputPlaceholder: {
        backgroundColor: 'transparent',
        borderBottomWidth: 1,
        borderBottomColor: '#333',
        paddingVertical: spacing.md,
        marginBottom: spacing.lg,
    },
    placeholderText: {
        color: '#555',
        fontSize: 14,
    },
    submitButton: {
        borderWidth: 1,
        borderColor: '#333',
        paddingVertical: 15,
        alignItems: 'center',
        marginTop: spacing.md,
    },
    submitButtonText: {
        color: colors.text,
        fontSize: 14,
        letterSpacing: 1,
        fontWeight: '600',
    },
});

export default ContactScreen;
