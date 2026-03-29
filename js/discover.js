class FloatingPost {
    constructor(element) {
        this.element = element;
        this.width = element.offsetWidth;
        this.height = element.offsetHeight;
        
        // Posición inicial aleatoria si no tiene style
        this.x = element.offsetLeft;
        this.y = element.offsetTop;
        
        // Velocidad tipo DVD
        const speed = 0.5 + Math.random() * 0.5;
        const angle = Math.random() * Math.PI * 2;
        this.vx = Math.cos(angle) * speed;
        this.vy = Math.sin(angle) * speed;
        
        this.isDragging = false;
    }

    update() {
        if (this.isDragging) return;

        this.x += this.vx;
        this.y += this.vy;

        // Rebotes en bordes
        if (this.x <= 0 || this.x >= window.innerWidth - this.width) {
            this.vx *= -1;
            this.x = Math.max(0, Math.min(this.x, window.innerWidth - this.width));
        }
        if (this.y <= 80 || this.y >= window.innerHeight - this.height) {
            this.vy *= -1;
            this.y = Math.max(80, Math.min(this.y, window.innerHeight - this.height));
        }

        this.element.style.left = `${this.x}px`;
        this.element.style.top = `${this.y}px`;
    }

    startDrag() { this.isDragging = true; }
    endDrag() { this.isDragging = false; }
}

const postObjects = [];
document.querySelectorAll('.floating-post').forEach(el => {
    postObjects.push(new FloatingPost(el));
});

function animate() {
    postObjects.forEach(p => p.update());
    requestAnimationFrame(animate);
}
animate();

// Lógica de Abrir Perfil al hacer Click
document.addEventListener('click', e => {
    const post = e.target.closest('.floating-post');
    const likeBtn = e.target.closest('.like-btn');

    if (likeBtn) {
        ejecutarLike(likeBtn);
        return;
    }

    if (post) {
        const user = post.dataset.user;
        const handle = post.dataset.handle;
        const foto = post.dataset.foto;
        
        document.getElementById('modalUser').textContent = user;
        document.getElementById('modalHandle').textContent = handle;
        document.getElementById('modalImg').src = foto;
        document.getElementById('viewProfileBtn').href = "perfil_usuario.php?alias=" + user;
        
        new bootstrap.Modal(document.getElementById('profileModal')).show();
    }
});

// Función AJAX para dar Like sin recargar
function ejecutarLike(btn) {
    const pid = btn.dataset.id;
    const formData = new FormData();
    formData.append('id_post', pid);
    formData.append('accion', 'like');

    fetch('../negocio/gestionar_interacciones.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            const icon = btn.querySelector('i');
            const count = btn.querySelector('span');
            btn.classList.toggle('liked');
            
            if(btn.classList.contains('liked')) {
                icon.className = 'bi bi-heart-fill';
                count.textContent = parseInt(count.textContent) + 1;
            } else {
                icon.className = 'bi bi-heart';
                count.textContent = parseInt(count.textContent) - 1;
            }
        }
    });
}