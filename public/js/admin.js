/* Admin Panel JS — Sanggar Mulya Bhakti */
document.addEventListener('DOMContentLoaded', () => {

    // ── Sidebar toggle ──────────────────────────────
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggle   = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('sidebarClose');

    const openSidebar  = () => { sidebar?.classList.add('open'); overlay?.classList.add('show'); document.body.style.overflow='hidden'; };
    const closeSidebar = () => { sidebar?.classList.remove('open'); overlay?.classList.remove('show'); document.body.style.overflow=''; };
    toggle?.addEventListener('click', openSidebar);
    closeBtn?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    // ── Flash auto dismiss ──────────────────────────
    const flash = document.getElementById('flashMsg');
    if (flash) setTimeout(() => flash.style.opacity='0', 4000);

    // ── Tabs ────────────────────────────────────────
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const group = btn.closest('[data-tabs]') || document.body;
            group.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            group.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            const target = btn.dataset.tab;
            const panel = document.getElementById(target);
            if (panel) panel.classList.add('active');
            
            // Simpan status tab aktif untuk halaman ini
            sessionStorage.setItem('active_tab_' + window.location.pathname, target);
        });
    });

    // Kembalikan tab aktif yang tersimpan saat memuat halaman
    const savedTabId = sessionStorage.getItem('active_tab_' + window.location.pathname);
    if (savedTabId) {
        const savedBtn = document.querySelector(`.tab-btn[data-tab="${savedTabId}"]`);
        if (savedBtn) {
            const group = savedBtn.closest('[data-tabs]') || document.body;
            group.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            group.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            savedBtn.classList.add('active');
            const panel = document.getElementById(savedTabId);
            if (panel) panel.classList.add('active');
        }
    }

    // ── File upload preview ─────────────────────────
    document.querySelectorAll('.file-upload-area').forEach(area => {
        const input   = area.querySelector('input[type="file"]');
        const preview = area.parentElement.querySelector('.file-preview img');

        if(input) {
            input.addEventListener('click', e => e.stopPropagation());
        }

        area.addEventListener('click', () => input?.click());
        area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('dragover'); });
        area.addEventListener('dragleave', ()=> area.classList.remove('dragover'));
        area.addEventListener('drop', e => {
            e.preventDefault(); area.classList.remove('dragover');
            if (e.dataTransfer.files[0] && input) {
                const dt = new DataTransfer();
                dt.items.add(e.dataTransfer.files[0]);
                input.files = dt.files;
                input.dispatchEvent(new Event('change'));
            }
        });
        input?.addEventListener('change', () => {
            const file = input.files[0];
            if (file && preview && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => { 
                    preview.src = e.target.result; 
                    const previewWrapper = preview.closest('.file-preview');
                    if(previewWrapper) previewWrapper.style.display='block'; 
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // ── Modals ──────────────────────────────────────
    document.querySelectorAll('[data-modal-open]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = document.getElementById(btn.dataset.modalOpen);
            modal?.classList.add('show');
        });
    });
    document.querySelectorAll('[data-modal-close], .modal-bg').forEach(el => {
        el.addEventListener('click', (e) => {
            if (e.target === el) el.closest('.modal-bg')?.classList.remove('show');
        });
    });

    // ── Delete confirm ──────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', e => {
            if (!confirm(btn.dataset.confirm || 'Apakah Anda yakin ingin menghapus ini?')) e.preventDefault();
        });
    });

    // ── Misi dynamic rows ───────────────────────────
    const misiWrap = document.getElementById('misiWrap');
    const addMisi  = document.getElementById('addMisi');
    if (misiWrap && addMisi) {
        addMisi.addEventListener('click', () => {
            const idx = misiWrap.querySelectorAll('.misi-row').length;
            const row = document.createElement('div');
            row.className = 'misi-row';
            row.style.cssText = 'display:flex;gap:8px;margin-bottom:8px';
            row.innerHTML = `<input type="text" name="misi[${idx}]" class="form-control" placeholder="Poin misi ke-${idx+1}" required>
                <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger btn-sm btn-icon">✕</button>`;
            misiWrap.appendChild(row);
        });
    }

    // ── Penghargaan dynamic rows ────────────────────
    const phWrap = document.getElementById('phWrap');
    const addPh  = document.getElementById('addPenghargaan');
    if (phWrap && addPh) {
        addPh.addEventListener('click', () => {
            const idx = phWrap.querySelectorAll('.ph-row').length;
            const row = document.createElement('div');
            row.className = 'ph-row';
            row.style.cssText = 'display:flex;gap:8px;margin-bottom:8px';
            row.innerHTML = `<input type="text" name="penghargaan[${idx}]" class="form-control" placeholder="Contoh: 🥇 Best Performance">
                <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger btn-sm btn-icon">✕</button>`;
            phWrap.appendChild(row);
        });
    }

    // ── CSRF for fetch ──────────────────────────────
    window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
});