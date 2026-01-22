import React, { useState } from 'react';
import {
    View,
    Text,
    ScrollView,
    StyleSheet,
    TouchableOpacity,
    TextInput,
    StatusBar,
    Dimensions,
    Image,
    Alert,
    ActivityIndicator,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { useSettings } from '../../context/SettingsContext';
import { useAuth } from '../../context/AuthContext';
const { width, height } = Dimensions.get('window');
const EditProfileScreen = ({ navigation }) => {
    const { colors } = useSettings();
    const { user, updateProfile, loading } = useAuth();
    const [name, setName] = useState(user?.name || '');
    const [email, setEmail] = useState(user?.email || '');
    const [phone, setPhone] = useState(user?.phone || '');
    const [address, setAddress] = useState(user?.address || '');
    const [saving, setSaving] = useState(false);
    const handleSave = async () => {
        if (!name.trim()) {
            Alert.alert('Perhatian', 'Nama tidak boleh kosong');
            return;
        }
        setSaving(true);
        const result = await updateProfile({
            name: name.trim(),
            phone: phone.trim(),
        });
        setSaving(false);
        if (result.success) {
            Alert.alert('Berhasil', 'Profil berhasil diperbarui', [
                { text: 'OK', onPress: () => navigation.goBack() }
            ]);
        } else {
            Alert.alert('Gagal', result.error || 'Tidak dapat menyimpan perubahan');
        }
    };
    return (
        <View style={styles.container}>
            <StatusBar barStyle="light-content" backgroundColor="#8B1538" />
            {}
            <View style={styles.header}>
                <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
                    <Icon name="arrow-back" size={24} color="#FFF" />
                </TouchableOpacity>
                <Text style={styles.headerTitle}>Edit Profile</Text>
                <View style={{ width: 24 }} />
            </View>
            <ScrollView showsVerticalScrollIndicator={false}>
                {}
                <View style={styles.avatarSection}>
                    <View style={styles.avatarContainer}>
                        <View style={styles.avatar}>
                            {user?.avatar_url ? (
                                <Image source={{ uri: user.avatar_url }} style={styles.avatarImage} />
                            ) : (
                                <Icon name="person-outline" size={48} color="#FFF" />
                            )}
                        </View>
                        <TouchableOpacity style={styles.cameraBadge}>
                            <Icon name="camera-outline" size={14} color="#FFF" />
                        </TouchableOpacity>
                    </View>
                    <Text style={styles.changePhotoText}>Klik untuk ubah foto</Text>
                </View>
                {}
                <View style={styles.formCard}>
                    {}
                    <Text style={styles.label}>Nama Lengkap</Text>
                    <View style={styles.inputContainer}>
                        <Icon name="person-outline" size={20} color="#999" style={styles.inputIcon} />
                        <TextInput
                            style={styles.input}
                            value={name}
                            onChangeText={setName}
                            placeholder="Nama Lengkap"
                            placeholderTextColor="#999"
                        />
                    </View>
                    {}
                    <Text style={styles.label}>Email</Text>
                    <View style={[styles.inputContainer, { backgroundColor: '#F5F5F5' }]}>
                        <Icon name="mail-outline" size={20} color="#999" style={styles.inputIcon} />
                        <TextInput
                            style={[styles.input, { color: '#888' }]}
                            value={email}
                            editable={false}
                            placeholder="Email Address"
                            placeholderTextColor="#999"
                        />
                    </View>
                    {}
                    <Text style={styles.label}>Nomor Telepon</Text>
                    <View style={styles.inputContainer}>
                        <Icon name="call-outline" size={20} color="#999" style={styles.inputIcon} />
                        <TextInput
                            style={styles.input}
                            value={phone}
                            onChangeText={setPhone}
                            placeholder="Nomor Telepon"
                            placeholderTextColor="#999"
                            keyboardType="phone-pad"
                        />
                    </View>
                    {}
                    <Text style={styles.label}>Alamat</Text>
                    <View style={[styles.inputContainer, styles.textAreaContainer]}>
                        <Icon name="location-outline" size={20} color="#999" style={[styles.inputIcon, { marginTop: 4 }]} />
                        <TextInput
                            style={[styles.input, styles.textArea]}
                            value={address}
                            onChangeText={setAddress}
                            placeholder="Alamat Lengkap"
                            placeholderTextColor="#999"
                            multiline
                            numberOfLines={3}
                        />
                    </View>
                    {}
                    <View style={styles.actionButtons}>
                        <TouchableOpacity
                            style={styles.cancelButton}
                            onPress={() => navigation.goBack()}
                        >
                            <Text style={styles.cancelButtonText}>Batal</Text>
                        </TouchableOpacity>
                        <TouchableOpacity
                            style={[styles.saveButton, saving && { opacity: 0.7 }]}
                            onPress={handleSave}
                            disabled={saving}
                        >
                            {saving ? (
                                <ActivityIndicator size="small" color="#FFF" />
                            ) : (
                                <Text style={styles.saveButtonText}>Simpan Perubahan</Text>
                            )}
                        </TouchableOpacity>
                    </View>
                    <View style={{ height: 50 }} />
                </View>
            </ScrollView>
        </View>
    );
};
const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#F5F5F0',
    },
    header: {
        height: 60,
        backgroundColor: '#8B1538',
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        paddingHorizontal: 16,
    },
    backButton: {
        padding: 8,
    },
    headerTitle: {
        fontSize: 18,
        fontWeight: 'bold',
        color: '#FFF',
        fontFamily: 'serif',
    },
    avatarSection: {
        alignItems: 'center',
        paddingVertical: 30,
        backgroundColor: '#F5F5F0',
    },
    avatarContainer: {
        position: 'relative',
        marginBottom: 12,
    },
    avatar: {
        width: 100,
        height: 100,
        borderRadius: 50,
        backgroundColor: '#8B1538',
        justifyContent: 'center',
        alignItems: 'center',
        overflow: 'hidden',
    },
    avatarImage: {
        width: 100,
        height: 100,
        borderRadius: 50,
    },
    cameraBadge: {
        position: 'absolute',
        bottom: 0,
        right: 0,
        backgroundColor: '#DAA520',
        width: 32,
        height: 32,
        borderRadius: 16,
        justifyContent: 'center',
        alignItems: 'center',
        borderWidth: 3,
        borderColor: '#F5F5F0',
    },
    changePhotoText: {
        fontSize: 13,
        color: '#666',
        marginTop: 4,
    },
    formCard: {
        backgroundColor: '#FFF',
        borderTopLeftRadius: 24,
        borderTopRightRadius: 24,
        paddingHorizontal: 20,
        paddingTop: 24,
        minHeight: height * 0.7,
        elevation: 4,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: -2 },
        shadowOpacity: 0.05,
        shadowRadius: 8,
    },
    label: {
        fontSize: 14,
        color: '#333',
        marginBottom: 8,
        marginLeft: 4,
        marginTop: 4,
    },
    inputContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        borderWidth: 1,
        borderColor: '#E0E0E0',
        borderRadius: 12,
        paddingHorizontal: 14,
        paddingVertical: 12,
        marginBottom: 16,
        backgroundColor: '#FFF',
    },
    inputIcon: {
        marginRight: 12,
    },
    input: {
        flex: 1,
        fontSize: 15,
        color: '#333',
        padding: 0,
    },
    textAreaContainer: {
        alignItems: 'flex-start',
        paddingVertical: 12,
    },
    textArea: {
        height: 60,
        textAlignVertical: 'top',
    },
    actionButtons: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        marginTop: 20,
    },
    cancelButton: {
        flex: 1,
        backgroundColor: '#E0E0E0',
        paddingVertical: 14,
        borderRadius: 12,
        marginRight: 8,
        alignItems: 'center',
    },
    cancelButtonText: {
        color: '#555',
        fontWeight: 'bold',
        fontSize: 14,
    },
    saveButton: {
        flex: 1.5,
        backgroundColor: '#8B1538',
        paddingVertical: 14,
        borderRadius: 12,
        marginLeft: 8,
        alignItems: 'center',
    },
    saveButtonText: {
        color: '#FFF',
        fontWeight: 'bold',
        fontSize: 14,
    },
});
export default EditProfileScreen;