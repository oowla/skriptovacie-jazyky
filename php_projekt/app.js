function toggleFavorite(button, itemId) {
    event.preventDefault(); 
    event.stopPropagation(); 

    fetch('toggle_like.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ item_id: itemId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'not_logged_in') {
            window.location.href = 'login.php';
        } else if (data.status === 'liked') {
            button.classList.add('active');
        } else if (data.status === 'unliked') {
            button.classList.remove('active');
        }
    })
    .catch(error => console.error('Error:', error));
}

document.addEventListener('DOMContentLoaded', () => {
    
    const fileInput = document.getElementById('itemPhotos');
    const previewContainer = document.getElementById('previewContainer');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');

    if (fileInput && previewContainer) {
        fileInput.addEventListener('change', function() {
            const files = this.files;
            
            previewContainer.innerHTML = '';

            if (files.length > 0) {
                uploadPlaceholder.style.display = 'none';

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    
                    if (file.type.match('image.*')) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.classList.add('image-preview');
                            previewContainer.appendChild(img);
                        }

                        reader.readAsDataURL(file);
                    }
                }
            } else {
                uploadPlaceholder.style.display = 'flex';
            }
        });
    }

});

let currentImageIndex = 0;
let fullscreenImageIndex = 0;
let zoomLevel = 1;
let panX = 0, panY = 0;
let isDragging = false;
let dragStartX = 0, dragStartY = 0, dragStartPanX = 0, dragStartPanY = 0;

function showImage(index) {
    const images = document.querySelectorAll('.gallery-image');
    if (images.length === 0) return;
    
    images.forEach(img => img.style.display = 'none');
    images[index].style.display = 'block';
    
    const dots = document.querySelectorAll('.gallery-dot');
    dots.forEach((dot, i) => {
        dot.style.background = i === index ? 'white' : 'rgba(255,255,255,0.5)';
    });
}

function nextImage() {
    const total = document.querySelectorAll('.gallery-image').length;
    currentImageIndex = (currentImageIndex + 1) % total;
    showImage(currentImageIndex);
}

function prevImage() {
    const total = document.querySelectorAll('.gallery-image').length;
    currentImageIndex = (currentImageIndex - 1 + total) % total;
    showImage(currentImageIndex);
}

function goToImage(index) {
    currentImageIndex = index;
    showImage(currentImageIndex);
}

function openFullscreen(index) {
    fullscreenImageIndex = index;
    resetZoom();
    document.getElementById('fullscreenModal').style.display = 'flex';
    updateFullscreenImage();
}

function closeFullscreen() {
    document.getElementById('fullscreenModal').style.display = 'none';
    resetZoom();
}

function updateFullscreenImage() {
    if (typeof fullscreenImages === 'undefined' || fullscreenImages.length === 0) return;
    
    const img = document.getElementById('fullscreenImage');
    img.src = fullscreenImages[fullscreenImageIndex];
    
    const counter = document.getElementById('imageCounter');
    if (counter) {
        counter.textContent = `${fullscreenImageIndex + 1} / ${fullscreenImages.length}`;
    }
    resetPan();
}

function fullscreenNextImage() {
    fullscreenImageIndex = (fullscreenImageIndex + 1) % fullscreenImages.length;
    updateFullscreenImage();
}

function fullscreenPrevImage() {
    fullscreenImageIndex = (fullscreenImageIndex - 1 + fullscreenImages.length) % fullscreenImages.length;
    updateFullscreenImage();
}

function zoomIn() {
    zoomLevel = Math.min(zoomLevel + 0.1, 5);
    applyTransform();
    updateZoomDisplay();
}

function zoomOut() {
    zoomLevel = Math.max(zoomLevel - 0.1, 1);
    applyTransform();
    updateZoomDisplay();
}

function resetZoom() {
    zoomLevel = 1;
    resetPan();
    applyTransform();
    updateZoomDisplay();
}

function updateZoomDisplay() {
    const zl = document.getElementById('zoomLevel');
    if(zl) zl.textContent = Math.round(zoomLevel * 100) + '%';
}

function resetPan() {
    panX = 0;
    panY = 0;
}

function applyTransform() {
    const img = document.getElementById('fullscreenImage');
    if(!img) return;
    img.style.transform = `scale(${zoomLevel}) translate(${panX}px, ${panY}px)`;
    img.style.cursor = zoomLevel > 1 ? 'grab' : 'default';
}

function startDrag(e) {
    if (zoomLevel <= 1) return;
    isDragging = true;
    dragStartX = e.clientX;
    dragStartY = e.clientY;
    dragStartPanX = panX;
    dragStartPanY = panY;
    document.getElementById('fullscreenImage').style.cursor = 'grabbing';
    document.addEventListener('mousemove', onDrag);
    document.addEventListener('mouseup', stopDrag);
}

function onDrag(e) {
    if (!isDragging) return;
    panX = dragStartPanX + (e.clientX - dragStartX) / zoomLevel;
    panY = dragStartPanY + (e.clientY - dragStartY) / zoomLevel;
    applyTransform();
}

function stopDrag() {
    isDragging = false;
    document.removeEventListener('mousemove', onDrag);
    document.removeEventListener('mouseup', stopDrag);
    const img = document.getElementById('fullscreenImage');
    if(img) img.style.cursor = 'grab';
}

document.addEventListener('wheel', (e) => {
    const modal = document.getElementById('fullscreenModal');
    if (modal && modal.style.display === 'flex') {
        e.preventDefault();
        if (e.deltaY < 0) zoomIn();
        else zoomOut();
    }
}, { passive: false });

document.addEventListener('keydown', (e) => {
    const modal = document.getElementById('fullscreenModal');
    if (modal && modal.style.display === 'flex') {
        if (e.key === 'ArrowRight') fullscreenNextImage();
        if (e.key === 'ArrowLeft') fullscreenPrevImage();
        if (e.key === 'Escape') closeFullscreen();
        if (e.key === '+' || e.key === '=') zoomIn();
        if (e.key === '-') zoomOut();
        if (e.key === '0') resetZoom();
    } else {
        if (document.querySelectorAll('.gallery-image').length > 1) {
            if (e.key === 'ArrowRight') nextImage();
            if (e.key === 'ArrowLeft') prevImage();
        }
    }
});

document.addEventListener('click', (e) => {
    if (e.target.id === 'fullscreenModal') closeFullscreen();
});