<?php
/**
 * WhatsApp Floating Widget
 *
 * Displays a floating WhatsApp button in the bottom-right corner with a chat bubble CTA.
 *
 * @package TugasinWP
 * @since 2.8.0
 */

// Check if widget is enabled (default: enabled)
$wa_enabled = get_option( 'tugasin_wa_widget_enabled', '1' );
if ( $wa_enabled === '' || $wa_enabled === '0' ) {
    return;
}

// Get WhatsApp URL using the existing helper function (has proper fallbacks)
$wa_url = tugasin_get_whatsapp_url();

// Get widget-specific settings
$wa_cta   = get_option( 'tugasin_wa_widget_cta', 'Halo! Kamu butuh bantuan? Tim Tugasin siap bantu kamu. Yuk konsultasi sekarang, GRATIS!' );
$wa_delay = get_option( 'tugasin_wa_widget_delay', 3 );
?>

<div class="tugasin-wa-widget" data-delay="<?php echo esc_attr( $wa_delay ); ?>">
    <!-- Chat Bubble -->
    <div class="wa-bubble" aria-hidden="true">
        <button type="button" class="wa-bubble-close" aria-label="<?php esc_attr_e( 'Close', 'tugasin' ); ?>">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1L11 11M1 11L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <div class="wa-bubble-content">
            <div class="wa-bubble-avatar">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 5C13.66 5 15 6.34 15 8C15 9.66 13.66 11 12 11C10.34 11 9 9.66 9 8C9 6.34 10.34 5 12 5ZM12 19.2C9.5 19.2 7.29 17.92 6 15.98C6.03 13.99 10 12.9 12 12.9C13.99 12.9 17.97 13.99 18 15.98C16.71 17.92 14.5 19.2 12 19.2Z"/>
                </svg>
            </div>
            <div class="wa-bubble-text">
                <span class="wa-bubble-name"><?php esc_html_e( 'Tim Tugasin', 'tugasin' ); ?></span>
                <p><?php echo esc_html( $wa_cta ); ?></p>
            </div>
        </div>
        <a href="<?php echo esc_url( $wa_url ); ?>" class="wa-bubble-cta" target="_blank" rel="noopener noreferrer">
            <?php esc_html_e( 'Chat Sekarang', 'tugasin' ); ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
            </svg>
        </a>
    </div>
    
    <!-- WhatsApp Button -->
    <a href="<?php echo esc_url( $wa_url ); ?>" 
       class="wa-button" 
       target="_blank" 
       rel="noopener noreferrer"
       aria-label="<?php esc_attr_e( 'Chat via WhatsApp', 'tugasin' ); ?>"
       title="<?php esc_attr_e( 'Chat via WhatsApp', 'tugasin' ); ?>">
        <svg viewBox="0 0 32 32" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M16.002 2.667C8.638 2.667 2.669 8.636 2.669 16c0 2.364.618 4.667 1.79 6.698L2.667 29.333l6.87-1.803A13.252 13.252 0 0016.002 29.333c7.364 0 13.333-5.97 13.333-13.333S23.366 2.667 16.002 2.667zm0 24.444c-2.088 0-4.12-.558-5.903-1.617l-.424-.252-4.387 1.151 1.17-4.282-.275-.44A11.027 11.027 0 014.89 16c0-6.127 4.985-11.111 11.112-11.111 6.127 0 11.111 4.984 11.111 11.111s-4.984 11.111-11.111 11.111zm6.098-8.32c-.334-.168-1.977-.976-2.283-1.088-.307-.112-.53-.167-.753.168-.223.335-.865 1.088-1.06 1.311-.195.224-.39.252-.724.084-.334-.167-1.41-.52-2.686-1.658-.993-.886-1.664-1.98-1.859-2.314-.195-.335-.021-.516.147-.683.151-.15.334-.39.502-.586.167-.195.223-.335.335-.558.111-.224.056-.419-.028-.586-.084-.168-.753-1.815-1.032-2.485-.272-.653-.548-.565-.753-.576-.195-.01-.419-.012-.642-.012a1.232 1.232 0 00-.893.419c-.307.335-1.172 1.144-1.172 2.79s1.2 3.236 1.367 3.46c.168.223 2.36 3.604 5.718 5.053.799.345 1.423.55 1.909.705.802.255 1.533.219 2.111.133.644-.096 1.977-.809 2.256-1.59.28-.78.28-1.45.196-1.59-.084-.14-.307-.223-.64-.39z"/>
        </svg>
    </a>
</div>

<style>
/* WhatsApp Widget Styles */
.tugasin-wa-widget {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

/* WhatsApp Button - Using theme emerald color */
.wa-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #064e3b 0%, #059669 100%);
    border-radius: 50%;
    color: white;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(6, 78, 59, 0.35);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    animation: wa-pulse 2.5s ease-in-out infinite;
}

.wa-button:hover {
    transform: scale(1.08);
    box-shadow: 0 8px 24px rgba(6, 78, 59, 0.45);
}

.wa-button svg {
    width: 32px;
    height: 32px;
}

@keyframes wa-pulse {
    0%, 100% { 
        box-shadow: 0 4px 16px rgba(6, 78, 59, 0.35), 0 0 0 0 rgba(6, 78, 59, 0.3); 
    }
    50% { 
        box-shadow: 0 4px 16px rgba(6, 78, 59, 0.35), 0 0 0 14px rgba(6, 78, 59, 0); 
    }
}

/* Chat Bubble */
.wa-bubble {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 320px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.18);
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px) scale(0.9);
    transition: opacity 0.4s ease, transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), visibility 0.4s;
    overflow: hidden;
}

.wa-bubble.visible {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

.wa-bubble-close {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 24px;
    height: 24px;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: rgba(255, 255, 255, 0.8);
    transition: all 0.2s ease;
    z-index: 1;
}

.wa-bubble-close:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white;
}

.wa-bubble-content {
    display: flex;
    gap: 12px;
    padding: 20px;
    background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
    color: white;
}

.wa-bubble-avatar {
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.wa-bubble-avatar svg {
    opacity: 0.95;
}

.wa-bubble-text {
    flex: 1;
}

.wa-bubble-name {
    font-weight: 600;
    font-size: 14px;
    display: block;
    margin-bottom: 4px;
}

.wa-bubble-text p {
    margin: 0;
    font-size: 13px;
    line-height: 1.5;
    opacity: 0.95;
}

.wa-bubble-cta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 20px;
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: white;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.25s ease;
}

.wa-bubble-cta:hover {
    background: linear-gradient(135deg, #047857 0%, #059669 100%);
}

.wa-bubble-cta svg {
    flex-shrink: 0;
}

/* Arrow pointer */
.wa-bubble::after {
    content: '';
    position: absolute;
    bottom: -8px;
    right: 24px;
    width: 16px;
    height: 16px;
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    transform: rotate(45deg);
    box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.08);
}

/* Mobile adjustments */
@media (max-width: 480px) {
    .tugasin-wa-widget {
        bottom: 16px;
        right: 16px;
    }
    
    .wa-button {
        width: 56px;
        height: 56px;
    }
    
    .wa-button svg {
        width: 28px;
        height: 28px;
    }
    
    .wa-bubble {
        width: calc(100vw - 32px);
        right: -8px;
    }
}
</style>

<script>
(function() {
    var widget = document.querySelector('.tugasin-wa-widget');
    if (!widget) return;
    
    var bubble = widget.querySelector('.wa-bubble');
    var closeBtn = widget.querySelector('.wa-bubble-close');
    var delay = parseInt(widget.dataset.delay, 10) * 1000;
    
    // Check if user dismissed previously (within this session)
    var dismissed = sessionStorage.getItem('tugasin_wa_dismissed');
    
    // Show bubble after delay (if not dismissed and delay > 0)
    if (!dismissed && delay > 0) {
        setTimeout(function() {
            bubble.classList.add('visible');
        }, delay);
    }
    
    // Close button handler
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            bubble.classList.remove('visible');
            sessionStorage.setItem('tugasin_wa_dismissed', 'true');
        });
    }
})();
</script>
