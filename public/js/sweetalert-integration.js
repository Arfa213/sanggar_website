// =========================================================================
// SANGGAR MULYA BHAKTI — Premium SweetAlert2 Global Confirmation Integrator
// =========================================================================

(function() {
    // Inject Custom SweetAlert2 Premium Styles
    const style = document.createElement('style');
    style.innerHTML = `
        .swal-popup-custom {
            border-radius: 20px !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            padding: 2.2rem 1.8rem !important;
            border: 1px solid #E8E0D8 !important;
            background: #FAF8F6 !important; /* Premium Warm Ivory */
            box-shadow: 0 24px 64px rgba(26,26,26,0.12) !important;
        }
        .swal-popup-custom .swal2-title {
            font-family: 'Playfair Display', serif !important;
            font-weight: 700 !important;
            color: #1A1A1A !important;
            font-size: 1.6rem !important;
            margin-bottom: 8px !important;
        }
        .swal-popup-custom .swal2-html-container {
            color: #3D3D3D !important;
            font-size: 0.95rem !important;
            line-height: 1.6 !important;
            margin-top: 10px !important;
        }
        .swal-popup-custom .swal2-icon {
            border-width: 3px !important;
            margin: 0.5rem auto 1.5rem !important;
        }
        /* Custom SweetAlert2 Icons */
        .swal-popup-custom .swal2-icon.swal2-warning {
            border-color: #EA580C !important;
            color: #EA580C !important;
        }
        .swal-popup-custom .swal2-icon.swal2-question {
            border-color: #C65D2E !important;
            color: #C65D2E !important;
        }
        .swal-btn {
            padding: 12px 28px !important;
            font-size: 0.875rem !important;
            font-weight: 700 !important;
            border-radius: 50px !important;
            cursor: pointer !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            border: none !important;
            margin: 6px 8px 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            outline: none !important;
        }
        .swal-btn-confirm {
            background: #C65D2E !important; /* Theme Primary Color */
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(198,93,46,0.2) !important;
        }
        .swal-btn-confirm:hover {
            background: #A34A22 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 16px rgba(198,93,46,0.3) !important;
        }
        .swal-btn-confirm:active {
            transform: translateY(0) !important;
        }
        .swal-btn-cancel {
            background: #F3F4F6 !important;
            color: #4B5563 !important;
            border: 1px solid #E5E7EB !important;
        }
        .swal-btn-cancel:hover {
            background: #E5E7EB !important;
            color: #1F2937 !important;
            transform: translateY(-2px) !important;
        }
        .swal-btn-cancel:active {
            transform: translateY(0) !important;
        }
        
        /* Modernized animation styles */
        .swal2-popup.swal2-show {
            animation: swal-slide-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }
        @keyframes swal-slide-in {
            0% { transform: scale(0.9) translateY(20px); opacity: 0; }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);

    // Global Confirm SweetAlert2 Handler
    function showSwalConfirm(title, message, iconType, confirmText, callback) {
        Swal.fire({
            title: title || 'Konfirmasi Tindakan',
            text: message,
            icon: iconType || 'warning',
            showCancelButton: true,
            confirmButtonText: confirmText || 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'swal-btn swal-btn-confirm',
                cancelButton: 'swal-btn swal-btn-cancel',
                popup: 'swal-popup-custom'
            },
            buttonsStyling: false,
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    }

    // Expose utility globally
    window.showSwalConfirm = showSwalConfirm;

    // 1. Intercept standard Form submissions that have onsubmit="return confirm(...)"
    document.addEventListener('submit', function(e) {
        const form = e.target;
        const onsubmitAttr = form.getAttribute('onsubmit');
        
        if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
            e.preventDefault();
            e.stopPropagation();
            
            // Extract the confirm message
            let match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
            let message = match ? match[1] : 'Apakah Anda yakin ingin melakukan tindakan ini?';
            
            // Customize dialog details based on message contents
            let title = 'Konfirmasi';
            let icon = 'warning';
            let confirmText = 'Ya, Lanjutkan';
            
            if (message.toLowerCase().includes('keluar') || message.toLowerCase().includes('log out')) {
                title = 'Keluar Aplikasi';
                icon = 'question';
                confirmText = 'Ya, Keluar';
            } else if (message.toLowerCase().includes('hapus') || message.toLowerCase().includes('delete') || message.toLowerCase().includes('batalkan')) {
                title = 'Hapus Data';
                icon = 'warning';
                confirmText = 'Ya, Hapus';
            } else if (message.toLowerCase().includes('tutup') || message.toLowerCase().includes('nonaktif')) {
                title = 'Konfirmasi';
                icon = 'warning';
                confirmText = 'Ya, Ubah';
            }
            
            showSwalConfirm(title, message, icon, confirmText, function() {
                // Remove onsubmit temporary to prevent loop, then submit
                form.removeAttribute('onsubmit');
                form.submit();
                form.setAttribute('onsubmit', onsubmitAttr);
            });
        }
    }, true); // Capture phase to run before inline handlers!

    // 2. Intercept clicks on links, buttons that use onclick="return confirm(...)" or data-confirm="..."
    document.addEventListener('click', function(e) {
        const targetConfirm = e.target.closest('[onclick*="confirm("], [data-confirm]');
        if (targetConfirm) {
            // Check if it's handled by form submit interceptor already to avoid double modals
            const formParent = targetConfirm.closest('form');
            if (formParent && formParent.getAttribute('onsubmit') && formParent.getAttribute('onsubmit').includes('confirm(')) {
                return; // Let form submit interceptor handle this
            }
            
            e.preventDefault();
            e.stopPropagation();
            
            let message = '';
            const onclickAttr = targetConfirm.getAttribute('onclick');
            const dataConfirm = targetConfirm.getAttribute('data-confirm');
            
            if (dataConfirm) {
                message = dataConfirm;
            } else if (onclickAttr) {
                let match = onclickAttr.match(/confirm\(['"](.*?)['"]\)/);
                message = match ? match[1] : 'Apakah Anda yakin?';
            }
            
            let title = 'Konfirmasi';
            let icon = 'warning';
            let confirmText = 'Ya, Lanjutkan';
            
            if (message.toLowerCase().includes('keluar') || message.toLowerCase().includes('log out')) {
                title = 'Keluar Aplikasi';
                icon = 'question';
                confirmText = 'Ya, Keluar';
            } else if (message.toLowerCase().includes('hapus') || message.toLowerCase().includes('delete') || message.toLowerCase().includes('batalkan')) {
                title = 'Hapus Data';
                icon = 'warning';
                confirmText = 'Ya, Hapus';
            } else if (message.toLowerCase().includes('tutup') || message.toLowerCase().includes('nonaktif')) {
                title = 'Konfirmasi';
                icon = 'warning';
                confirmText = 'Ya, Ubah';
            }
            
            showSwalConfirm(title, message, icon, confirmText, function() {
                if (targetConfirm.tagName === 'A') {
                    window.location.href = targetConfirm.href;
                } else if (formParent) {
                    // Disable confirmation attributes temporarily to submit
                    let tempOnsubmit = formParent.getAttribute('onsubmit');
                    let tempOnclick = targetConfirm.getAttribute('onclick');
                    
                    if (tempOnsubmit) formParent.removeAttribute('onsubmit');
                    if (tempOnclick) targetConfirm.removeAttribute('onclick');
                    
                    if (targetConfirm.type === 'submit') {
                        formParent.submit();
                    } else {
                        targetConfirm.click();
                    }
                    
                    if (tempOnsubmit) formParent.setAttribute('onsubmit', tempOnsubmit);
                    if (tempOnclick) targetConfirm.setAttribute('onclick', tempOnclick);
                } else {
                    // Just a general button click trigger
                    let tempOnclick = targetConfirm.getAttribute('onclick');
                    if (tempOnclick) targetConfirm.removeAttribute('onclick');
                    targetConfirm.click();
                    if (tempOnclick) targetConfirm.setAttribute('onclick', tempOnclick);
                }
            });
        }
    }, true); // Capture phase to run before inline handlers!
})();
