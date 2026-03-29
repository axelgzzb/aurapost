window.onload = () => {
  const body = document.body;
  // Reinicia la animación CSS del fondo
  body.style.animation = 'none';
  // Reaplica la animación tras un instante
  requestAnimationFrame(() => {
    body.style.animation = '';
  });
};
class AuraPost {
    constructor() {
        this.currentEmotion = null;
        this.init();
    }

    init() {
        this.setupEmotionSelection();
        this.setupPostInteractions();
        this.setupConfetti();
    }

    setupEmotionSelection() {
        const emotionChips = document.querySelectorAll('.emotion-chip');
        const emotionBtns = document.querySelectorAll('.emotion-btn');
        
        emotionChips.forEach(chip => {
            chip.addEventListener('click', () => {
                this.selectEmotion(chip.dataset.emotion);
            });
        });

        emotionBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                emotionBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.currentEmotion = btn.dataset.emotion;
            });
        });
    }

    selectEmotion(emotion) {
        this.currentEmotion = emotion;
        
        // Mostrar emoción seleccionada
        const emotionDisplay = document.getElementById('selectedEmotion');
        if (emotionDisplay) {
            emotionDisplay.textContent = this.getEmotionName(emotion);
            emotionDisplay.className = `text-${emotion}`;
        }

        // Efecto visual
        this.createEmotionAura(emotion);
    }

    getEmotionName(emotion) {
        const names = {
            happy: 'Feliz 😊',
            sad: 'Triste 😢',
            inspired: 'Inspirado 💫',
            angry: 'Enojado 😠',
            love: 'Amoroso ❤️',
            excited: 'Emocionado 🎉',
            peaceful: 'Tranquilo 🕊️',
            motivated: 'Motivado 🚀',
            scary: 'Asustado 😱',
            thoughtful: 'Reflexivo 🤔',
            adventurous: 'Aventurero 🧭',
            grateful: 'Agradecido 🙏'
        };
        return names[emotion] || emotion;
    }

    createEmotionAura(emotion) {
        const aura = document.createElement('div');
        aura.className = `emotion-aura aura-${emotion}`;
        aura.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            border-radius: 50%;
            opacity: 0;
            pointer-events: none;
            z-index: 9999;
        `;
        
        document.body.appendChild(aura);
        
        // Animación
        aura.animate([
            { transform: 'translate(-50%, -50%) scale(0)', opacity: 0.8 },
            { transform: 'translate(-50%, -50%) scale(3)', opacity: 0 }
        ], {
            duration: 1000,
            easing: 'ease-out'
        });
        
        setTimeout(() => aura.remove(), 1000);
    }

    setupPostInteractions() {
        // Likes en posts del feed
        document.addEventListener('click', (e) => {
            if (e.target.closest('.post-action.like')) {
                this.handleLike(e.target.closest('.post-action'));
            }
            
            if (e.target.closest('.post-action.bookmark')) {
                this.handleBookmark(e.target.closest('.post-action'));
            }
        });
    }

    handleLike(button) {
        const icon = button.querySelector('i');
        const count = button.querySelector('.count');
        
        if (button.classList.contains('active')) {
            button.classList.remove('active');
            icon.className = 'far fa-heart';
            if (count) count.textContent = parseInt(count.textContent) - 1;
        } else {
            button.classList.add('active');
            icon.className = 'fas fa-heart';
            if (count) count.textContent = parseInt(count.textContent) + 1;
            
            // Efecto de confeti para muchos likes
            if (parseInt(count.textContent) >= 10) {
                this.createConfetti();
            }
        }
    }

    handleBookmark(button) {
        const icon = button.querySelector('i');
        
        if (button.classList.contains('active')) {
            button.classList.remove('active');
            icon.className = 'far fa-bookmark';
        } else {
            button.classList.add('active');
            icon.className = 'fas fa-bookmark';
        }
    }

    setupConfetti() {
        // Confetti para celebraciones
        window.createConfetti = this.createConfetti.bind(this);
    }

    createConfetti() {
        const container = document.createElement('div');
        container.className = 'confetti-container';
        
        for (let i = 0; i < 50; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.cssText = `
                left: ${Math.random() * 100}%;
                background: ${this.getRandomColor()};
                animation-delay: ${Math.random() * 2}s;
            `;
            container.appendChild(confetti);
        }
        
        document.body.appendChild(container);
        
        setTimeout(() => {
            container.remove();
        }, 3000);
    }

    getRandomColor() {
        const colors = [
            '#7b4eff', '#ff6b6b', '#ffd93d', '#6bcfff', 
            '#b77cff', '#ff8fb1', '#a8e6cf', '#ff9f43'
        ];
        return colors[Math.floor(Math.random() * colors.length)];
    }

    // Publicar nuevo post
    publishPost(content, emotion) {
        if (!content.trim()) {
            this.showNotification('Escribe algo antes de publicar', 'error');
            return;
        }

        if (!emotion) {
            this.showNotification('Selecciona una emoción', 'warning');
            return;
        }

        // Aquí iría la lógica para enviar el post al servidor
        this.showNotification('¡Post publicado con éxito!', 'success');
        this.createConfetti();
        
        // Limpiar formulario
        const textarea = document.querySelector('.create-input');
        if (textarea) textarea.value = '';
        
        const emotionBtns = document.querySelectorAll('.emotion-btn');
        emotionBtns.forEach(btn => btn.classList.remove('active'));
        
        this.currentEmotion = null;
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show`;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Posicionar notification
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 10000;
            min-width: 300px;
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
}

// Inicializar AuraPost
document.addEventListener('DOMContentLoaded', () => {
    window.auraApp = new AuraPost();
    
    // Configurar el botón de publicar
    const publishBtn = document.querySelector('.publish-btn');
    if (publishBtn) {
        publishBtn.addEventListener('click', () => {
            const textarea = document.querySelector('.create-input');
            const activeEmotion = document.querySelector('.emotion-btn.active');
            
            window.auraApp.publishPost(
                textarea ? textarea.value : '',
                activeEmotion ? activeEmotion.dataset.emotion : null
            );
        });
    }
});

// Funciones globales para HTML
function toggleLike(button) {
    if (window.auraApp) {
        window.auraApp.handleLike(button);
    }
}

function toggleBookmark(button) {
    if (window.auraApp) {
        window.auraApp.handleBookmark(button);
    }
}

  // Recupera el estado guardado
  if (localStorage.getItem('highContrast') === 'true') {
    document.body.classList.add('high-contrast');
  }

  // Escucha clicks globales para alternar contraste
  document.addEventListener('click', (e) => {
    const toggle = e.target.closest('#contrastToggle, .toggle-contrast-btn');
    if (!toggle) return;

    document.body.classList.toggle('high-contrast');
    const enabled = document.body.classList.contains('high-contrast');
    localStorage.setItem('highContrast', enabled);
  });

