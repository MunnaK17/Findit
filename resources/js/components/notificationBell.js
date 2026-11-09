/**
 * Notification Bell Alpine Component
 * Handles real-time notifications via Laravel Echo + Pusher
 */
function notificationBell() {
    return {
        open: false,
        notifications: [],
        unreadCount: 0,

        init() {
            // Load initial notifications from server-rendered data
            this.loadFromServer();

            // Setup realtime via Laravel Echo
            this.setupEcho();
        },

        loadFromServer() {
            axios.get('/notifications/api')
                .then(res => {
                    this.notifications = res.data.notifications;
                    this.unreadCount = res.data.unread_count;
                })
                .catch(() => {});
        },

        setupEcho() {
            if (typeof window.Echo === 'undefined') return;

            const userId = document.querySelector('meta[name="user-id"]')?.content;
            if (!userId) return;

            // Listen for claim status updates (user channel)
            window.Echo.private(`user.${userId}`)
                .listen('.claim.status', (data) => {
                    this.addNotification({
                        id: Date.now(),
                        title: data.title,
                        body: data.message,
                        data: { type: data.status, claim_id: data.claim_id, report_id: data.report_id },
                        read_at: null,
                        created_at: new Date().toISOString(),
                    });
                    this.unreadCount++;
                    this.showToast(data.title, data.message);
                });

            // Listen for new claim submissions (admin channel)
            window.Echo.channel('admin')
                .listen('.claim.submitted', (data) => {
                    this.addNotification({
                        id: Date.now(),
                        title: 'Klaim Baru!',
                        body: data.message,
                        data: { type: 'claim_submitted', claim_id: data.claim_id, report_id: data.report_id },
                        read_at: null,
                        created_at: new Date().toISOString(),
                    });
                    this.unreadCount++;
                    this.showToast('Klaim Baru!', data.message);
                });
        },

        addNotification(n) {
            this.notifications.unshift(n);
            if (this.notifications.length > 20) {
                this.notifications.pop();
            }
        },

        markAllRead() {
            axios.post('/notifications/mark-all-read')
                .then(() => {
                    this.notifications.forEach(n => n.read_at = new Date().toISOString());
                    this.unreadCount = 0;
                });
        },

        openUrl(n) {
            const url = n.data?.url || '/notifications';
            if (!n.read_at) {
                axios.post(`/notifications/${n.id}/read`).catch(() => {});
                n.read_at = new Date().toISOString();
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }
            window.location.href = url;
        },

        timeAgo(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);

            if (diff < 60) return 'Baru saja';
            if (diff < 3600) return `${Math.floor(diff / 60)} menit lalu`;
            if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`;
            return `${Math.floor(diff / 86400)} hari lalu`;
        },

        showToast(title, body) {
            if (typeof window.showToast === 'function') {
                window.showToast(title, body);
            }
        },
    };
}

window.notificationBell = notificationBell;
