<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FindIt') }} — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

    {{-- Chatbot Widget (hanya untuk user yang login) --}}
    @auth
    <div id="chatbot-widget" style="position:fixed;bottom:24px;right:24px;z-index:9999;">

        {{-- Toggle Button --}}
        <button onclick="toggleChat()"
                style="width:52px;height:52px;border-radius:50%;background:var(--navy);
                       border:none;cursor:pointer;box-shadow:0 4px 16px rgba(15,32,68,0.3);
                       display:flex;align-items:center;justify-content:center;">
            <svg viewBox="0 0 24 24" style="width:22px;height:22px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </button>

        {{-- Chat Box --}}
        <div id="chat-box"
             style="display:none;position:absolute;bottom:64px;right:0;
                    width:320px;background:var(--bg2);border-radius:var(--rlg);
                    border:0.5px solid var(--border);
                    box-shadow:0 8px 32px rgba(15,32,68,0.15);overflow:hidden;">

            {{-- Header --}}
            <div style="background:var(--navy);padding:14px 16px;
                        display:flex;align-items:center;gap:10px;">
                <div style="width:32px;height:32px;border-radius:8px;background:var(--accent);
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:var(--navy);fill:none;stroke-width:2.5;">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:12px;font-weight:700;color:#fff;">FindIt Assistant</div>
                    <div style="font-size:10px;color:rgba(255,255,255,0.6);">
                        Tanya soal barang hilang & temuan
                    </div>
                </div>
            </div>

            {{-- Messages --}}
            <div id="chat-messages"
                 style="height:260px;overflow-y:auto;padding:14px;
                        display:flex;flex-direction:column;gap:10px;">
                <div style="background:var(--navy-pale);border-radius:var(--r);
                            padding:10px 12px;font-size:11px;color:var(--text);
                            max-width:90%;line-height:1.6;">
                    Halo! Saya asisten FindIt 👋<br>
                    Saya bisa bantu kamu cari info barang hilang & temuan di kampus.
                    Ada yang bisa saya bantu?
                </div>
            </div>

            {{-- Input --}}
            <div style="border-top:0.5px solid var(--border);padding:10px 12px;
                        display:flex;gap:8px;">
                <input type="text" id="chat-input"
                       placeholder="Ketik pesanmu..."
                       style="flex:1;border:0.5px solid var(--border);border-radius:var(--r);
                              padding:7px 10px;font-size:11px;font-family:'Manrope',sans-serif;
                              background:var(--bg);color:var(--text);outline:none;"
                       onkeydown="if(event.key==='Enter') sendChat()">
                <button onclick="sendChat()"
                        style="background:var(--navy);border:none;border-radius:var(--r);
                               width:34px;height:34px;cursor:pointer;flex-shrink:0;
                               display:flex;align-items:center;justify-content:center;">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2.5;stroke-linecap:round;">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
    function toggleChat() {
        const box = document.getElementById('chat-box');
        box.style.display = box.style.display === 'none' ? 'block' : 'none';
    }

    function sendChat() {
        const input    = document.getElementById('chat-input');
        const messages = document.getElementById('chat-messages');
        const msg      = input.value.trim();
        if (!msg) return;

        // Pesan user
        messages.innerHTML += `
            <div style="background:var(--navy);border-radius:var(--r);padding:9px 12px;
                        font-size:11px;color:#fff;max-width:90%;line-height:1.6;
                        align-self:flex-end;margin-left:auto;">
                ${msg}
            </div>`;
        input.value = '';
        messages.scrollTop = messages.scrollHeight;

        // Loading
        const loadingId = 'loading-' + Date.now();
        messages.innerHTML += `
            <div id="${loadingId}"
                 style="background:var(--navy-pale);border-radius:var(--r);padding:9px 12px;
                        font-size:11px;color:var(--text-3);max-width:90%;">
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
            messages.innerHTML += `
                <div style="background:var(--navy-pale);border-radius:var(--r);padding:9px 12px;
                            font-size:11px;color:var(--text);max-width:90%;line-height:1.6;">
                    ${data.reply}
                </div>`;
            messages.scrollTop = messages.scrollHeight;
        })
        .catch(() => {
            document.getElementById(loadingId).remove();
            messages.innerHTML += `
                <div style="background:var(--danger-light);border-radius:var(--r);padding:9px 12px;
                            font-size:11px;color:var(--danger);max-width:90%;">
                    Gagal terhubung. Coba lagi.
                </div>`;
            messages.scrollTop = messages.scrollHeight;
        });
    }
    </script>
    @endauth

</body>
</html>