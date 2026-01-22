import React from 'react';
import { View, Text, ScrollView, StyleSheet, Image, Dimensions } from 'react-native';
import { colors } from '../theme/colors';
const { width } = Dimensions.get('window');
const PROMOS = [
    { id: 1, title: 'Diskon 30% Hari Ini!', desc: 'Untuk menu pasta & steak', color: '#800020' },
    { id: 2, title: 'Buy 1 Get 1 Free', desc: 'Minuman dingin spesial', color: '#1A1A1A' },
    { id: 3, title: 'Paket Hemat Keluarga', desc: 'Mulai dari Rp 150rb', color: '#D4AF37' },
];
const PromoBanner = () => {
    return (
        <View style={styles.container}>
            <Text style={styles.sectionTitle}>Spesial Untukmu</Text>
            <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.scrollContent}>
                {PROMOS.map((item) => (
                    <View key={item.id} style={[styles.card, { backgroundColor: item.color }]}>
                        <View style={styles.content}>
                            <Text style={styles.title}>{item.title}</Text>
                            <Text style={styles.desc}>{item.desc}</Text>
                            <View style={styles.badge}>
                                <Text style={styles.badgeText}>Klaim</Text>
                            </View>
                        </View>
                        {}
                        <View style={styles.circle} />
                    </View>
                ))}
            </ScrollView>
        </View>
    );
};
const styles = StyleSheet.create({
    container: {
        marginBottom: 20,
    },
    sectionTitle: {
        paddingHorizontal: 20,
        fontSize: 18,
        fontWeight: 'bold',
        color: colors.text,
        marginBottom: 10,
    },
    scrollContent: {
        paddingLeft: 20,
        paddingRight: 10,
    },
    card: {
        width: width * 0.75,
        height: 140,
        borderRadius: 16,
        marginRight: 10,
        padding: 20,
        justifyContent: 'center',
        overflow: 'hidden',
    },
    content: {
        zIndex: 2,
        maxWidth: '70%',
    },
    title: {
        color: '#FFF',
        fontSize: 18,
        fontWeight: 'bold',
        marginBottom: 4,
    },
    desc: {
        color: 'rgba(255,255,255,0.8)',
        fontSize: 12,
        marginBottom: 12,
    },
    badge: {
        backgroundColor: '#FFF',
        alignSelf: 'flex-start',
        paddingHorizontal: 12,
        paddingVertical: 6,
        borderRadius: 20,
    },
    badgeText: {
        color: '#000',
        fontSize: 10,
        fontWeight: 'bold',
    },
    circle: {
        position: 'absolute',
        right: -30,
        bottom: -30,
        width: 140,
        height: 140,
        borderRadius: 70,
        backgroundColor: 'rgba(255,255,255,0.1)',
    },
});
export default PromoBanner;