import React, { createContext, useContext, useState, useEffect, useCallback, useRef } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';

const CartContext = createContext();

const BASE_URL = 'https://website-restaurant.up.railway.app/api';

export const CartProvider = ({ children }) => {
    const [cartItems, setCartItems] = useState([]);
    const [cartTotal, setCartTotal] = useState(0);
    const [isCartOpen, setIsCartOpen] = useState(false);
    const [loading, setLoading] = useState(false);
    const pollingRef = useRef(null);

    // Get auth config
    const getAuthConfig = async () => {
        const token = await AsyncStorage.getItem('auth_token');
        if (!token) return null;
        return {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };
    };

    // Load cart from server
    const loadCart = useCallback(async () => {
        try {
            const config = await getAuthConfig();
            if (!config) {
                setCartItems([]);
                setCartTotal(0);
                return;
            }

            const response = await axios.get(`${BASE_URL}/cart`, config);
            const data = response.data;

            if (data.success) {
                setCartItems(data.items || []);
                setCartTotal(data.total || 0);
            }
        } catch (error) {
            console.log('Load cart error:', error);
        }
    }, []);

    // Start polling for real-time sync
    useEffect(() => {
        loadCart();

        // Poll every 5 seconds for real-time sync
        pollingRef.current = setInterval(() => {
            loadCart();
        }, 5000);

        return () => {
            if (pollingRef.current) {
                clearInterval(pollingRef.current);
            }
        };
    }, [loadCart]);

    // Add item to cart (server-side)
    const addToCart = useCallback(async (item) => {
        try {
            const config = await getAuthConfig();
            if (!config) {
                console.log('Not logged in');
                return { success: false, error: 'Login required' };
            }

            await axios.post(`${BASE_URL}/cart`, {
                menu_id: item.id,
                quantity: 1
            }, config);

            // Reload cart to get updated data
            await loadCart();
            return { success: true };
        } catch (error) {
            console.log('Add to cart error:', error.response?.data || error.message);
            const errorMessage = error.response?.data?.message || error.message;
            return { success: false, error: errorMessage };
        }
    }, [loadCart]);

    // Update quantity (server-side)
    const updateQuantity = useCallback(async (id, quantity) => {
        try {
            const config = await getAuthConfig();
            if (!config) return;

            if (quantity <= 0) {
                await removeItem(id);
                return;
            }

            await axios.put(`${BASE_URL}/cart/${id}`, { quantity }, config);
            await loadCart();
        } catch (error) {
            console.log('Update quantity error:', error);
        }
    }, [loadCart]);

    // Remove item from cart (server-side)
    const removeItem = useCallback(async (id) => {
        try {
            const config = await getAuthConfig();
            if (!config) return;

            await axios.delete(`${BASE_URL}/cart/${id}`, config);
            await loadCart();
        } catch (error) {
            console.log('Remove item error:', error);
        }
    }, [loadCart]);

    // Clear entire cart (server-side)
    const clearCart = useCallback(async () => {
        try {
            const config = await getAuthConfig();
            if (!config) return;

            await axios.delete(`${BASE_URL}/cart`, config);
            setCartItems([]);
            setCartTotal(0);
        } catch (error) {
            console.log('Clear cart error:', error);
        }
    }, []);

    // Open/Close cart popup
    const openCart = useCallback(() => {
        loadCart(); // Refresh before opening
        setIsCartOpen(true);
    }, [loadCart]);

    const closeCart = useCallback(() => setIsCartOpen(false), []);

    // Calculated value
    const cartItemCount = cartItems.reduce((sum, item) => sum + (item.quantity || 0), 0);

    // Checkout - create order from cart
    const checkout = async (customerData = {}) => {
        setLoading(true);
        try {
            const config = await getAuthConfig();
            if (!config) {
                setLoading(false);
                return { success: false, error: 'Login required' };
            }

            // Create order from cart
            const orderResponse = await axios.post(`${BASE_URL}/orders`, {
                items: cartItems.map(item => ({
                    menu_id: item.menu_id,
                    quantity: item.quantity,
                    price: item.price
                })),
                total: cartTotal,
                payment_method: 'cash', // Default payload to fix 422 error
                ...customerData
            }, config);

            // Clear cart after successful order
            await clearCart();

            // Generate receipt data
            const receipt = {
                receiptNumber: `ORD-${Date.now().toString().slice(-8)}`,
                date: new Date().toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                }),
                time: new Date().toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                }),
                items: cartItems.map(item => ({
                    name: item.menu_name,
                    quantity: item.quantity,
                    price: item.subtotal || (item.price * item.quantity)
                })),
                total: cartTotal
            };

            closeCart();
            setLoading(false);
            return { success: true, receipt };
        } catch (error) {
            console.log('Checkout error:', error.response?.data || error.message);
            setLoading(false);
            const errorMessage = error.response?.data?.message || error.message;
            return { success: false, error: errorMessage };
        }
    };

    // Force refresh cart
    const refreshCart = useCallback(() => {
        loadCart();
    }, [loadCart]);

    return (
        <CartContext.Provider
            value={{
                cartItems,
                cartItemCount,
                cartTotal,
                isCartOpen,
                loading,
                addToCart,
                updateQuantity,
                removeItem,
                clearCart,
                openCart,
                closeCart,
                checkout,
                refreshCart
            }}
        >
            {children}
        </CartContext.Provider>
    );
};

export const useCart = () => {
    const context = useContext(CartContext);
    if (!context) {
        throw new Error('useCart must be used within a CartProvider');
    }
    return context;
};

export default CartContext;
