/**
 * Laravel Echo Setup for Real-time Notifications
 * Uses Laravel Reverb as the WebSocket broadcaster (laravel-echo v2+)
 */
import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: '127.0.0.1',
    wsPort: 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});
