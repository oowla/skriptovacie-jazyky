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