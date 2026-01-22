import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import LinearGradient from 'react-native-linear-gradient';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import { colors } from '../theme/colors';
const MemberCard = () => {
    return (
        <LinearGradient
            colors={['#1A1A1A', '#000000']}
            start={{ x: 0, y: 0 }}
            end={{ x: 1, y: 1 }}
            style={styles.card}
        >
            <View style={styles.header}>
                <View>
                    <Text style={styles.tierText}>PLATINUM MEMBER</Text>
                    <Text style={styles.nameText}>John Doe</Text>
                </View>
                <Icon name="crown" size={30} color={colors.secondary} />
            </View>
            <View style={styles.footer}>
                <View>
                    <Text style={styles.pointsLabel}>Poin Anda</Text>
                    <Text style={styles.pointsValue}>2,450 XP</Text>
                </View>
                <TouchableOpacity style={styles.button}>
                    <Text style={styles.buttonText}>Tukar Poin</Text>
                </TouchableOpacity>
            </View>
        </LinearGradient>
    );
};
const styles = StyleSheet.create({
    card: {
        margin: 20,
        borderRadius: 16,
        padding: 20,
        elevation: 5,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 2 },
        shadowOpacity: 0.25,
        shadowRadius: 3.84,
    },
    header: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: 20,
    },
    tierText: {
        color: colors.secondary,
        fontWeight: 'bold',
        letterSpacing: 1,
        fontSize: 12,
    },
    nameText: {
        color: '#FFF',
        fontSize: 20,
        fontWeight: 'bold',
        marginTop: 4,
    },
    footer: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'flex-end',
    },
    pointsLabel: {
        color: '#AAA',
        fontSize: 12,
    },
    pointsValue: {
        color: '#FFF',
        fontSize: 24,
        fontWeight: 'bold',
    },
    button: {
        backgroundColor: colors.secondary,
        paddingHorizontal: 16,
        paddingVertical: 8,
        borderRadius: 20,
    },
    buttonText: {
        color: '#000',
        fontWeight: 'bold',
        fontSize: 12,
    },
});
export default MemberCard;