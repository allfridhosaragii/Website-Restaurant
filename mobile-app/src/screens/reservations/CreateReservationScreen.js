import React, { useState } from 'react';
import {
    View,
    Text,
    ScrollView,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    Alert,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { colors, spacing, fontSize, borderRadius } from '../../theme/colors';
import { reservationAPI } from '../../api/client';
import { useSettings } from '../../context/SettingsContext';

const CreateReservationScreen = ({ navigation }) => {
    const { isDarkMode, colors, t } = useSettings();
    const [date, setDate] = useState('');
    const [time, setTime] = useState('');
    const [guests, setGuests] = useState('2');
    const [notes, setNotes] = useState('');
    const [loading, setLoading] = useState(false);

    const handleSubmit = async () => {
        if (!date || !time || !guests) {
            Alert.alert('Error', 'Mohon lengkapi semua field');
            return;
        }

        try {
            setLoading(true);
            await reservationAPI.create({
                date,
                time,
                guests: parseInt(guests),
                notes,
            });
            Alert.alert(
                t('success'),
                'Reservation created successfully',
                [{ text: 'OK', onPress: () => navigation.goBack() }]
            );
        } catch (error) {
            Alert.alert('Error', 'Failed to create reservation');
        } finally {
            setLoading(false);
        }
    };

    const guestOptions = ['1', '2', '3', '4', '5', '6', '7', '8'];

    return (
        <ScrollView style={[styles.container, { backgroundColor: colors.background }]}>
            {/* Date */}
            <View style={styles.inputGroup}>
                <Text style={[styles.label, { color: colors.text }]}>Date</Text>
                <View style={[styles.inputContainer, { backgroundColor: colors.surface, borderColor: colors.border }]}>
                    <Icon name="calendar-outline" size={20} color={colors.textSecondary} />
                    <TextInput
                        style={[styles.input, { color: colors.text }]}
                        placeholder="YYYY-MM-DD"
                        placeholderTextColor={colors.textSecondary}
                        value={date}
                        onChangeText={setDate}
                    />
                </View>
            </View>

            {/* Time */}
            <View style={styles.inputGroup}>
                <Text style={[styles.label, { color: colors.text }]}>Time</Text>
                <View style={[styles.inputContainer, { backgroundColor: colors.surface, borderColor: colors.border }]}>
                    <Icon name="time-outline" size={20} color={colors.textSecondary} />
                    <TextInput
                        style={[styles.input, { color: colors.text }]}
                        placeholder="HH:MM"
                        placeholderTextColor={colors.textSecondary}
                        value={time}
                        onChangeText={setTime}
                    />
                </View>
            </View>

            {/* Guests */}
            <View style={styles.inputGroup}>
                <Text style={[styles.label, { color: colors.text }]}>Guests</Text>
                <View style={styles.guestsContainer}>
                    {guestOptions.map((num) => (
                        <TouchableOpacity
                            key={num}
                            style={[
                                styles.guestOption,
                                { backgroundColor: colors.surface, borderColor: colors.border },
                                guests === num && { backgroundColor: colors.primary, borderColor: colors.primary },
                            ]}
                            onPress={() => setGuests(num)}
                        >
                            <Text
                                style={[
                                    styles.guestText,
                                    { color: colors.text },
                                    guests === num && { color: colors.black, fontWeight: 'bold' },
                                ]}
                            >
                                {num}
                            </Text>
                        </TouchableOpacity>
                    ))}
                </View>
            </View>

            {/* Notes */}
            <View style={styles.inputGroup}>
                <Text style={[styles.label, { color: colors.text }]}>Notes (Optional)</Text>
                <View style={[styles.inputContainer, styles.textAreaContainer, { backgroundColor: colors.surface, borderColor: colors.border }]}>
                    <TextInput
                        style={[styles.input, styles.textArea, { color: colors.text }]}
                        placeholder="Special requests..."
                        placeholderTextColor={colors.textSecondary}
                        value={notes}
                        onChangeText={setNotes}
                        multiline
                        numberOfLines={4}
                        textAlignVertical="top"
                    />
                </View>
            </View>

            {/* Submit Button */}
            <TouchableOpacity
                style={[styles.button, { backgroundColor: colors.primary }, loading && styles.buttonDisabled]}
                onPress={handleSubmit}
                disabled={loading}
            >
                {loading ? (
                    <ActivityIndicator color={colors.black} />
                ) : (
                    <Text style={[styles.buttonText, { color: colors.black }]}>Create Reservation</Text>
                )}
            </TouchableOpacity>
        </ScrollView>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.background,
        padding: spacing.lg,
    },
    inputGroup: {
        marginBottom: spacing.lg,
    },
    label: {
        fontSize: fontSize.md,
        fontWeight: '600',
        color: colors.text,
        marginBottom: spacing.sm,
    },
    inputContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: colors.surface,
        borderRadius: borderRadius.md,
        paddingHorizontal: spacing.md,
        borderWidth: 1,
        borderColor: colors.border,
    },
    input: {
        flex: 1,
        paddingVertical: spacing.md,
        paddingHorizontal: spacing.sm,
        color: colors.text,
        fontSize: fontSize.md,
    },
    textAreaContainer: {
        alignItems: 'flex-start',
    },
    textArea: {
        minHeight: 100,
        paddingTop: spacing.md,
    },
    guestsContainer: {
        flexDirection: 'row',
        flexWrap: 'wrap',
        gap: spacing.sm,
    },
    guestOption: {
        width: 48,
        height: 48,
        borderRadius: borderRadius.md,
        backgroundColor: colors.surface,
        justifyContent: 'center',
        alignItems: 'center',
        borderWidth: 1,
        borderColor: colors.border,
    },
    guestOptionActive: {
        backgroundColor: colors.accent,
        borderColor: colors.accent,
    },
    guestText: {
        fontSize: fontSize.lg,
        color: colors.text,
        fontWeight: '600',
    },
    guestTextActive: {
        color: colors.background,
    },
    button: {
        backgroundColor: colors.accent,
        paddingVertical: spacing.md,
        borderRadius: borderRadius.md,
        alignItems: 'center',
        marginTop: spacing.md,
    },
    buttonDisabled: {
        opacity: 0.7,
    },
    buttonText: {
        color: colors.background,
        fontSize: fontSize.lg,
        fontWeight: '600',
    },
});

export default CreateReservationScreen;
