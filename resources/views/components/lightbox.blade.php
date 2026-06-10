{{-- Lightbox Component - Auto activate for all images with lightbox-img class --}}
{{-- No @push/@stack needed - injects directly into the page --}}

<style>
/* Lightbox Overlay */
#findit-lightbox-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.92);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.25s ease, visibility 0.25s ease;
}

#findit-lightbox-overlay.open {
    opacity: 1;
    visibility: visible;
}

/* Lightbox Content */
#findit-lightbox-inner {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    transform: scale(0.92);
    transition: transform 0.25s ease;
}

#findit-lightbox-overlay.open #findit-lightbox-inner {
    transform: scale(1);
}

/* Image */
#findit-lightbox-img {
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
    border-radius: 10px;
    box-shadow: 0 30px 100px rgba(0, 0, 0, 0.6);
}

/* Close Button */
.findit-lightbox-close {
    position: absolute;
    top: -60px;
    right: 0;
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 28px;
    line-height: 1;
    font-weight: 300;
    transition: all 0.2s ease;
    z-index: 10;
}

.findit-lightbox-close:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: scale(1.1);
}

/* Caption */
#findit-lightbox-caption {
    margin-top: 16px;
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    font-weight: 500;
    text-align: center;
}

/* Cursor on clickable images */
img.lightbox-img {
    cursor: zoom-in;
    transition: opacity 0.15s ease;
}

img.lightbox-img:hover {
    opacity: 0.88;
}
</style>

{{-- Lightbox Overlay --}}
<div id="findit-lightbox-overlay">
    <div id="findit-lightbox-inner">
        <button class="findit-lightbox-close" title="Tutup (ESC)" aria-label="Tutup">&times;</button>
        <img id="findit-lightbox-img" src="" alt="">
        <div id="findit-lightbox-caption"></div>
    </div>
</div>

<script>
(function() {
    'use strict';

    var overlay = document.getElementById('findit-lightbox-overlay');
    var inner = document.getElementById('findit-lightbox-inner');
    var img = document.getElementById('findit-lightbox-img');
    var caption = document.getElementById('findit-lightbox-caption');
    var closeBtn = document.querySelector('.findit-lightbox-close');

    var isOpen = false;

    function open(src, text) {
        if (!src) return;
        img.src = src;
        img.alt = text || '';
        caption.textContent = text || '';
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
        isOpen = true;
        closeBtn.focus();
    }

    function close() {
        overlay.classList.remove('open');
        document.body.style.overflow = '';
        isOpen = false;
        setTimeout(function() {
            img.src = '';
            img.alt = '';
            caption.textContent = '';
        }, 300);
    }

    // Close button
    closeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        close();
    });

    // Click outside to close
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            close();
        }
    });

    // ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) {
            close();
        }
    });

    // CLICK HANDLER - CAPTURE PHASE (runs BEFORE link navigation)
    document.addEventListener('click', function(e) {
        var target = e.target;

        // If lightbox is open and clicking inside, check for close button
        if (isOpen && inner.contains(target)) {
            // Don't close if clicking on the image itself
            return;
        }

        // Check if clicked on lightbox-img image
        if (target.tagName === 'IMG' && target.classList.contains('lightbox-img')) {
            e.preventDefault();
            e.stopPropagation();
            var src = target.src || target.dataset.full || '';
            var cap = target.alt || target.dataset.caption || '';
            open(src, cap);
            return;
        }

        // Check if clicking inside a lightbox-trigger container
        var trigger = target.closest('.lightbox-trigger');
        if (trigger) {
            e.preventDefault();
            e.stopPropagation();
            var triggerImg = trigger.querySelector('img');
            if (triggerImg) {
                var src = triggerImg.src || triggerImg.dataset.full || '';
                var cap = triggerImg.alt || trigger.dataset.caption || triggerImg.dataset.caption || '';
                open(src, cap);
            }
        }
    }, true); // CAPTURE PHASE = true (runs before parent <a> gets the event)

    // Auto-attach lightbox-img class to foto_barang images
    function attach() {
        var images = document.querySelectorAll('img[src*="storage/foto_barang"]');
        images.forEach(function(i) {
            if (!i.classList.contains('lightbox-img')) {
                i.classList.add('lightbox-img');
            }
        });
    }

    attach();

    // Watch for new content (pagination, AJAX)
    if (typeof MutationObserver !== 'undefined') {
        new MutationObserver(function(mutations) {
            mutations.forEach(function(m) {
                if (m.addedNodes.length > 0) {
                    setTimeout(attach, 50);
                }
            });
        }).observe(document.body, { childList: true, subtree: true });
    }

})();
</script>