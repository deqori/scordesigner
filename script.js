// Burger menu toggle
const burger = document.getElementById('burger');
const navMenu = document.getElementById('nav-menu');

burger.addEventListener('click', () => {
    burger.classList.toggle('active');
    navMenu.classList.toggle('active');
});

// Close menu when clicking on a link
document.querySelectorAll('#nav-menu a').forEach(link => {
    link.addEventListener('click', () => {
        burger.classList.remove('active');
        navMenu.classList.remove('active');
    });
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// Contact form submission
const contactForm = document.getElementById('contactForm');
const submitBtn = document.getElementById('submitBtn');
const formMessage = document.getElementById('formMessage');

contactForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span style="display: inline-block; animation: spin 1s linear infinite;">⏳</span> Envoi en cours...';
    
    // Hide previous messages
    formMessage.classList.remove('show', 'success', 'error');

    // Get form data
    const formData = new FormData(contactForm);

    try {
        const response = await fetch('send-email.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            formMessage.className = 'form-message success show';
            formMessage.innerHTML = '<strong>🎉 Message envoyé avec succès !</strong><div style="margin-top: 10px; font-size: 15px; line-height: 1.6;">' + data.message + '</div>';
            contactForm.reset();
            
            // Scroll to success message smoothly
            setTimeout(() => {
                formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 200);
        } else {
            formMessage.className = 'form-message error show';
            formMessage.innerHTML = '<strong>⚠️ Erreur d\'envoi</strong><div style="margin-top: 10px;">' + data.message + '</div>';
            
            // Scroll to error message
            setTimeout(() => {
                formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 200);
        }
    } catch (error) {
        formMessage.className = 'form-message error show';
        formMessage.innerHTML = '<strong>⚠️ Erreur de connexion</strong><div style="margin-top: 10px;">Impossible d\'envoyer le message. Vérifiez votre connexion et réessayez.</div>';
        
        setTimeout(() => {
            formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 200);
    }

    // Re-enable button
    submitBtn.disabled = false;
    submitBtn.textContent = 'Envoyer le message';
});

// Add spin animation
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);

// Pricing cards slider functionality
const initPricingSlider = () => {
    const pricingGrid = document.querySelector('.pricing-grid');
    const pricingCards = document.querySelectorAll('.pricing-card');

    if (!pricingGrid || pricingCards.length === 0) return;

    // Function to update active card based on scroll position
    const updateActiveCard = () => {
        const scrollLeft = pricingGrid.scrollLeft;
        const cardWidth = pricingCards[0].offsetWidth;
        const gap = parseFloat(getComputedStyle(pricingGrid).gap) || 0;

        // Calculate which card is in the center
        const centerPosition = scrollLeft + (pricingGrid.offsetWidth / 2);
        let activeIndex = 0;

        pricingCards.forEach((card, index) => {
            const cardLeft = card.offsetLeft;
            const cardCenter = cardLeft + (cardWidth / 2);

            // Check if this card's center is closest to viewport center
            if (Math.abs(cardCenter - centerPosition) < cardWidth / 2) {
                activeIndex = index;
            }
        });

        // Update active class
        pricingCards.forEach((card, index) => {
            if (index === activeIndex) {
                card.classList.add('active');
            } else {
                card.classList.remove('active');
            }
        });
    };

    // Throttle scroll event for better performance
    let scrollTimeout;
    pricingGrid.addEventListener('scroll', () => {
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        scrollTimeout = setTimeout(updateActiveCard, 50);
    });

    // Initial update
    updateActiveCard();

    // Update on window resize
    window.addEventListener('resize', updateActiveCard);
};

// Initialize pricing slider when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPricingSlider);
} else {
    initPricingSlider();
}