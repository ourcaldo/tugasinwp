document.addEventListener('DOMContentLoaded', () => {

    // Mobile Menu Toggle
    const mobileBtn = document.querySelector('.mobile-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');

    if (mobileBtn && mobileMenu) {
        const icon = mobileBtn.querySelector('i');

        mobileBtn.addEventListener('click', () => {
            const isOpening = !mobileMenu.classList.contains('active');

            // Toggle menu
            mobileMenu.classList.toggle('active');

            // Toggle body class for CSS targeting (MUST happen for close button visibility)
            if (isOpening) {
                document.body.classList.add('menu-open');
                document.body.style.overflow = 'hidden';
                if (icon) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                }
            } else {
                document.body.classList.remove('menu-open');
                document.body.style.overflow = '';
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }


    // Close menu when link clicked
    document.querySelectorAll('.mobile-menu a').forEach(link => {
        link.addEventListener('click', () => {
            if (mobileMenu) {
                mobileMenu.classList.remove('active');
                document.body.classList.remove('menu-open');
                document.body.style.overflow = '';
            }
            const icon = document.querySelector('.mobile-toggle i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    });

    // Close button inside menu
    const closeBtn = document.querySelector('.mobile-menu-close');
    if (closeBtn && mobileMenu) {
        closeBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
            document.body.classList.remove('menu-open');
            document.body.style.overflow = '';
            const icon = document.querySelector('.mobile-toggle i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }


    // Mobile Accordion Toggle
    document.querySelectorAll('.accordion-header').forEach(header => {
        header.addEventListener('click', () => {
            header.classList.toggle('active');
            const accordion = header.parentElement;
            const content = accordion.querySelector('.accordion-content');
            if (content) {
                content.classList.toggle('active');
            }
        });
    });

    // FAQ Accordion Toggle
    document.querySelectorAll('.faq-header').forEach(header => {
        header.addEventListener('click', () => {
            const faqItem = header.parentElement;
            const content = faqItem.querySelector('.faq-content');
            const isActive = faqItem.classList.contains('active');

            // Close all other FAQ items
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('active');
                item.querySelector('.faq-content').style.display = 'none';
            });

            // Toggle current item
            if (!isActive) {
                faqItem.classList.add('active');
                content.style.display = 'block';
            }
        });
    });

    // Intersection Observer for Animation
    // We will add 'fade-up' class to elements
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px"
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Elements to animate
    const animateElements = document.querySelectorAll('.value-card, .service-card, .talent-card, .section-header, .hero-content');

    // Auto-Slide Carousel Logic
    const initCarousel = () => {
        const cards = document.querySelectorAll('.talent-card-hero');
        if (!cards.length) return;

        let currentIndex = 0;
        const totalCards = cards.length;

        const updateCards = () => {
            // Reset all
            cards.forEach(card => {
                card.classList.remove('active', 'next', 'prev');
            });

            // Set Active
            cards[currentIndex].classList.add('active');

            // Set Next (Loop around)
            const nextIndex = (currentIndex + 1) % totalCards;
            cards[nextIndex].classList.add('next');

            // Set Prev (Loop back)
            const prevIndex = (currentIndex - 1 + totalCards) % totalCards;
            cards[prevIndex].classList.add('prev');
        };

        // Initialize positions
        updateCards();

        // Auto slide every 3s
        setInterval(() => {
            currentIndex = (currentIndex + 1) % totalCards;
            updateCards();
        }, 3000);
    };

    initCarousel();

    // Tutor Carousel Auto-Slide Logic (3s smooth sliding)
    const initTutorCarousel = () => {
        const cards = document.querySelectorAll('.tutor-card');
        if (!cards.length) return;

        let currentIndex = 0;
        const totalCards = cards.length;

        const updateTutorCards = () => {
            // Reset all cards
            cards.forEach(card => {
                card.classList.remove('active', 'next', 'prev');
            });

            // Set Active (center card)
            cards[currentIndex].classList.add('active');

            // Set Next (right of center)
            const nextIndex = (currentIndex + 1) % totalCards;
            cards[nextIndex].classList.add('next');

            // Set Prev (left of center)
            const prevIndex = (currentIndex - 1 + totalCards) % totalCards;
            cards[prevIndex].classList.add('prev');
        };

        // Initialize
        updateTutorCards();

        // Auto slide every 4 seconds (slower, smoother)
        setInterval(() => {
            currentIndex = (currentIndex + 1) % totalCards;
            updateTutorCards();
        }, 4000);
    };

    initTutorCarousel();

    // Add CSS transition style dynamically
    const styleSheet = document.createElement("style");
    styleSheet.textContent = `
        .value-card, .service-card, .talent-card, .section-header, .hero-content {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .in-view {
            opacity: 1;
            transform: translateY(0);
        }
        /* Stagger delays */
        .value-card:nth-child(1) { transition-delay: 0.1s; }
        .value-card:nth-child(2) { transition-delay: 0.2s; }
        .value-card:nth-child(3) { transition-delay: 0.3s; }
        
        .talent-card:nth-child(1) { transition-delay: 0.1s; }
        .talent-card:nth-child(2) { transition-delay: 0.2s; }
        .talent-card:nth-child(3) { transition-delay: 0.3s; }
        .talent-card:nth-child(4) { transition-delay: 0.4s; }
    `;
    document.head.appendChild(styleSheet);

    animateElements.forEach(el => observer.observe(el));

    // ========================================
    // TABLE OF CONTENTS FUNCTIONALITY
    // ========================================

    // Desktop TOC Toggle
    const tocSidebar = document.getElementById('toc-sidebar');
    const tocToggle = document.getElementById('toc-toggle');
    const tocClose = document.getElementById('toc-close');

    if (tocSidebar && tocToggle && tocClose) {
        // Hide TOC
        tocClose.addEventListener('click', () => {
            tocSidebar.classList.add('hidden');
            tocToggle.classList.add('visible');
        });

        // Show TOC
        tocToggle.addEventListener('click', () => {
            tocSidebar.classList.remove('hidden');
            tocToggle.classList.remove('visible');
        });
    }

    // Mobile TOC Toggle
    const tocMobile = document.getElementById('toc-mobile');
    const tocMobileToggle = document.getElementById('toc-mobile-toggle');

    if (tocMobile && tocMobileToggle) {
        tocMobileToggle.addEventListener('click', () => {
            tocMobile.classList.toggle('expanded');
            const isExpanded = tocMobile.classList.contains('expanded');
            tocMobileToggle.setAttribute('aria-expanded', isExpanded);
        });

        // Close after clicking a link
        tocMobile.querySelectorAll('.toc-link').forEach(link => {
            link.addEventListener('click', () => {
                tocMobile.classList.remove('expanded');
                tocMobileToggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // TOC Smooth Scroll
    document.querySelectorAll('.toc-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('data-target') || link.getAttribute('href').substring(1);
            const targetEl = document.getElementById(targetId);

            if (targetEl) {
                const headerOffset = 100;
                const elementPosition = targetEl.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // TOC Scroll Spy - Highlight active section AND auto-scroll TOC to keep active visible
    const tocLinksDesktop = document.querySelectorAll('.toc-sidebar .toc-link');
    const tocLinksMobile = document.querySelectorAll('.toc-mobile .toc-link');
    const tocNav = document.querySelector('.toc-nav');
    const tocMobileContent = document.getElementById('toc-mobile-content');
    const headings = [];

    // Build headings array from desktop TOC links
    tocLinksDesktop.forEach(link => {
        const targetId = link.getAttribute('data-target') || link.getAttribute('href').substring(1);
        const heading = document.getElementById(targetId);
        if (heading) {
            headings.push({ id: targetId, element: heading, desktopLink: link });
        }
    });

    // Map mobile links to headings
    tocLinksMobile.forEach(link => {
        const targetId = link.getAttribute('data-target') || link.getAttribute('href').substring(1);
        const headingObj = headings.find(h => h.id === targetId);
        if (headingObj) {
            headingObj.mobileLink = link;
        }
    });

    if (headings.length > 0) {
        // Track the last active ID to avoid unnecessary scrolling
        let lastActiveId = null;

        const scrollTocToActive = (desktopLink, mobileLink) => {
            // Use scrollIntoView for desktop TOC - this handles all offset calculations
            if (desktopLink && tocNav) {
                desktopLink.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center',
                    inline: 'nearest'
                });
            }

            // Use scrollIntoView for mobile TOC
            if (mobileLink && tocMobileContent) {
                mobileLink.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center',
                    inline: 'nearest'
                });
            }
        };

        const updateActiveLink = () => {
            const scrollPosition = window.scrollY + 150;

            let currentActiveId = null;
            let activeDesktopLink = null;
            let activeMobileLink = null;

            headings.forEach(({ id, element, desktopLink, mobileLink }) => {
                if (element.offsetTop <= scrollPosition) {
                    currentActiveId = id;
                    activeDesktopLink = desktopLink;
                    activeMobileLink = mobileLink;
                }
            });

            // Update active class on all links
            tocLinksDesktop.forEach(link => {
                const linkTarget = link.getAttribute('data-target') || link.getAttribute('href').substring(1);
                link.classList.toggle('active', linkTarget === currentActiveId);
            });

            tocLinksMobile.forEach(link => {
                const linkTarget = link.getAttribute('data-target') || link.getAttribute('href').substring(1);
                link.classList.toggle('active', linkTarget === currentActiveId);
            });

            // Auto-scroll TOC only when active changes
            if (currentActiveId && currentActiveId !== lastActiveId) {
                lastActiveId = currentActiveId;
                scrollTocToActive(activeDesktopLink, activeMobileLink);
            }
        };

        window.addEventListener('scroll', updateActiveLink, { passive: true });
        updateActiveLink(); // Initial check
    }

    // ========================================
    // SEO CONTENT BOX EXPAND/COLLAPSE
    // ========================================
    const seoToggle = document.getElementById('seo-content-toggle');
    const seoBox = document.getElementById('seo-content-box');

    if (seoToggle && seoBox) {
        seoToggle.addEventListener('click', () => {
            const isExpanded = seoBox.classList.contains('expanded');
            const expandText = seoToggle.getAttribute('data-expand-text');
            const collapseText = seoToggle.getAttribute('data-collapse-text');
            const btnText = seoToggle.querySelector('.btn-text');

            if (isExpanded) {
                // Collapse
                seoBox.classList.remove('expanded');
                if (btnText) btnText.textContent = expandText;

                // Scroll back to top of box
                seoBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                // Expand
                seoBox.classList.add('expanded');
                if (btnText) btnText.textContent = collapseText;
            }
        });
    }

    // ========================================
    // YouTube Facade - Click to Load
    // ========================================
    document.querySelectorAll('.youtube-facade').forEach(facade => {
        facade.addEventListener('click', function () {
            // Prevent double-click
            if (this.classList.contains('loading') || this.querySelector('iframe')) {
                return;
            }

            const embedUrl = this.dataset.embedUrl;
            if (!embedUrl) return;

            // Add loading state
            this.classList.add('loading');

            // Create iframe
            const iframe = document.createElement('iframe');
            iframe.src = embedUrl;
            iframe.title = 'YouTube Video';
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
            iframe.setAttribute('allowfullscreen', '');

            // When iframe loads, remove loading state
            iframe.addEventListener('load', () => {
                this.classList.remove('loading');
            });

            // Append iframe
            this.appendChild(iframe);
        });
    });

});
