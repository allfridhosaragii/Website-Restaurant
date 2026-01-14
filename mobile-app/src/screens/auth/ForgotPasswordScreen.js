import React, { useState, useEffect, useRef } from 'react';
import {
    View,
    Text,
    StyleSheet,
    TextInput,
    TouchableOpacity,
    Dimensions,
    KeyboardAvoidingView,
    Platform,
    ScrollView,
    Alert,
    ActivityIndicator
} from 'react-native';
import LinearGradient from 'react-native-linear-gradient';
import Icon from 'react-native-vector-icons/Ionicons';
import Animated, {
    useSharedValue,
    useAnimatedStyle,
    withSequence,
    withTiming,
    withSpring,
    FadeIn,
    FadeOut,
    SlideInRight,
    SlideOutLeft
} from 'react-native-reanimated';
import { useNavigation } from '@react-navigation/native';

const { width } = Dimensions.get('window');

// --- COLORS ---
const COLORS = {
    maroon: '#8B1538',
    maroonDark: '#6B0F2A',
    maroonDarker: '#4A0A1C',
    white: '#FFFFFF',
    textDark: '#1F2937',
    textLight: '#6B7280',
    border: '#E5E7EB',
    error: '#EF4444',
    success: '#10B981',
    blue: '#2563EB',
    gold: '#F59E0B',
    bg: '#FAF9F6' // Cream background
};

const ForgotPasswordScreen = () => {
    const navigation = useNavigation();

    // Steps: 1=Email, 2=OTP, 3=Reset
    const [step, setStep] = useState(1);

    // Data State
    const [email, setEmail] = useState('');
    const [otpInputs, setOtpInputs] = useState(['', '', '', '', '', '']);
    const [generatedOTP, setGeneratedOTP] = useState(null);
    const [timeLeft, setTimeLeft] = useState(600); // 10 mins
    const [newPass, setNewPass] = useState('');
    const [confirmPass, setConfirmPass] = useState('');
    const [loading, setLoading] = useState(false);

    // UI State
    const [showPass, setShowPass] = useState(false);
    const [showConfirm, setShowConfirm] = useState(false);

    // Refs
    const otpRefs = useRef([]);

    // Shake Animation for Error
    const shakeOffset = useSharedValue(0);
    const triggerShake = () => {
        shakeOffset.value = withSequence(
            withTiming(-10, { duration: 50 }),
            withTiming(10, { duration: 50 }),
            withTiming(-10, { duration: 50 }),
            withTiming(10, { duration: 50 }),
            withTiming(0, { duration: 50 })
        );
    };
    const shakeStyle = useAnimatedStyle(() => ({
        transform: [{ translateX: shakeOffset.value }]
    }));

    // --- STEP 1: EMAIL ---
    const handleSendOTP = () => {
        // Regex Validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email || !emailRegex.test(email)) {
            Alert.alert("Error", "Mohon masukkan email yang valid.");
            return;
        }

        setLoading(true);
        // Simulate API
        setTimeout(() => {
            setLoading(false);
            const demoOTP = Math.floor(100000 + Math.random() * 900000).toString();
            setGeneratedOTP(demoOTP);
            setStep(2);
            setTimeLeft(600); // Reset timer
            // In dev mode only
            console.log("DEV ONLY - OTP:", demoOTP);
            Alert.alert("OTP Terkirim", `Kode OTP (Dev Mode): ${demoOTP}`);
        }, 1500);
    };

    // --- STEP 2: OTP ---
    useEffect(() => {
        let timer;
        if (step === 2 && timeLeft > 0) {
            timer = setInterval(() => setTimeLeft(prev => prev - 1), 1000);
        }
        return () => clearInterval(timer);
    }, [step, timeLeft]);

    const formatTime = (secs) => {
        const m = Math.floor(secs / 60);
        const s = secs % 60;
        return `${m}:${s < 10 ? '0' : ''}${s}`;
    };

    const handleOtpChange = (text, index) => {
        if (!/^\d*$/.test(text)) return; // Only numbers

        const newOtp = [...otpInputs];
        newOtp[index] = text;
        setOtpInputs(newOtp);

        // Auto Focus Next
        if (text && index < 5) {
            otpRefs.current[index + 1].focus();
        }
    };

    const handleOtpBackspace = (key, index) => {
        if (key === 'Backspace' && !otpInputs[index] && index > 0) {
            otpRefs.current[index - 1].focus();
        }
    };

    const handleVerifyOTP = () => {
        const inputOTP = otpInputs.join('');
        if (inputOTP.length !== 6) return;

        setLoading(true);
        setTimeout(() => {
            setLoading(false);
            if (inputOTP === generatedOTP) {
                setStep(3);
            } else {
                triggerShake();
                Alert.alert("Error", "Kode OTP salah!");
                setOtpInputs(['', '', '', '', '', '']);
                otpRefs.current[0].focus();
            }
        }, 1000);
    };

    // --- STEP 3: RESET PASSWORD ---
    const getStrength = (pass) => {
        let s = 0;
        if (pass.length >= 8) s++;
        if (/[A-Z]/.test(pass) && /[a-z]/.test(pass)) s++;
        if (/\d/.test(pass)) s++;
        if (/[^a-zA-Z0-9]/.test(pass)) s++;
        return s;
    };
    const strength = getStrength(newPass);

    const handleResetPassword = () => {
        if (strength < 1) {
            Alert.alert("Password Lemah", "Password minimal 8 karakter.");
            return;
        }
        if (newPass !== confirmPass) {
            triggerShake();
            Alert.alert("Error", "Password konfirmasi tidak cocok.");
            return;
        }

        setLoading(true);
        setTimeout(() => {
            setLoading(false);
            Alert.alert("Sukses", "Password berhasil direset! Silakan login.");
            navigation.navigate('Login');
        }, 1500);
    };


    // --- RENDERERS ---

    const renderHeader = () => (
        <View style={styles.header}>
            <TouchableOpacity onPress={() => step === 1 ? navigation.goBack() : setStep(step - 1)} style={styles.backBtn}>
                <Icon name="arrow-back" size={24} color={COLORS.maroon} />
            </TouchableOpacity>
            <Text style={styles.headerTitle}>
                {step === 1 ? 'Lupa Password' : step === 2 ? 'Verifikasi OTP' : 'Reset Password'}
            </Text>
            <View style={{ width: 40 }} />
        </View>
    );

    const renderStep1 = () => (
        <Animated.View entering={FadeIn} leaving={FadeOut}>
            <View style={styles.iconContainer}>
                <LinearGradient colors={['#E0F2FE', '#BAE6FD']} style={styles.iconCircle}>
                    <Icon name="mail" size={48} color={COLORS.blue} />
                </LinearGradient>
            </View>
            <Text style={styles.mainTitle}>Lupa Password?</Text>
            <Text style={styles.subtitle}>Masukkan email Anda, kami akan mengirimkan kode OTP untuk reset password.</Text>

            <View style={styles.inputGroup}>
                <Text style={styles.label}>Email</Text>
                <View style={styles.inputWrapper}>
                    <Icon name="mail-outline" size={20} color={COLORS.textLight} style={styles.inputIcon} />
                    <TextInput
                        style={styles.input}
                        placeholder="email@example.com"
                        value={email}
                        onChangeText={setEmail}
                        autoCapitalize="none"
                        keyboardType="email-address"
                    />
                </View>
            </View>

            <View style={styles.infoBox}>
                <Text style={styles.infoText}>💡 <Text style={{ fontWeight: 'bold' }}>Catatan:</Text> Kode OTP berlaku selama 10 menit.</Text>
            </View>

            <TouchableOpacity style={styles.submitBtn} onPress={handleSendOTP} disabled={loading}>
                <LinearGradient colors={[COLORS.maroon, COLORS.maroonDark]} style={styles.btnGradient}>
                    {loading ? <ActivityIndicator color="white" /> : <Text style={styles.btnText}>Kirim Kode OTP</Text>}
                </LinearGradient>
            </TouchableOpacity>
        </Animated.View>
    );

    const renderStep2 = () => (
        <Animated.View entering={SlideInRight} leaving={SlideOutLeft}>
            <View style={styles.iconContainer}>
                <LinearGradient colors={['#DCFCE7', '#BBF7D0']} style={styles.iconCircle}>
                    <Icon name="shield-checkmark" size={48} color={COLORS.success} />
                </LinearGradient>
            </View>
            <Text style={styles.mainTitle}>Verifikasi OTP</Text>
            <Text style={styles.subtitle}>Kode dikirim ke: <Text style={{ color: COLORS.blue, fontWeight: 'bold' }}>{email}</Text></Text>

            <View style={styles.timerContainer}>
                <Text style={[styles.timerText, timeLeft < 60 && { color: COLORS.error }]}>⏱️ {formatTime(timeLeft)}</Text>
            </View>

            <Animated.View style={[styles.otpContainer, shakeStyle]}>
                {otpInputs.map((digit, i) => (
                    <TextInput
                        key={i}
                        ref={r => otpRefs.current[i] = r}
                        style={[styles.otpInput, digit && { borderColor: COLORS.blue }]}
                        value={digit}
                        onChangeText={t => handleOtpChange(t, i)}
                        onKeyPress={e => handleOtpBackspace(e.nativeEvent.key, i)}
                        keyboardType="number-pad"
                        maxLength={1}
                        selectTextOnFocus
                    />
                ))}
            </Animated.View>

            <TouchableOpacity style={[styles.submitBtn, otpInputs.join('').length < 6 && { opacity: 0.5 }]} onPress={handleVerifyOTP} disabled={otpInputs.join('').length < 6 || loading}>
                <LinearGradient colors={[COLORS.maroon, COLORS.maroonDark]} style={styles.btnGradient}>
                    {loading ? <ActivityIndicator color="white" /> : <Text style={styles.btnText}>Verifikasi OTP</Text>}
                </LinearGradient>
            </TouchableOpacity>

            <TouchableOpacity style={styles.resendBtn} disabled={timeLeft > 540} onPress={() => { setTimeLeft(600); setGeneratedOTP(Math.floor(100000 + Math.random() * 900000).toString()); Alert.alert("Sent"); }}>
                <Icon name="refresh" size={16} color={timeLeft > 540 ? COLORS.textLight : COLORS.blue} />
                <Text style={[styles.resendText, timeLeft > 540 && { color: COLORS.textLight }]}>
                    Kirim Ulang OTP {timeLeft > 540 && `(${formatTime(600 - timeLeft)})`}
                </Text>
            </TouchableOpacity>
        </Animated.View>
    );

    const renderStep3 = () => (
        <Animated.View entering={SlideInRight}>
            <View style={styles.iconContainer}>
                <LinearGradient colors={['#F3E8FF', '#E9D5FF']} style={styles.iconCircle}>
                    <Icon name="lock-closed" size={48} color="#9333EA" />
                </LinearGradient>
            </View>
            <Text style={styles.mainTitle}>Reset Password</Text>
            <Text style={styles.subtitle}>Buat password baru untuk akun Anda.</Text>

            {/* New Pass */}
            <View style={styles.inputGroup}>
                <Text style={styles.label}>Password Baru</Text>
                <View style={styles.inputWrapper}>
                    <Icon name="lock-closed-outline" size={20} color={COLORS.textLight} style={styles.inputIcon} />
                    <TextInput
                        style={styles.input}
                        placeholder="Minimal 8 karakter"
                        value={newPass}
                        onChangeText={setNewPass}
                        secureTextEntry={!showPass}
                    />
                    <TouchableOpacity style={styles.eyeIcon} onPress={() => setShowPass(!showPass)}>
                        <Icon name={showPass ? "eye-off-outline" : "eye-outline"} size={20} color={COLORS.textLight} />
                    </TouchableOpacity>
                </View>
            </View>

            {/* Strength Meter */}
            <View style={styles.strengthContainer}>
                <View style={[styles.strengthBar, strength >= 1 && { backgroundColor: COLORS.error }]} />
                <View style={[styles.strengthBar, strength >= 2 && { backgroundColor: COLORS.gold }]} />
                <View style={[styles.strengthBar, strength >= 3 && { backgroundColor: COLORS.blue }]} />
                <View style={[styles.strengthBar, strength >= 4 && { backgroundColor: COLORS.success }]} />
            </View>
            <Text style={[styles.strengthLabel,
            strength === 1 && { color: COLORS.error },
            strength === 2 && { color: COLORS.gold },
            strength === 3 && { color: COLORS.blue },
            strength === 4 && { color: COLORS.success }
            ]}>
                {strength === 0 ? '' : strength === 1 ? 'Lemah' : strength === 2 ? 'Sedang' : strength === 3 ? 'Kuat' : 'Sangat Kuat'}
            </Text>

            {/* Confirm Pass */}
            <View style={styles.inputGroup}>
                <Text style={styles.label}>Konfirmasi Password</Text>
                <View style={styles.inputWrapper}>
                    <Icon name="lock-closed-outline" size={20} color={COLORS.textLight} style={styles.inputIcon} />
                    <TextInput
                        style={styles.input}
                        placeholder="Ulangi password"
                        value={confirmPass}
                        onChangeText={setConfirmPass}
                        secureTextEntry={!showConfirm}
                    />
                    <TouchableOpacity style={styles.eyeIcon} onPress={() => setShowConfirm(!showConfirm)}>
                        <Icon name={showConfirm ? "eye-off-outline" : "eye-outline"} size={20} color={COLORS.textLight} />
                    </TouchableOpacity>
                </View>
                {/* Match Check */}
                {confirmPass.length > 0 && newPass === confirmPass && (
                    <View style={styles.matchIndicator}>
                        <Icon name="checkmark-circle" size={14} color={COLORS.success} />
                        <Text style={styles.matchText}>Password cocok</Text>
                    </View>
                )}
            </View>

            {/* Req List */}
            <View style={styles.reqCard}>
                <Text style={styles.reqTitle}>Persyaratan:</Text>
                <Text style={[styles.reqItem, newPass.length >= 8 && styles.reqMet]}>• Minimal 8 karakter</Text>
                <Text style={[styles.reqItem, /[A-Z]/.test(newPass) && /[a-z]/.test(newPass) && styles.reqMet]}>• Huruf besar & kecil</Text>
                <Text style={[styles.reqItem, /\d/.test(newPass) && styles.reqMet]}>• Minimal 1 angka</Text>
            </View>

            <TouchableOpacity style={styles.submitBtn} onPress={handleResetPassword} disabled={loading}>
                <LinearGradient colors={[COLORS.maroon, COLORS.maroonDark]} style={styles.btnGradient}>
                    {loading ? <ActivityIndicator color="white" /> : <Text style={styles.btnText}>Reset Password</Text>}
                </LinearGradient>
            </TouchableOpacity>
        </Animated.View>
    );

    return (
        <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={{ flex: 1 }}>
            <View style={styles.bg}>
                {renderHeader()}
                <ScrollView contentContainerStyle={styles.content}>
                    {step === 1 && renderStep1()}
                    {step === 2 && renderStep2()}
                    {step === 3 && renderStep3()}
                </ScrollView>
            </View>
        </KeyboardAvoidingView>
    );
};

const styles = StyleSheet.create({
    bg: { flex: 1, backgroundColor: COLORS.bg },
    header: { padding: 24, paddingTop: 60, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
    backBtn: { padding: 8, borderRadius: 12, backgroundColor: 'rgba(0,0,0,0.05)' },
    headerTitle: { fontSize: 18, fontWeight: 'bold', color: COLORS.textDark },
    content: { padding: 24, paddingBottom: 50 },

    iconContainer: { alignItems: 'center', marginBottom: 24 },
    iconCircle: { width: 96, height: 96, borderRadius: 48, alignItems: 'center', justifyContent: 'center' },
    mainTitle: { fontSize: 24, fontWeight: 'bold', color: COLORS.textDark, textAlign: 'center', marginBottom: 8 },
    subtitle: { fontSize: 14, color: COLORS.textLight, textAlign: 'center', marginBottom: 32 },

    inputGroup: { marginBottom: 16 },
    label: { fontSize: 14, fontWeight: '600', color: COLORS.textDark, marginBottom: 8 },
    inputWrapper: { position: 'relative' },
    inputIcon: { position: 'absolute', left: 16, top: 14, zIndex: 1 },
    eyeIcon: { position: 'absolute', right: 16, top: 14, padding: 2 },
    input: { backgroundColor: COLORS.white, borderWidth: 1, borderColor: COLORS.border, borderRadius: 12, padding: 12, paddingLeft: 48, fontSize: 14, color: COLORS.textDark },

    submitBtn: { marginTop: 24, borderRadius: 12, overflow: 'hidden', elevation: 2 },
    btnGradient: { padding: 16, alignItems: 'center', justifyContent: 'center' },
    btnText: { color: COLORS.white, fontWeight: 'bold', fontSize: 16 },

    infoBox: { backgroundColor: '#EFF6FF', padding: 16, borderRadius: 12, borderWidth: 1, borderColor: '#BFDBFE' },
    infoText: { color: '#1E40AF', fontSize: 12 },

    timerContainer: { alignSelf: 'center', paddingHorizontal: 16, paddingVertical: 8, borderRadius: 20, backgroundColor: '#EFF6FF', borderWidth: 1, borderColor: '#BFDBFE', marginBottom: 24 },
    timerText: { color: COLORS.blue, fontWeight: 'bold' },

    otpContainer: { flexDirection: 'row', justifyContent: 'center', gap: 8, marginBottom: 24 },
    otpInput: { width: 44, height: 56, borderWidth: 2, borderColor: COLORS.border, borderRadius: 12, fontSize: 24, textAlign: 'center', backgroundColor: COLORS.white, color: COLORS.textDark },

    resendBtn: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', marginTop: 24, gap: 8 },
    resendText: { color: COLORS.blue, fontWeight: '600' },

    strengthContainer: { flexDirection: 'row', gap: 4, marginTop: 8, marginBottom: 4 },
    strengthBar: { height: 4, flex: 1, borderRadius: 2, backgroundColor: COLORS.border },
    strengthLabel: { fontSize: 12, fontWeight: 'bold', marginBottom: 12, textAlign: 'right' },

    matchIndicator: { flexDirection: 'row', alignItems: 'center', gap: 4, marginTop: 8 },
    matchText: { color: COLORS.success, fontSize: 12, fontWeight: '600' },

    reqCard: { padding: 16, backgroundColor: '#F9FAFB', borderRadius: 12, borderWidth: 1, borderColor: COLORS.border, marginTop: 12 },
    reqTitle: { fontWeight: 'bold', fontSize: 12, color: COLORS.textDark, marginBottom: 8 },
    reqItem: { fontSize: 12, color: COLORS.textLight, marginBottom: 2 },
    reqMet: { color: COLORS.success, fontWeight: '600' }
});

export default ForgotPasswordScreen;
