// DNA Vacation - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Active nav link
    const currentPath = window.location.pathname.split('/').pop();
    document.querySelectorAll('.navbar .nav-link').forEach(link => {
        const href = link.getAttribute('href');
        if (!href) return;
        const linkPage = href.split('/').pop();
        if (linkPage && linkPage === currentPath) {
            link.classList.add('active');
        }
        // Homepage
        if ((currentPath === '' || currentPath === 'index.php') && (linkPage === '' || linkPage === '/')) {
            link.classList.add('active');
        }
    });

    // Smooth scroll for anchor links (excluding tab toggles)
    document.querySelectorAll('a[href^="#"]:not([data-bs-toggle])').forEach(a => {
        a.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#' || href.length < 2) return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                const yOffset = -80;
                const y = target.getBoundingClientRect().top + window.pageYOffset + yOffset;
                window.scrollTo({ top: y, behavior: 'smooth' });
            }
        });
    });

    // Admin sidebar toggle (mobile)
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('show'));
        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }

    // Image preview on file input
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        input.addEventListener('change', function() {
            const preview = document.getElementById(this.dataset.preview);
            if (preview && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => preview.src = e.target.result;
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // Auto-dismiss success alerts
    document.querySelectorAll('.alert-success.alert-dismissible').forEach(alert => {
        setTimeout(() => {
            try {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                if (bsAlert) bsAlert.close();
            } catch(e) {}
        }, 8000);
    });

    // Format WhatsApp phone input to numbers only
    document.querySelectorAll('input[name*="whatsapp"], input[name*="phone"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9+]/g, '');
        });
    });

    // Confirm before destructive actions
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
});

// Placeholder image handler - shows brand-styled SVG if image fails to load
function handleImgError(img) {
    img.onerror = null;
    img.src = 'data:image/svg+xml;utf8,' + encodeURIComponent(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300">' +
        '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">' +
        '<stop offset="0" stop-color="#E0F7FA"/><stop offset="1" stop-color="#F1F5F9"/></linearGradient></defs>' +
        '<rect fill="url(#g)" width="400" height="300"/>' +
        '<circle cx="120" cy="120" r="45" fill="#0891B2" opacity=".15"/>' +
        '<path fill="#0891B2" opacity=".2" d="M0 250L40 240C80 230 160 210 240 215C320 220 400 240 400 240L400 300L0 300Z"/>' +
        '<text x="200" y="165" text-anchor="middle" fill="#0891B2" font-family="sans-serif" font-size="18" font-weight="700">DNA Vacation</text>' +
        '<text x="200" y="190" text-anchor="middle" fill="#64748B" font-family="sans-serif" font-size="11">Foto akan ditambahkan</text>' +
        '</svg>'
    );
    img.style.objectFit = 'cover';
}
