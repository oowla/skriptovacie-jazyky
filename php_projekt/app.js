const products = [
    {
        id: 1,
        brand: "Vintage Leather",
        price: "$85.00",
        size: "M / 38",
        image: "images/leather_jacket.jpg"
    },
    {
        id: 2,
        brand: "Dr. Martens",
        price: "$70.00",
        size: "UK 8 / EU 42",
        image: "images/drmartens.jpg"
    },
    {
        id: 3,
        brand: "Lip Service top",
        price: "$35.00",
        size: "S / 34",
        image: "images/lip_service.jpg"
    },
    {
        id: 4,
        brand: "Distressed Denim",
        price: "$35.00",
        size: "W30 L32",
        image: "images/pants.jpg"
    },
    {
        id: 5,
        brand: "Killstar",
        price: "$20.00",
        size: "S / 36",
        image: "images/killstar.jpg"
    },
    {
        id: 6,
        brand: "Short dress",
        price: "$25.00",
        size: "L / 40",
        image: "images/dress.jpg"
    }
];

function renderProducts() {
    const productGrid = document.getElementById('productGrid');
    
    products.forEach(product => {
        const productHTML = `
            <div class="product-card">
                <div class="product-image-wrapper">
                    <img src="${product.image}" alt="${product.brand}">
                    <button class="btn-favorite" onclick="toggleFavorite(this)">
                        <i class="fa-solid fa-heart"></i>
                    </button>
                </div>
                <div class="product-info">
                    <div class="product-brand">${product.brand}</div>
                    <div class="product-details">
                        <span class="product-price">${product.price}</span>
                        <span class="product-size">${product.size}</span>
                    </div>
                </div>
            </div>
        `;
        productGrid.innerHTML += productHTML;
    });
}

function toggleFavorite(button) {
    event.stopPropagation(); 
    
    button.classList.toggle('active');
}

window.onload = renderProducts;

document.addEventListener('DOMContentLoaded', () => {
    
    const dropdownToggle = document.getElementById('categoriesToggle');
    const dropdownMenu = document.getElementById('categoriesMenu');

    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', function(event) {
            event.preventDefault();
            dropdownMenu.classList.toggle('show');
        });

        document.addEventListener('click', function(event) {
            const isClickInside = dropdownToggle.contains(event.target) || dropdownMenu.contains(event.target);
            
            if (!isClickInside) {
                dropdownMenu.classList.remove('show');
            }
        });
    }

});

document.addEventListener('DOMContentLoaded', () => {

    const sendBtn = document.getElementById('sendMessageBtn');
    const msgInput = document.getElementById('messageInput');
    const chatHistory = document.getElementById('chatHistory');

    function sendMessage() {
        const text = msgInput.value.trim();
        if (text === '') return;

        const now = new Date();
        let hours = now.getHours();
        let minutes = now.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; 
        minutes = minutes < 10 ? '0' + minutes : minutes;
        const timeString = hours + ':' + minutes + ' ' + ampm;

        const msgHTML = `
            <div class="message-wrapper sent">
                <div class="message bubble">
                    ${text}
                </div>
                <span class="msg-time">${timeString}</span>
            </div>
        `;

        chatHistory.innerHTML += msgHTML;

        msgInput.value = '';

        chatHistory.scrollTop = chatHistory.scrollHeight;
    }

    if (sendBtn && msgInput && chatHistory) {
        
        sendBtn.addEventListener('click', sendMessage);

        msgInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        chatHistory.scrollTop = chatHistory.scrollHeight;
    }

});

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