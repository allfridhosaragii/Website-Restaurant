import React, { createContext, useState, useContext, useEffect } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { GoogleSignin, statusCodes } from '@react-native-google-signin/google-signin';
import axios from 'axios';

const AuthContext = createContext(null);

const BASE_URL = 'https://website-restaurant.up.railway.app/api';

// Configure Google Sign-In
const WEB_CLIENT_ID = '55427073142-6kdkbkd1qn47g7dds339pca6u1duj44f.apps.googleusercontent.com';

const configureGoogleSignIn = () => {
    try {
        GoogleSignin.configure({
            webClientId: WEB_CLIENT_ID,
            offlineAccess: true,
            forceCodeForRefreshToken: true,
            scopes: ['profile', 'email']
        });
    } catch (e) {
        console.error('Google Sign-In Config Error', e);
    }
};

// Initial config
configureGoogleSignIn();

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [token, setToken] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [isGuest, setIsGuest] = useState(false);

    useEffect(() => {
        configureGoogleSignIn();
        checkAuth();
    }, []);

    const checkAuth = async () => {
        try {
            const storedToken = await AsyncStorage.getItem('auth_token');
            const storedUser = await AsyncStorage.getItem('user');

            if (storedToken && storedUser) {
                setToken(storedToken);
                setUser(JSON.parse(storedUser));
            }
        } catch (e) {
            console.log('Auth check error:', e);
        } finally {
            setLoading(false);
        }
    };

    const login = async (email, password) => {
        try {
            setError(null);
            setLoading(true);
            setIsGuest(false);

            console.log(`[Auth] Login attempt: ${email}`);
            console.log(`[Auth] Endpoint: ${BASE_URL}/auth/login`);

            const response = await axios.post(`${BASE_URL}/auth/login`, {
                email: email,
                password: password
            }, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            console.log('[Auth] Login Success Response:', response.status);

            const { user: userData, token: authToken } = response.data;

            if (!authToken || !userData) {
                throw new Error("Invalid response from server (Missing token/user)");
            }

            await AsyncStorage.setItem('auth_token', authToken);
            await AsyncStorage.setItem('user', JSON.stringify(userData));

            setUser(userData);
            setToken(authToken);

            return { success: true };
        } catch (e) {
            console.error('[Auth] Login Error:', e);
            if (e.response) {
                console.log('[Auth] Error Data:', e.response.data);
                console.log('[Auth] Error Status:', e.response.status);
            }

            const message = e.response?.data?.message || 'Login gagal. Periksa email dan password.';

            // Detailed validation error handling
            const errors = e.response?.data?.errors;
            let finalMessage = message;
            if (errors) {
                if (errors.email) finalMessage = errors.email[0];
                else if (errors.password) finalMessage = errors.password[0];
            }

            setError(finalMessage);
            return { success: false, error: finalMessage };
        } finally {
            setLoading(false);
        }
    };

    const guestLogin = () => {
        setIsGuest(true);
    };

    const googleLogin = async () => {
        try {
            setError(null);
            setLoading(true);
            setIsGuest(false);

            await GoogleSignin.hasPlayServices();
            const signInResult = await GoogleSignin.signIn();

            let idToken, googleUser;

            if (signInResult.data) {
                idToken = signInResult.data.idToken;
                googleUser = signInResult.data.user;
            } else if (signInResult.idToken) {
                idToken = signInResult.idToken;
                googleUser = signInResult.user;
            } else {
                idToken = signInResult.idToken;
                googleUser = signInResult.user;
            }

            if (!idToken) throw new Error("No ID Token from Google");

            const response = await axios.post(`${BASE_URL}/auth/google`, {
                id_token: idToken,
                email: googleUser.email,
                name: googleUser.name,
                google_id: googleUser.id,
                photo: googleUser.photo
            }, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const { user: userData, token: authToken } = response.data;

            await AsyncStorage.setItem('auth_token', authToken);
            await AsyncStorage.setItem('user', JSON.stringify(userData));

            setUser(userData);
            setToken(authToken);

            return { success: true };
        } catch (error) {
            console.error('[Auth] Google Login Error:', error);
            let message = 'Google Login Failed';
            if (error.code === statusCodes.SIGN_IN_CANCELLED) {
                message = 'Login dibatalkan user';
            } else {
                message = error.message || 'Gagal login Google';
            }
            setError(message);
            return { success: false, error: message };
        } finally {
            setLoading(false);
        }
    };

    const register = async (data) => {
        try {
            setError(null);
            setLoading(true);
            setIsGuest(false);

            const response = await axios.post(`${BASE_URL}/auth/register`, data, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const { user: userData, token: authToken } = response.data;

            await AsyncStorage.setItem('auth_token', authToken);
            await AsyncStorage.setItem('user', JSON.stringify(userData));

            setUser(userData);
            setToken(authToken);

            return { success: true };
        } catch (e) {
            console.error('[Auth] Register Error:', e);
            const message = e.response?.data?.message || 'Registrasi gagal.';
            setError(message);
            return { success: false, error: message };
        } finally {
            setLoading(false);
        }
    };

    const logout = async () => {
        try {
            if (!isGuest && token) {
                await axios.post(`${BASE_URL}/auth/logout`, {}, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
            }
        } catch (e) {
            console.log('[Auth] Logout API error:', e);
        } finally {
            await AsyncStorage.removeItem('auth_token');
            await AsyncStorage.removeItem('user');

            try {
                const isSignedIn = await GoogleSignin.isSignedIn();
                if (isSignedIn) {
                    await GoogleSignin.signOut();
                }
            } catch (err) { }

            setUser(null);
            setToken(null);
            setIsGuest(false);
        }
    };

    const updateProfile = async (data) => {
        try {
            setLoading(true);
            const currentToken = await AsyncStorage.getItem('auth_token');

            const formData = new FormData();
            if (data.name) formData.append('name', data.name);
            if (data.phone) formData.append('phone', data.phone);
            if (data.avatar) {
                formData.append('avatar', {
                    uri: data.avatar.uri,
                    type: data.avatar.type,
                    name: data.avatar.fileName,
                });
            }
            formData.append('_method', 'PUT');

            console.log('[Auth] Updating Profile...');

            const response = await axios.post(`${BASE_URL}/auth/user`, formData, {
                headers: {
                    'Authorization': `Bearer ${currentToken}`,
                    'Accept': 'application/json',
                    'Content-Type': 'multipart/form-data'
                }
            });

            const updatedUser = response.data.user || response.data;
            const newUserState = { ...user, ...updatedUser };

            setUser(newUserState);
            await AsyncStorage.setItem('user', JSON.stringify(newUserState));
            return { success: true };
        } catch (e) {
            console.log('[Auth] Update Profile Error:', e);
            const message = e.response?.data?.message || 'Gagal memperbarui profil';
            return { success: false, error: message };
        } finally {
            setLoading(false);
        }
    };

    const value = {
        user,
        token,
        loading,
        error,
        isGuest,
        isAuthenticated: !!token || isGuest,
        login,
        googleLogin,
        guestLogin,
        register,
        logout,
        checkAuth,
        updateProfile,
    };

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within AuthProvider');
    }
    return context;
};

export default AuthContext;
