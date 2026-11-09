<x-app-layout>

    <div style="max-width:680px;margin:0 auto;padding:26px 17px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:26px;">
            <h1 style="font-size:25px;font-weight:800;color:var(--text);">Notifikasi</h1>
            @if($notifications->count() > 0)
                <form method="POST" action="{{ route('notifications.markAllRead') }}">
                    @csrf
                    <button type="submit" style="font-size:13px;color:#2563eb;background:transparent;border:none;cursor:pointer;font-weight:600;">
                        Tandai semua baca
                    </button>
                </form>
            @endif
        </div>

        @forelse($notifications as $n)
            <div style="background:{{ $n->read_at ? '#fff' : '#eff6ff' }};
                        border-radius:12px;padding:17px;margin-bottom:13px;
                        border:1px solid {{ $n->read_at ? 'var(--border)' : '#bfdbfe' }};
                        display:flex;gap:13px;align-items:flex-start;">
                <div style="width:43px;height:43px;border-radius:11px;background:var(--navy);
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:17px;">
                    🔔
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:14px;font-weight:700;color:#111827;">{{ $n->title ?? 'Notifikasi' }}</div>
                    <div style="font-size:13px;color:#6b7280;margin-top:4px;">{{ $n->body ?? '' }}</div>
                    <div style="font-size:12px;color:#9ca3af;margin-top:7px;">
                        {{ $n->created_at->diffForHumans() }}
                    </div>
                </div>
                @if(!$n->read_at)
                    <div style="width:9px;height:9px;border-radius:50%;background:#2563eb;flex-shrink:0;margin-top:7px;"></div>
                @endif
            </div>
        @empty
            <div style="text-align:center;padding:51px 26px;color:#9ca3af;">
                <div style="font-size:51px;margin-bottom:13px;">🔔</div>
                <div style="font-size:15px;font-weight:600;">Tidak ada notifikasi</div>
                <div style="font-size:13px;margin-top:4px;">Notifikasi klaim akan muncul di sini</div>
            </div>
        @endforelse

        <div style="margin-top:17px;">
            {{ $notifications->links() }}
        </div>
    </div>

</x-app-layout>