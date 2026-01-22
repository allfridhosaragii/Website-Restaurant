import React, { createContext, useState, useContext, useEffect } from 'react';
import { useColorScheme } from 'react-native';
import { colors as defaultColors } from '../theme/colors';
import { translations } from '../i18n/strings';
const SettingsContext = createContext();
export const SettingsProvider = ({ children }) => {
    const systemScheme = useColorScheme();
    const [isDarkMode, setIsDarkMode] = useState(true); 
    const [language, setLanguage] = useState('en'); 
    const toggleTheme = () => setIsDarkMode(prev => !prev);
    const toggleLanguage = () => setLanguage(prev => prev === 'en' ? 'id' : 'en');
    const themeColors = {
        ...defaultColors,
        background: isDarkMode ? '#000000' : '#F8F8F8', 
        surface: isDarkMode ? '#121212' : '#FFFFFF',    
        surfaceLight: isDarkMode ? '#1E1E1E' : '#F0F0F0', 
        text: isDarkMode ? '#FFFFFF' : '#1A1A1A',       
        textSecondary: isDarkMode ? '#B0B0B0' : '#666666', 
        border: isDarkMode ? '#333333' : '#E0E0E0',     
        primary: '#D4AF37', 
        primaryDim: 'rgba(212, 175, 55, 0.3)',
        cardShadow: isDarkMode ? 'transparent' : 'rgba(0,0,0,0.05)', 
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