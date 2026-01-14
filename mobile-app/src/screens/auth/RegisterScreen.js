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
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { useAuth } from '../../context/AuthContext';

const RegisterScreen = ({ navigation }) => {
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [phone, setPhone] = useState('');
    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const { register } = useAuth();
    const [loading, setLoading] = useState(false);

    const handleRegister = async () => {
        setLoading(true);
        // Simulate API call
        setTimeout(async () => {
            await register(name, email, password);
            setLoading(false);
        }, 1500);
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
                    {/* Header Section */}
                    <View style={styles.headerContainer}>
                        <View style={styles.logoBox}>
                            <Text style={styles.logoLetter}>C</Text>
                        </View>
                        <Text style={styles.appTitle}>Daftar</Text>
                        <Text style={styles.appSubtitle}>Buat akun baru untuk memulai perjalanan kuliner Anda</Text>
                    </View>

                    {/* Form Card */}
                    <View style={styles.card}>

                        {/* Name Input */}
                        <Text style={styles.label}>Nama Lengkap</Text>
                        <View style={styles.inputContainer}>
                            <Icon name="person-outline" size={20} color="#999" style={styles.inputIcon} />
                            <TextInput
                                style={styles.input}
                                placeholder="Masukkan nama lengkap"
                                placeholderTextColor="#999"
                                value={name}
                                onChangeText={setName}
                            />
                        </View>

                        {/* Email Input */}
                        <Text style={styles.label}>Email</Text>
                        <View style={styles.inputContainer}>
                            <Icon name="mail-outline" size={20} color="#999" style={styles.inputIcon} />
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

                        {/* Phone Input */}
                        <Text style={styles.label}>Nomor Telepon</Text>
                        <View style={styles.inputContainer}>
                            <Icon name="call-outline" size={20} color="#999" style={styles.inputIcon} />
                            <TextInput
                                style={styles.input}
                                placeholder="+62 812 3456 7890"
                                placeholderTextColor="#999"
                                value={phone}
                                onChangeText={setPhone}
                                keyboardType="phone-pad"
                            />
                        </View>

                        {/* Password Input */}
                        <Text style={styles.label}>Password</Text>
                        <View style={styles.inputContainer}>
                            <Icon name="lock-closed-outline" size={20} color="#999" style={styles.inputIcon} />
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

                        {/* Confirm Password Input */}
                        <Text style={styles.label}>Konfirmasi Password</Text>
                        <View style={styles.inputContainer}>
                            <Icon name="lock-closed-outline" size={20} color="#999" style={styles.inputIcon} />
                            <TextInput
                                style={styles.input}
                                placeholder="••••••••"
                                placeholderTextColor="#999"
                                value={confirmPassword}
                                onChangeText={setConfirmPassword}
                                secureTextEntry={!showConfirmPassword}
                            />
                            <TouchableOpacity onPress={() => setShowConfirmPassword(!showConfirmPassword)}>
                                <Icon name={showConfirmPassword ? "eye-off-outline" : "eye-outline"} size={20} color="#999" />
                            </TouchableOpacity>
                        </View>

                        {/* Register Button */}
                        <TouchableOpacity style={styles.registerButton} onPress={handleRegister} disabled={loading}>
                            <Text style={styles.registerButtonText}>{loading ? 'Memproses...' : 'Daftar'}</Text>
                        </TouchableOpacity>
                    </View>

                    {/* Divider */}
                    <View style={styles.dividerContainer}>
                        <View style={styles.dividerLine} />
                        <Text style={styles.dividerText}>Atau lanjutkan dengan</Text>
                        <View style={styles.dividerLine} />
                    </View>

                    {/* Google Login */}
                    <TouchableOpacity style={styles.googleButton}>
                        <Image
                            source={{ uri: 'https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg' }}
                            style={styles.googleIcon}
                        />
                        <View style={styles.gLogoContainer}>
                            <Text style={{ fontSize: 24, fontWeight: 'bold' }}><Text style={{ color: '#4285F4' }}>G</Text><Text style={{ color: '#DB4437' }}>o</Text><Text style={{ color: '#F4B400' }}>o</Text><Text style={{ color: '#4285F4' }}>g</Text><Text style={{ color: '#0F9D58' }}>l</Text><Text style={{ color: '#DB4437' }}>e</Text></Text>
                        </View>
                        <Text style={styles.googleButtonText}>Daftar dengan Google</Text>
                    </TouchableOpacity>

                    {/* Footer */}
                    <View style={styles.footer}>
                        <Text style={styles.footerText}>Sudah punya akun? </Text>
                        <TouchableOpacity onPress={() => navigation.navigate('Login')}>
                            <Text style={styles.footerLink}>Masuk Disini</Text>
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
        backgroundColor: '#F5F5F0', // Cream Background
    },
    scrollContent: {
        padding: 24,
        alignItems: 'center',
    },
    // Header
    headerContainer: {
        alignItems: 'center',
        marginTop: 20,
        marginBottom: 32,
    },
    logoBox: {
        width: 64,
        height: 64,
        backgroundColor: '#9A1B3F', // Burgundy
        borderRadius: 20,
        justifyContent: 'center',
        alignItems: 'center',
        marginBottom: 16,
        elevation: 8,
        shadowColor: '#9A1B3F',
        shadowOffset: { width: 0, height: 6 },
        shadowOpacity: 0.3,
        shadowRadius: 8,
    },
    logoLetter: {
        fontSize: 32,
        fontWeight: 'bold',
        color: '#FFF',
    },
    appTitle: {
        fontSize: 24,
        fontWeight: 'bold',
        color: '#1A1A1A',
        fontFamily: 'serif',
        marginBottom: 8,
    },
    appSubtitle: {
        fontSize: 14,
        color: '#666',
        textAlign: 'center',
        paddingHorizontal: 20,
        lineHeight: 20,
    },
    // Card
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
        marginBottom: 16,
    },
    inputIcon: {
        marginRight: 12,
    },
    input: {
        flex: 1,
        fontSize: 15,
        color: '#333',
    },
    registerButton: {
        backgroundColor: '#9A1B3F',
        borderRadius: 16,
        height: 56,
        justifyContent: 'center',
        alignItems: 'center',
        marginTop: 16,
        elevation: 4,
        shadowColor: '#9A1B3F',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.3,
        shadowRadius: 8,
    },
    registerButtonText: {
        color: '#FFF',
        fontSize: 16,
        fontWeight: 'bold',
    },
    // Divider
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
    // Google Button
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
    // Footer
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

export default RegisterScreen;
