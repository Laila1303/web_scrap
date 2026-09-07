<script>
        function openCapsuleModal(id) {
            const dataEl = document.getElementById('capsule-data-' + id);
            if (!dataEl) return;
            const sender = dataEl.getAttribute('data-sender') || 'Sahabat';
            const date = dataEl.getAttribute('data-date') || '';
            const content = dataEl.innerHTML;

            const modalSender = document.getElementById('modal-sender');
            const modalContent = document.getElementById('modal-content');
            const modalDate = document.getElementById('modal-date');
            const modal = document.getElementById('read-modal');

            if (modalSender) modalSender.textContent = 'Dari: ' + sender;
            if (modalContent) modalContent.innerHTML = content;
            if (modalDate) modalDate.textContent = 'Dikirim pada: ' + date;
            if (modal) modal.style.display = 'flex';
        }

        function closeCapsuleModal() {
            const modal = document.getElementById('read-modal');
            if (modal) modal.style.display = 'none';
        }

        window.openCapsule = openCapsuleModal;
        window.closeModal = closeCapsuleModal;

        function initKapsul() {
            // Event delegation untuk tombol BACA SEKARANG
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-open-capsule') || (e.target.tagName === 'BUTTON' && e.target.getAttribute('onclick')?.includes('openCapsule') ? e.target : null);
                if (btn) {
                    const id = btn.getAttribute('data-capsule-id');
                    if (id) {
                        e.preventDefault();
                        openCapsuleModal(id);
                    }
                }
            });

            // Tombol Tutup Modal
            const closeX = document.getElementById('btn-close-modal-x');
            const closeFooter = document.getElementById('btn-close-modal-footer');
            const modalBg = document.getElementById('read-modal');

            if (closeX) closeX.onclick = closeCapsuleModal;
            if (closeFooter) closeFooter.onclick = closeCapsuleModal;
            if (modalBg) {
                modalBg.onclick = function(e) {
                    if (e.target === modalBg) closeCapsuleModal();
                };
            }

            // Countdown timer
            function updateCountdowns() {
                const now = new Date().getTime();
                const timers = document.querySelectorAll('.countdown-timer');
                
                timers.forEach(timer => {
                    const targetAttr = timer.getAttribute('data-target');
                    if (!targetAttr) return;

                    const targetDate = new Date(targetAttr).getTime();
                    const diff = targetDate - now;
                    
                    if (diff <= 0) {
                        timer.innerHTML = "<span class='text-green-700 font-bold'>Sudah bisa dibuka! Muat ulang halaman.</span>";
                    } else {
                        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60)) / (1000 * 60));
                        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                        
                        timer.innerHTML = `Terkunci. Buka dalam: <span class="font-mono font-bold">${days}h ${hours}j ${minutes}m ${seconds}d</span>`;
                    }
                });
            }
            
            setInterval(updateCountdowns, 1000);
            updateCountdowns();
        }

        // Langsung jalankan tanpa menunggu DOMContentLoaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initKapsul);
        } else {
            initKapsul();
        }
    </script>