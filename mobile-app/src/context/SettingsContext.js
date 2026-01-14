import React, { createContext, useState, useContext, useEffect } from 'react';
import { useColorScheme } from 'react-native';
import { colors as defaultColors } from '../theme/colors';
import { translations } from '../i18n/strings';

const SettingsContext = createContext();

export const SettingsProvider = ({ children }) => {
    const systemScheme = useColorScheme();
    const [isDarkMode, setIsDarkMode] = useState(true); // Default to Luxury Dark
    const [language, setLanguage] = useState('en'); // Default English

    const toggleTheme = () => setIsDarkMode(prev => !prev);
    const toggleLanguage = () => setLanguage(prev => prev === 'en' ? 'id' : 'en');

    // Dynamic Colors based on theme
    // Note: Since the original colors.js was heavily hardcoded for dark mode (Gold/Black),
    // we will swap the background/surface colors for Light Mode while keeping the Gold accents.
    const themeColors = {
        ...defaultColors,
        background: isDarkMode ? '#000000' : '#F8F8F8', // Pure Black vs Soft Off-White
        surface: isDarkMode ? '#121212' : '#FFFFFF',    // Dark Gray vs Pure White
        surfaceLight: isDarkMode ? '#1E1E1E' : '#F0F0F0', // Lighter Gray vs Light Gray
        text: isDarkMode ? '#FFFFFF' : '#1A1A1A',       // White vs Near Black
        textSecondary: isDarkMode ? '#B0B0B0' : '#666666', // Light Gray vs Dark Gray
        border: isDarkMode ? '#333333' : '#E0E0E0',     // Dark Border vs Light Border
        primary: '#D4AF37', // Gold remains consistent
        primaryDim: 'rgba(212, 175, 55, 0.3)',
        cardShadow: isDarkMode ? 'transparent' : 'rgba(0,0,0,0.05)', // Subtle shadow only in light mode
    };

    const t = (key) => translations[language][key] || key;

    return (
        <SettingsContext.Provider value={{
            isDarkMode,
            toggleTheme,
            language,
            toggleLanguage,
            colors: themeColors,
            t
        }}>
            {children}
        </SettingsContext.Provider>
    );
};

export const useSettings = () => useContext(SettingsContext);
