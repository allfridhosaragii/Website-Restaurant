import React from 'react';
import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    SafeAreaView,
} from 'react-native';
import { colors, spacing, fontSize, borderRadius } from '../../theme/colors';

const AboutScreen = () => {
    return (
        <SafeAreaView style={styles.container}>
            <ScrollView showsVerticalScrollIndicator={false}>
                {/* Hero Section */}
                <View style={styles.heroSection}>
                    <Text style={styles.heroTitle}>The Legacy</Text>
                    <View style={styles.separator} />
                    <Text style={styles.heroSubtitle}>OF CULINAIRE</Text>
                    <Text style={styles.scrollText}>SCROLL TO EXPLORE</Text>
                </View>

                {/* Chapter I: The Foundations */}
                <View style={styles.section}>
                    <Text style={styles.chapterLabel}>CHAPTER I</Text>
                    <Text style={styles.chapterTitle}>The Foundations</Text>

                    <TimelineItem
                        year="2009"
                        title="The Inception"
                        description="A humble beginning in the heart of Surabaya, redefining local flavors with modern techniques."
                    />
                    <TimelineItem
                        year="2010"
                        title="First Recognition"
                        description="Awarded 'Best Newcomer' by Surabaya Culinary Association."
                    />
                    <TimelineItem
                        year="2011"
                        title="Expansion"
                        description="Opened our second branch, introducing a wider range of fusion dishes."
                    />
                    <TimelineItem
                        year="2012"
                        title="Going Global"
                        description="Featured in international food magazines, attracting visitors from around the globe."
                    />
                </View>

                {/* Chapter II: The Golden Era */}
                <View style={[styles.section, styles.darkerSection]}>
                    <Text style={styles.chapterLabel}>CHAPTER II</Text>
                    <Text style={styles.chapterTitle}>The Golden Era</Text>
                    <Text style={styles.sectionSubtitle}>A period of unprecedented growth and culinary innovation.</Text>

                    <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.horizontalScroll}>
                        <YearCard year="2013" title="Michelin Star" desc="Received our first Michelin Star for exceptional cuisine." />
                        <YearCard year="2014" title="New Heights" desc="Launched our signature 12-course tasting menu." />
                        <YearCard year="2015" title="Sustainability" desc="Available fully sustainable sourcing for all ingredients." />
                        <YearCard year="2016" title="Innovation" desc="Pioneered molecular gastronomy in East Java." />
                        <YearCard year="2017" title="The Summit" desc="Voted Top 10 Restaurants in Indonesia." />
                    </ScrollView>
                </View>

                {/* Chapter III: Resilience */}
                <View style={styles.section}>
                    <Text style={styles.chapterLabel}>CHAPTER III</Text>
                    <Text style={styles.chapterTitle}>Resilience</Text>

                    <SpotlightItem year="2018" title="Reinvention" desc="Revamped our entire menu to focus on heritage recipes." />
                    <SpotlightItem year="2019" title="Community" desc="Launched charity programs to support local farmers." />
                    <SpotlightItem year="2020" title="Adaptation" desc="Thrived through global challenges with innovative delivery experiences." />
                    <SpotlightItem year="2021" title="Digital Leaps" desc="Integrated state-of-the-art tech for seamless dining." />
                </View>

                {/* Chapter IV: The Vision */}
                <View style={[styles.section, styles.darkerSection]}>
                    <Text style={styles.chapterLabel}>CHAPTER IV</Text>
                    <Text style={styles.chapterTitle}>The Vision</Text>

                    <VisionCard year="2022" title="New Horizons" desc="Expanding our footprint to Bali and Jakarta." />
                    <VisionCard year="2023" title="Tech & Taste" desc="AI-driven menu curation for personalized dining." />
                    <VisionCard year="2024" title="Global Brand" desc="Establishing Culinaire as a global luxury dining brand." />
                    <VisionCard year="2025" title="The Future" desc="Continuing to push the boundaries of culinary art." />
                </View>

                <View style={styles.footer}>
                    <Text style={styles.footerText}>EST. 2009 • CULINAIRE</Text>
                </View>
            </ScrollView>
        </SafeAreaView>
    );
};

const TimelineItem = ({ year, title, description }) => (
    <View style={styles.timelineItem}>
        <Text style={styles.yearLabel}>{year}</Text>
        <View style={styles.timelineContent}>
            <Text style={styles.itemTitle}>{title}</Text>
            <Text style={styles.itemDesc}>{description}</Text>
        </View>
    </View>
);

const YearCard = ({ year, title, desc }) => (
    <View style={styles.yearCard}>
        <Text style={styles.cardYear}>{year}</Text>
        <Text style={styles.cardTitle}>{title}</Text>
        <Text style={styles.cardDesc}>{desc}</Text>
    </View>
);

const SpotlightItem = ({ year, title, desc }) => (
    <View style={styles.spotlightItem}>
        <Text style={styles.spotlightYear}>{year}</Text>
        <View>
            <Text style={styles.spotlightTitle}>{title}</Text>
            <Text style={styles.spotlightDesc}>{desc}</Text>
        </View>
    </View>
);

const VisionCard = ({ year, title, desc }) => (
    <View style={styles.visionCard}>
        <Text style={styles.visionYear}>{year}</Text>
        <Text style={styles.visionTitle}>{title}</Text>
        <Text style={styles.visionDesc}>{desc}</Text>
    </View>
);

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.background,
    },
    heroSection: {
        height: 400,
        justifyContent: 'center',
        alignItems: 'center',
        backgroundColor: '#050608',
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
    },
    heroTitle: {
        fontSize: 48,
        fontWeight: 'bold',
        color: colors.text,
        letterSpacing: -1,
    },
    separator: {
        width: 60,
        height: 2,
        backgroundColor: colors.accent,
        marginVertical: spacing.md,
    },
    heroSubtitle: {
        fontSize: 14,
        color: colors.accent,
        letterSpacing: 4,
        fontFamily: 'monospace',
    },
    scrollText: {
        position: 'absolute',
        bottom: 30,
        fontSize: 10,
        color: colors.textHint,
        letterSpacing: 2,
    },
    section: {
        padding: spacing.xl,
        backgroundColor: '#050608',
    },
    darkerSection: {
        backgroundColor: '#0A0C10',
    },
    chapterLabel: {
        fontSize: 12,
        color: colors.accent,
        letterSpacing: 2,
        textAlign: 'center',
        marginBottom: spacing.xs,
        fontFamily: 'monospace',
    },
    chapterTitle: {
        fontSize: 32,
        color: colors.text,
        textAlign: 'center',
        marginBottom: spacing.xl,
    },
    sectionSubtitle: {
        color: colors.textSecondary,
        textAlign: 'center',
        marginBottom: spacing.xl,
        paddingHorizontal: spacing.lg,
    },
    timelineItem: {
        marginBottom: spacing.xl,
        borderLeftWidth: 1,
        borderLeftColor: colors.border,
        paddingLeft: spacing.lg,
        marginLeft: spacing.lg,
    },
    yearLabel: {
        fontSize: 36,
        color: 'rgba(212, 175, 55, 0.3)',
        fontWeight: 'bold',
        position: 'absolute',
        left: -20,
        top: -20,
    },
    timelineContent: {
        marginTop: spacing.md,
    },
    itemTitle: {
        fontSize: 18,
        color: colors.text,
        marginBottom: spacing.xs,
        fontWeight: '600',
    },
    itemDesc: {
        fontSize: 14,
        color: colors.textSecondary,
        lineHeight: 20,
    },
    horizontalScroll: {
        marginHorizontal: -spacing.xl,
        paddingHorizontal: spacing.xl,
    },
    yearCard: {
        width: 200,
        height: 250,
        backgroundColor: 'rgba(255,255,255,0.03)',
        borderRadius: borderRadius.md,
        padding: spacing.lg,
        marginRight: spacing.md,
        borderWidth: 1,
        borderColor: 'rgba(255,255,255,0.1)',
        justifyContent: 'center',
    },
    cardYear: {
        fontSize: 24,
        color: colors.accent,
        marginBottom: spacing.sm,
        fontWeight: 'bold',
    },
    cardTitle: {
        fontSize: 18,
        color: colors.text,
        marginBottom: spacing.sm,
    },
    cardDesc: {
        fontSize: 13,
        color: colors.textSecondary,
    },
    spotlightItem: {
        marginBottom: spacing.md,
        padding: spacing.md,
        borderLeftWidth: 2,
        borderLeftColor: 'rgba(255,255,255,0.1)',
        backgroundColor: 'rgba(255,255,255,0.02)',
    },
    spotlightYear: {
        fontSize: 16,
        color: colors.accent,
        marginBottom: spacing.xs,
        fontWeight: 'bold',
    },
    spotlightTitle: {
        fontSize: 16,
        color: colors.text,
        marginBottom: spacing.xxs,
    },
    spotlightDesc: {
        fontSize: 13,
        color: colors.textSecondary,
    },
    visionCard: {
        padding: spacing.lg,
        backgroundColor: '#111',
        borderRadius: borderRadius.lg,
        marginBottom: spacing.md,
        borderWidth: 1,
        borderColor: 'rgba(255,255,255,0.1)',
    },
    visionYear: {
        fontSize: 32,
        color: colors.accent,
        marginBottom: spacing.sm,
        fontFamily: 'serif',
    },
    visionTitle: {
        fontSize: 20,
        color: colors.text,
        marginBottom: spacing.xs,
    },
    visionDesc: {
        fontSize: 14,
        color: colors.textSecondary,
    },
    footer: {
        padding: spacing.xl,
        alignItems: 'center',
        borderTopWidth: 1,
        borderTopColor: colors.border,
        marginTop: spacing.xl,
    },
    footerText: {
        fontSize: 10,
        color: colors.textHint,
        letterSpacing: 2,
    },
});

export default AboutScreen;
