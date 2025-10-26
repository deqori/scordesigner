<!-- Contact Section -->
<section id="contact" class="contact">
    <h2 class="section-title">Démarrons Ensemble</h2>
    <p class="section-subtitle">Vous avez un projet ? Parlons-en ! Nous vous répondons sous 24h.</p>
    
    <!-- Formulaire de contact -->
    <form class="contact-form" id="contactForm">
        <div class="form-group">
            <label for="name">Nom *</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="phone">Téléphone</label>
            <input type="tel" id="phone" name="phone">
        </div>
        <div class="form-group">
            <label for="message">Votre projet *</label>
            <textarea id="message" name="message" required></textarea>
        </div>
        <button type="submit" class="submit-button" id="submitBtn">Envoyer le message</button>
    </form>
    
    <div class="form-message" id="formMessage"></div>
</section>
<hr>