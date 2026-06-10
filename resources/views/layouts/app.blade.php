<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-id" content="{{ Auth::id() }}">
    @endauth
    <title>{{ config('app.name', 'FindIt') }} — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700;800&family=Inter:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body style="display:flex;flex-direction:column;min-height:100vh;">

    {{-- Navbar --}}
    @include('partials.navbar')

    {{-- Flash --}}
    @if(session('success') || session('error'))
        <div style="background:var(--bg2);border-bottom:0.5px solid var(--border);padding:10px 24px;">
            @if(session('success'))
                <div class="alert alert-success mb-0">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger mb-0">❌ {{ session('error') }}</div>
            @endif
        </div>
    @endif

    {{-- Main Content --}}
    <main style="flex:1;padding:24px;max-width:1200px;width:100%;margin:0 auto;">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Chatbot Widget --}}
    <div id="chatbot-widget" style="position:fixed;bottom:24px;right:24px;z-index:9999;">

        {{-- Toggle Button --}}
        <button onclick="toggleChat()"
                style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
                       border:none;cursor:pointer;box-shadow:0 4px 20px rgba(59,130,246,0.4);
                       display:flex;align-items:center;justify-content:center;">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </button>

        {{-- Chat Box --}}
        <div id="chat-box"
             style="display:none;position:absolute;bottom:80px;right:0;
                    width:380px;background:#fff;border-radius:20px;
                    border:1px solid #e5e7eb;
                    box-shadow:0 12px 40px rgba(0,0,0,0.15);overflow:hidden;">

            {{-- Header --}}
            <div style="background:linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);padding:24px 20px;
                        text-align:center;">
                <div style="font-size:18px;font-weight:700;color:#fff;margin-bottom:4px;">
                    Selamat Datang di FindIt 👋
                </div>
                <div style="font-size:12px;color:rgba(255,255,255,0.8);">
                    Temukan barang hilang & klaim barang temuan dengan mudah
                </div>
            </div>

            {{-- Messages / Welcome --}}
            <div id="chat-messages"
                 style="height:320px;overflow-y:auto;padding:20px;
                        display:flex;flex-direction:column;align-items:center;justify-content:center;gap:16px;">
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
                            display:flex;align-items:center;justify-content:center;">
                    <svg viewBox="0 0 24 24" style="width:40px;height:40px;stroke:#fff;fill:none;stroke-width:1.5;">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:14px;color:#111827;font-weight:600;margin-bottom:4px;">
                        Belum ada percakapan
                    </div>
                    <div style="font-size:12px;color:#6b7280;">
                        Pilih opsi di bawah atau ketik pertanyaanmu
                    </div>
                </div>
                <button onclick="startConversation()"
                        style="background:linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
                               border:none;border-radius:12px;padding:12px 24px;
                               color:#fff;font-size:14px;font-weight:600;cursor:pointer;
                               box-shadow:0 4px 12px rgba(59,130,246,0.3);">
                    💬 Mulai Bertanya
                </button>
            </div>

            {{-- Input (hidden initially) --}}
            <div id="chat-input-area" style="border-top:1px solid #e5e7eb;padding:14px 16px;
                        display:none;gap:10px;">
                <input type="text" id="chat-input"
                       placeholder="Ketik pesanmu..."
                       style="flex:1;border:1px solid #e5e7eb;border-radius:12px;
                              padding:12px 14px;font-size:13px;font-family:'Manrope',sans-serif;
                              background:#f9fafb;color:#111827;outline:none;"
                       onkeydown="if(event.key==='Enter') sendChat()">
                <button onclick="sendChat()"
                        style="background:linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
                               border:none;border-radius:12px;width:44px;height:44px;cursor:pointer;
                               display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:#fff;fill:none;stroke-width:2.5;stroke-linecap:round;">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
    let conversationStarted = false;

    function toggleChat() {
        const box = document.getElementById('chat-box');
        box.style.display = box.style.display === 'none' ? 'block' : 'none';
    }

    function startConversation() {
        const messages = document.getElementById('chat-messages');
        const inputArea = document.getElementById('chat-input-area');
        messages.style.justifyContent = 'flex-start';
        messages.innerHTML = `
            <div style="background:#f3f4f6;border-radius:16px;padding:14px 16px;font-size:13px;color:#111827;max-width:85%;line-height:1.6;align-self:flex-start;">
                Halo! 👋 Saya asisten FindIt.<br>
                Saya siap membantu kamu menemukan info barang hilang & temuan di kampus.<br>
                Ada yang bisa saya bantu?
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;">
                <button onclick="sendQuickReply(this)" data-message="barang temuan terbaru" style="background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:8px 14px;font-size:12px;color:#1e40af;cursor:pointer;">📦 Barang Temuan</button>
                <button onclick="sendQuickReply(this)" data-message="barang hilang terbaru" style="background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:8px 14px;font-size:12px;color:#1e40af;cursor:pointer;">🔍 Barang Hilang</button>
                <button onclick="sendQuickReply(this)" data-message="statistik findit" style="background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:8px 14px;font-size:12px;color:#1e40af;cursor:pointer;">📊 Statistik</button>
                <button onclick="sendQuickReply(this)" data-message="kategori barang" style="background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:8px 14px;font-size:12px;color:#1e40af;cursor:pointer;">📁 Kategori</button>
            </div>`;
        inputArea.style.display = 'flex';
        conversationStarted = true;
    }

    function sendQuickReply(btn) {
        const msg = btn.getAttribute('data-message');
        document.getElementById('chat-input').value = msg;
        sendChat();
    }

    function sendChat() {
        const input    = document.getElementById('chat-input');
        const messages = document.getElementById('chat-messages');
        const msg      = input.value.trim();
        if (!msg) return;

        messages.style.justifyContent = 'flex-start';

        // Pesan user
        messages.innerHTML += `
            <div style="background:linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);border-radius:16px;padding:12px 16px;
                        font-size:13px;color:#fff;max-width:85%;line-height:1.6;
                        align-self:flex-end;margin-left:auto;">
                ${msg}
            </div>`;
        input.value = '';
        messages.scrollTop = messages.scrollHeight;

        // Loading
        const loadingId = 'loading-' + Date.now();
        messages.innerHTML += `
            <div id="${loadingId}"
                 style="background:#f3f4f6;border-radius:16px;padding:12px 16px;
                        font-size:13px;color:#6b7280;max-width:85%;align-self:flex-start;">
                Sedang mengetik...
            </div>`;
        messages.scrollTop = messages.scrollHeight;

        fetch('{{ route("chatbot.ask") }}', {
            method: 'POST',
            headers: {
                'Content-Type'  : 'application/json',
                'X-CSRF-TOKEN'  : document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ message: msg })
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById(loadingId).remove();

            // Format reply dengan line breaks
            const formattedReply = data.reply.replace(/\n/g, '<br>');

            let html = `
                <div style="background:#f3f4f6;border-radius:16px;padding:12px 16px;
                            font-size:13px;color:#111827;max-width:85%;line-height:1.6;align-self:flex-start;">
                    ${formattedReply}
                </div>`;

            // Tambah Quick Replies jika ada
            if (data.quickReplies && data.quickReplies.length > 0) {
                html += `<div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;">`;
                data.quickReplies.forEach(qr => {
                    html += `<button onclick="sendQuickReply(this)" data-message="${qr.value}" style="background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:8px 14px;font-size:12px;color:#1e40af;cursor:pointer;">${qr.label}</button>`;
                });
                html += `</div>`;
            }

            messages.innerHTML += html;
            messages.scrollTop = messages.scrollHeight;
        })
        .catch(() => {
            document.getElementById(loadingId).remove();
            messages.innerHTML += `
                <div style="background:#fee2e2;border-radius:16px;padding:12px 16px;
                            font-size:13px;color:#dc2626;max-width:85%;align-self:flex-start;">
                    Gagal terhubung. Coba lagi.
                </div>`;
            messages.scrollTop = messages.scrollHeight;
        });
    }
    </script>

    {{-- Toast Notification Container --}}
    <div id="toast-container"
         style="position:fixed;top:16px;right:16px;z-index:99999;
                display:flex;flex-direction:column;gap:8px;max-width:320px;">
    </div>

    <script>
    window.showToast = function(title, body) {
        const container = document.getElementById('toast-container');
        const id = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = id;
        toast.style.cssText = `
            background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.15);
            padding:14px 16px;border-left:4px solid #2563eb;
            display:flex;flex-direction:column;gap:4px;animation:slideIn 0.3s ease;
            font-family:'Manrope',sans-serif;
        `;
        toast.innerHTML = `
            <div style="font-size:13px;font-weight:700;color:#111827;">${title}</div>
            <div style="font-size:12px;color:#6b7280;">${body}</div>
        `;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.transition = 'opacity 0.3s';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    };
    </script>

</body>
</html>
