import React, { useState } from 'react';
import {
    View,
    Text,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    StatusBar,
    Image,
    KeyboardAvoidingView,
    Platform,
    ScrollView,
    Alert,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { useAuth } from '../../context/AuthContext';
const LoginScreen = ({ navigation }) => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [showPassword, setShowPassword] = useState(false);
    const { login, googleLogin } = useAuth();
    const [loading, setLoading] = useState(false);
    const handleLogin = async () => {
        if (!email || !password) {
            Alert.alert("Perhatian", "Mohon isi email dan password");
            return;
        }
        setLoading(true);
        const result = await login(email, password);
        setLoading(false);
        if (!result.success) {
            Alert.alert("Login Gagal", result.error || "Terjadi kesalahan.");
        }
    };
    const handleGoogleLogin = async () => {
        setLoading(true);
        const result = await googleLogin();
        setLoading(false);
        if (!result.success) {
            Alert.alert("Google Login Gagal", result.error || "Terjadi kesalahan.");
        }
    };
    return (
        <View style={styles.container}>
            <StatusBar barStyle="dark-content" backgroundColor="#F5F5F0" />
            <KeyboardAvoidingView
                behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
                style={{ flex: 1 }}
            >
                <ScrollView
                    contentContainerStyle={styles.scrollContent}
                    showsVerticalScrollIndicator={false}
                >
                    {}
                    <View style={styles.headerContainer}>
                        <View style={styles.logoBox}>
                            <Text style={styles.logoLetter}>C</Text>
                        </View>
                        <Text style={styles.appTitle}>Culinaire</Text>
                        <Text style={styles.appSubtitle}>Premium Culinary Experience</Text>
                    </View>
                    {}
                    <View style={styles.card}>
                        {}
                        <Text style={styles.label}>Email</Text>
                        <View style={styles.inputContainer}>
                            <TextInput
                                style={styles.input}
                                placeholder="nama@email.com"
                                placeholderTextColor="#999"
                                value={email}
                                onChangeText={setEmail}
                                keyboardType="email-address"
                                autoCapitalize="none"
                            />
                        </View>
                        {}
                        <Text style={styles.label}>Password</Text>
                        <View style={styles.inputContainer}>
                            <TextInput
                                style={styles.input}
                                placeholder="••••••••"
                                placeholderTextColor="#999"
                                value={password}
                                onChangeText={setPassword}
                                secureTextEntry={!showPassword}
                            />
                            <TouchableOpacity onPress={() => setShowPassword(!showPassword)}>
                                <Icon name={showPassword ? "eye-off-outline" : "eye-outline"} size={20} color="#999" />
                            </TouchableOpacity>
                        </View>
                        <TouchableOpacity style={styles.forgotPassword} onPress={() => navigation.navigate('ForgotPassword')}>
                            <Text style={styles.forgotPasswordText}>Lupa password?</Text>
                        </TouchableOpacity>
                        {}
                        <TouchableOpacity style={styles.loginButton} onPress={handleLogin} disabled={loading}>
                            <Text style={styles.loginButtonText}>{loading ? 'Memuat...' : 'Masuk'}</Text>
                        </TouchableOpacity>
                    </View>
                    {}
                    <View style={styles.dividerContainer}>
                        <View style={styles.dividerLine} />
                        <Text style={styles.dividerText}>Atau lanjutkan dengan</Text>
                        <View style={styles.dividerLine} />
                    </View>
                    {}
                    <TouchableOpacity style={styles.googleButton} onPress={handleGoogleLogin} disabled={loading}>
                        <View style={styles.gLogoContainer}>
                            <Text style={{ fontSize: 24, fontWeight: 'bold' }}><Text style={{ color: '#4285F4' }}>G</Text><Text style={{ color: '#DB4437' }}>o</Text><Text style={{ color: '#F4B400' }}>o</Text><Text style={{ color: '#4285F4' }}>g</Text><Text style={{ color: '#0F9D58' }}>l</Text><Text style={{ color: '#DB4437' }}>e</Text></Text>
                        </View>
                    </TouchableOpacity>
                    {}
                    <View style={styles.footer}>
                        <Text style={styles.footerText}>Belum punya akun? </Text>
                        <TouchableOpacity onPress={() => navigation.navigate('Register')}>
                            <Text style={styles.footerLink}>Daftar Sekarang</Text>
                        </TouchableOpacity>
                    </View>
                    <View style={{ height: 50 }} />
                </ScrollView>
            </KeyboardAvoidingView>
        </View>
    );
};
const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#F5F5F0', 
    },
    scrollContent: {
        padding: 24,
        alignItems: 'center',
    },
    headerContainer: {
        alignItems: 'center',
        marginTop: 40,
        marginBottom: 40,
    },
    logoBox: {
        width: 80,
        height: 80,
        backgroundColor: '#9A1B3F', 
        borderRadius: 24,
        justifyContent: 'center',
        alignItems: 'center',
        marginBottom: 16,
        elevation: 10,
        shadowColor: '#9A1B3F',
        shadowOffset: { width: 0, height: 8 },
        shadowOpacity: 0.3,
        shadowRadius: 10,
    },
    logoLetter: {
        fontSize: 40,
        fontWeight: 'bold',
        color: '#FFF',
    },
    appTitle: {
        fontSize: 28,
        fontWeight: 'bold',
        color: '#1A1A1A',
        fontFamily: 'serif',
        marginBottom: 8,
    },
    appSubtitle: {
        fontSize: 14,
        color: '#666',
        letterSpacing: 0.5,
    },
    card: {
        backgroundColor: '#FFF',
        width: '100%',
        borderRadius: 24,
        padding: 24,
        elevation: 2,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 2 },
        shadowOpacity: 0.05,
        shadowRadius: 8,
        marginBottom: 32,
    },
    label: {
        fontSize: 14,
        color: '#333',
        marginBottom: 8,
        marginLeft: 4,
    },
    inputContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        borderWidth: 1,
        borderColor: '#E0E0E0',
        borderRadius: 16,
        paddingHorizontal: 16,
        height: 52,
        marginBottom: 20,
    },
    input: {
        flex: 1,
        fontSize: 15,
        color: '#333',
    },
    forgotPassword: {
        alignSelf: 'flex-end',
        marginBottom: 24,
    },
    forgotPasswordText: {
        color: '#9A1B3F', 
        fontSize: 13,
        fontWeight: '600',
    },
    loginButton: {
        backgroundColor: '#9A1B3F',
        borderRadius: 16,
        height: 56,
        justifyContent: 'center',
        alignItems: 'center',
        elevation: 4,
        shadowColor: '#9A1B3F',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.3,
        shadowRadius: 8,
    },
    loginButtonText: {
        color: '#FFF',
        fontSize: 16,
        fontWeight: 'bold',
    },
    dividerContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        width: '100%',
        marginBottom: 24,
    },
    dividerLine: {
        flex: 1,
        height: 1,
        backgroundColor: '#E0E0E0',
    },
    dividerText: {
        marginHorizontal: 16,
        color: '#888',
        fontSize: 13,
    },
    googleButton: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: '#FFF',
        width: '100%',
        height: 56,
        borderRadius: 16,
        borderWidth: 1,
        borderColor: '#E0E0E0',
        marginBottom: 40,
        elevation: 1,
    },
    gLogoContainer: {
        marginRight: 12,
    },
    googleButtonText: {
        fontSize: 15,
        color: '#333',
        fontWeight: '600',
    },
    footer: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    footerText: {
        color: '#666',
        fontSize: 14,
    },
    footerLink: {
        color: '#9A1B3F',
        fontSize: 14,
        fontWeight: 'bold',
    },
});
export default LoginScreen;