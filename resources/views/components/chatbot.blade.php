<div id="chatbot-wrap" style="font-family: 'Inter', system-ui, -apple-system, sans-serif;">

    {{-- Tombol buka/tutup dengan efek pulse --}}
    <div class="chatbot-pulse-ring"></div>
    <button id="chatbot-toggle"
        onclick="toggleChat()"
        title="Tanya Asisten Sanggar"
        class="chatbot-toggle-btn">
        <svg id="icon-chat" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <svg id="icon-close" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" style="display:none">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
        {{-- Badge notifikasi --}}
        <div id="chat-badge" class="chat-badge">
            <span>1</span>
        </div>
    </button>

    {{-- Jendela chat --}}
    <div id="chatbot-window" class="chatbot-window">

        {{-- Header Premium Glassmorphism --}}
        <div class="chatbot-header">
            <div class="chatbot-avatar">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <div class="online-indicator"></div>
            </div>
            <div class="chatbot-header-text">
                <div class="chatbot-title">Asisten Sanggar</div>
                <div class="chatbot-subtitle">Powered by Gemini AI</div>
            </div>
            <button onclick="clearChat()" title="Hapus riwayat chat" class="chatbot-clear-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            </button>
        </div>

        {{-- Pesan-pesan --}}
        <div id="chat-messages" class="chat-messages-container">
            {{-- Pesan selamat datang --}}
            <div class="msg-bot">
                Halo! Saya asisten virtual <strong>Sanggar Mulya Bhakti</strong> 🎭<br><br>
                Ada yang bisa saya bantu hari ini?
            </div>

            {{-- Quick replies --}}
            <div id="quick-replies" class="quick-replies-container">
                <button class="quick-btn" onclick="sendQuick('Bagaimana cara daftar anggota?')">✨ Cara daftar?</button>
                <button class="quick-btn" onclick="sendQuick('Apa saja jadwal latihan?')">📅 Jadwal latihan</button>
                <button class="quick-btn" onclick="sendQuick('Tarian apa saja yang diajarkan?')">🎭 Tarian tersedia</button>
                <button class="quick-btn" onclick="sendQuick('Berapa biaya pendaftaran?')">💰 Biaya daftar</button>
                <button class="quick-btn" onclick="sendQuick('Bagaimana cara menghubungi sanggar?')">📞 Kontak</button>
            </div>
        </div>

        {{-- Input area --}}
        <div class="chatbot-input-area">
            <input
                id="chat-input"
                type="text"
                placeholder="Ketik pertanyaan Anda di sini..."
                autocomplete="off"
                class="chatbot-input"
                onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();sendMessage();}"
            >
            <button id="send-btn" onclick="sendMessage()" class="chatbot-send-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<style>
/* CSS Reset specifically for chatbot to avoid theme conflicts */
#chatbot-wrap * {
    box-sizing: border-box;
}

/* Floating Button & Pulse */
.chatbot-toggle-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #FF7B42, #C65D2E);
    border-radius: 50%;
    border: none;
    cursor: pointer;
    z-index: 9999;
    box-shadow: 0 10px 30px rgba(198,93,46,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease;
}
.chatbot-toggle-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 14px 40px rgba(198,93,46,0.5);
}
.chatbot-pulse-ring {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(198,93,46,0.5);
    z-index: 9998;
    animation: pulseRing 2.5s infinite;
    pointer-events: none;
}
@keyframes pulseRing {
    0% { transform: scale(0.9); opacity: 1; }
    100% { transform: scale(1.6); opacity: 0; }
}

/* Notification Badge */
.chat-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 18px;
    height: 18px;
    background: #EF4444;
    border-radius: 50%;
    border: 2px solid #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
.chat-badge span {
    color: #fff;
    font-size: 9px;
    font-weight: 800;
}

/* Main Window */
.chatbot-window {
    position: fixed;
    bottom: 110px;
    right: 30px;
    width: 380px;
    height: 600px;
    max-height: calc(100vh - 140px);
    background: #FAFAFD;
    border-radius: 24px;
    box-shadow: 0 24px 80px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.05);
    z-index: 9998;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    opacity: 0;
    pointer-events: none;
    transform: translateY(20px) scale(0.95);
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    transform-origin: bottom right;
}
.chatbot-window.active {
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0) scale(1);
}

/* Header */
.chatbot-header {
    background: linear-gradient(135deg, #E86B32, #C65D2E);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    flex-shrink: 0;
    position: relative;
    z-index: 2;
    box-shadow: 0 4px 20px rgba(198,93,46,0.2);
}
.chatbot-avatar {
    width: 44px;
    height: 44px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    backdrop-filter: blur(5px);
    position: relative;
}
.online-indicator {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 10px;
    height: 10px;
    background: #10B981;
    border: 2px solid #C65D2E;
    border-radius: 50%;
}
.chatbot-header-text {
    flex: 1;
}
.chatbot-title {
    color: #fff;
    font-weight: 800;
    font-size: 1.05rem;
    line-height: 1.2;
    letter-spacing: 0.3px;
}
.chatbot-subtitle {
    color: rgba(255,255,255,0.85);
    font-size: 0.75rem;
    margin-top: 3px;
    font-weight: 500;
}
.chatbot-clear-btn {
    background: rgba(255,255,255,0.15);
    border: none;
    border-radius: 10px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #fff;
    transition: background 0.2s;
}
.chatbot-clear-btn:hover {
    background: rgba(255,255,255,0.3);
}

/* Messages Area */
.chat-messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 24px 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    scroll-behavior: smooth;
    background: url('data:image/svg+xml;utf8,<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="1.5" fill="%23E8E0D8"/></svg>') repeat;
}
.chat-messages-container::-webkit-scrollbar { width: 6px; }
.chat-messages-container::-webkit-scrollbar-track { background: transparent; }
.chat-messages-container::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }

/* Chat Bubbles */
.msg-bot {
    background: #FFFFFF;
    border: 1px solid rgba(0,0,0,0.04);
    border-radius: 20px 20px 20px 4px;
    padding: 14px 18px;
    font-size: 0.9rem;
    line-height: 1.6;
    color: #2D3748;
    max-width: 88%;
    align-self: flex-start;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    animation: msgIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    transform-origin: bottom left;
}
.msg-bot strong {
    color: #C65D2E;
    font-weight: 700;
}
.msg-user {
    background: linear-gradient(135deg, #FF7B42, #C65D2E);
    color: #fff;
    border-radius: 20px 20px 4px 20px;
    padding: 14px 18px;
    font-size: 0.9rem;
    line-height: 1.6;
    max-width: 88%;
    align-self: flex-end;
    box-shadow: 0 6px 16px rgba(198,93,46,0.25);
    animation: msgIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    transform-origin: bottom right;
}

/* Quick Replies */
.quick-replies-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 4px;
    animation: fadeIn 0.5s ease 0.3s both;
}
.quick-btn {
    background: #fff;
    border: 1.5px solid #F0D9CE;
    color: #C65D2E;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 8px 16px;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    font-family: inherit;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}
.quick-btn:hover {
    background: linear-gradient(135deg, #FF7B42, #C65D2E);
    border-color: transparent;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(198,93,46,0.2);
}

/* Loading Indicator */
.msg-loading {
    background: #FFFFFF;
    border: 1px solid rgba(0,0,0,0.04);
    border-radius: 20px 20px 20px 4px;
    padding: 16px 20px;
    align-self: flex-start;
    display: flex;
    gap: 6px;
    align-items: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    transform-origin: bottom left;
    animation: msgIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.msg-loading span {
    width: 8px; height: 8px;
    background: #C65D2E; border-radius: 50%;
    animation: dotBounce 1.4s ease-in-out infinite both;
    opacity: 0.6;
}
.msg-loading span:nth-child(1) { animation-delay: -0.32s; }
.msg-loading span:nth-child(2) { animation-delay: -0.16s; }

/* Input Area */
.chatbot-input-area {
    padding: 16px 20px;
    background: #fff;
    display: flex;
    gap: 12px;
    flex-shrink: 0;
    border-top: 1px solid rgba(0,0,0,0.05);
    position: relative;
    z-index: 2;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.02);
}
.chatbot-input {
    flex: 1;
    padding: 12px 18px;
    background: #F4F4F7;
    border: 1.5px solid transparent;
    border-radius: 50px;
    font-size: 0.9rem;
    outline: none;
    font-family: inherit;
    color: #333;
    transition: all 0.2s;
}
.chatbot-input::placeholder {
    color: #9CA3AF;
}
.chatbot-input:focus {
    background: #fff;
    border-color: #C65D2E;
    box-shadow: 0 0 0 4px rgba(198,93,46,0.1);
}
.chatbot-send-btn {
    width: 46px;
    height: 46px;
    background: linear-gradient(135deg, #FF7B42, #C65D2E);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 4px 12px rgba(198,93,46,0.3);
}
.chatbot-send-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(198,93,46,0.4);
}
.chatbot-send-btn:disabled {
    background: #E5E7EB;
    box-shadow: none;
    cursor: not-allowed;
    transform: none;
}
.chatbot-send-btn:disabled svg {
    stroke: #9CA3AF;
}

/* Animations */
@keyframes msgIn {
    from { opacity: 0; transform: translateY(10px) scale(0.95); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes dotBounce {
    0%, 80%, 100% { transform: scale(0); opacity: 0.3; }
    40% { transform: scale(1); opacity: 1; }
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 480px) {
    .chatbot-window {
        bottom: 0;
        right: 0;
        width: 100%;
        height: 100%;
        max-height: 100vh;
        border-radius: 0;
    }
    .chatbot-toggle-btn {
        bottom: 20px;
        right: 20px;
    }
    .chatbot-pulse-ring {
        bottom: 20px;
        right: 20px;
    }
}
</style>

<script>
(function() {
    const sessionId = 'chat_' + Math.random().toString(36).substr(2, 9);
    let isOpen = false;

    window.toggleChat = function() {
        isOpen = !isOpen;
        const win   = document.getElementById('chatbot-window');
        const iconC = document.getElementById('icon-chat');
        const iconX = document.getElementById('icon-close');
        const badge = document.getElementById('chat-badge');
        const pulse = document.querySelector('.chatbot-pulse-ring');

        if (isOpen) {
            win.style.display = 'flex'; // needed for flex layout to work before animating
            // Small timeout to allow display:flex to apply before adding class
            setTimeout(() => {
                win.classList.add('active');
            }, 10);
            iconC.style.display = 'none';
            iconX.style.display = 'block';
            if (badge) badge.style.display = 'none';
            if (pulse) pulse.style.display = 'none'; // stop pulsing when opened
            document.getElementById('chat-input').focus();
            scrollBottom();
        } else {
            win.classList.remove('active');
            setTimeout(() => {
                win.style.display = 'none';
            }, 400); // match transition duration
            iconC.style.display = 'block';
            iconX.style.display = 'none';
        }
    };

    window.sendQuick = function(msg) {
        const qr = document.getElementById('quick-replies');
        if (qr) qr.style.display = 'none';
        document.getElementById('chat-input').value = msg;
        sendMessage();
    };

    window.clearChat = function() {
        const msgs = document.getElementById('chat-messages');
        msgs.innerHTML = '<div class="msg-bot">Chat dihapus. Ada yang bisa saya bantu? 😊</div>';
        
        // Kembalikan tombol quick reply
        msgs.innerHTML += `
            <div id="quick-replies" class="quick-replies-container">
                <button class="quick-btn" onclick="sendQuick('Bagaimana cara daftar anggota?')">✨ Cara daftar?</button>
                <button class="quick-btn" onclick="sendQuick('Apa saja jadwal latihan?')">📅 Jadwal latihan</button>
                <button class="quick-btn" onclick="sendQuick('Tarian apa saja yang diajarkan?')">🎭 Tarian tersedia</button>
                <button class="quick-btn" onclick="sendQuick('Berapa biaya pendaftaran?')">💰 Biaya daftar</button>
                <button class="quick-btn" onclick="sendQuick('Bagaimana cara menghubungi sanggar?')">📞 Kontak</button>
            </div>
        `;

        fetch('{{ route("chatbot.clear") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ session_id: sessionId })
        });
    };

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    // Convert markdown bold to HTML strong, and handle line breaks
    function formatText(text) {
        return text
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
            .replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>')
            .replace(/\*(.*?)\*/g,'<em>$1</em>')
            .replace(/\n/g,'<br>');
    }

    function appendMsg(content, role) {
        const msgs = document.getElementById('chat-messages');
        const div  = document.createElement('div');
        div.className = role === 'user' ? 'msg-user' : 'msg-bot';
        div.innerHTML = formatText(content);
        msgs.appendChild(div);
        scrollBottom();
        return div;
    }

    function appendLoading() {
        const msgs = document.getElementById('chat-messages');
        const div  = document.createElement('div');
        div.className = 'msg-loading';
        div.id = 'typing-indicator';
        div.innerHTML = '<span></span><span></span><span></span>';
        msgs.appendChild(div);
        scrollBottom();
    }

    function removeLoading() {
        document.getElementById('typing-indicator')?.remove();
    }

    function scrollBottom() {
        const msgs = document.getElementById('chat-messages');
        setTimeout(() => msgs.scrollTop = msgs.scrollHeight, 50);
    }

    window.sendMessage = async function() {
        const input   = document.getElementById('chat-input');
        const message = input.value.trim();
        const sendBtn = document.getElementById('send-btn');
        
        if (!message) return;

        input.value  = '';
        input.disabled = true;
        sendBtn.disabled = true;

        const qr = document.getElementById('quick-replies');
        if (qr) qr.style.display = 'none';

        appendMsg(message, 'user');
        appendLoading();

        try {
            const res = await fetch('{{ route("chatbot.chat") }}', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify({ message, session_id: sessionId }),
            });

            const data = await res.json();
            removeLoading();

            if (data.success) {
                appendMsg(data.reply, 'bot');
            } else {
                appendMsg('Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot');
            }
        } catch (err) {
            removeLoading();
            appendMsg('Maaf, koneksi bermasalah. Pastikan server berjalan.', 'bot');
        } finally {
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();
        }
    };
})();
</script>
