// ============ مزايا مول - سلة التسوق ============
const CART_KEY = 'mazaya_cart';

function getCart(){
    return JSON.parse(localStorage.getItem(CART_KEY)) || [];
}
function saveCart(cart){
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    updateCartCount();
}
function updateCartCount(){
    const cart = getCart();
    const count = cart.reduce((sum, item) => sum + item.qty, 0);
    document.querySelectorAll('#cartCount').forEach(el => el.textContent = count);
}

function addToCart(id, name, price, image){
    let cart = getCart();
    const existing = cart.find(item => item.id === id);
    if(existing){
        existing.qty += 1;
    } else {
        cart.push({id, name, price, image, qty: 1});
    }
    saveCart(cart);
    showToast(name + ' أضيف للسلة');
}

function removeFromCart(id){
    let cart = getCart().filter(item => item.id !== id);
    saveCart(cart);
    renderCart();
}

function changeQty(id, delta){
    let cart = getCart();
    const item = cart.find(i => i.id === id);
    if(!item) return;
    item.qty += delta;
    if(item.qty < 1) item.qty = 1;
    saveCart(cart);
    renderCart();
}

function showToast(msg){
    let toast = document.querySelector('.toast');
    if(!toast){
        toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = '<i class="fa-solid fa-circle-check"></i><span></span>';
        document.body.appendChild(toast);
    }
    toast.querySelector('span').textContent = msg;
    toast.classList.add('show');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => toast.classList.remove('show'), 2200);
}

function renderCart(){
    const cart = getCart();
    const emptyEl = document.getElementById('cartEmpty');
    const itemsEl = document.getElementById('cartItems');
    const summaryEl = document.getElementById('cartSummary');
    if(!itemsEl) return;

    if(cart.length === 0){
        if(emptyEl) emptyEl.style.display = 'block';
        if(summaryEl) summaryEl.style.display = 'none';
        itemsEl.innerHTML = '';
        return;
    }
    if(emptyEl) emptyEl.style.display = 'none';
    if(summaryEl) summaryEl.style.display = 'flex';

    let total = 0;
    itemsEl.innerHTML = cart.map(item => {
        total += item.price * item.qty;
        const imagePath = item.image.includes('uploads/') ? item.image : ('uploads/' + item.image);
        return `
        <div class="cart-item">
            <img src="${imagePath}" alt="${item.name}" onerror="this.src='assets/images/placeholder.png'">
            <div>
                <h4>${item.name}</h4>
                <span class="item-price">${item.price.toLocaleString()} ج.م</span>
            </div>
            <div class="qty-control">
                <button onclick="changeQty(${item.id}, -1)">-</button>
                <span>${item.qty}</span>
                <button onclick="changeQty(${item.id}, 1)">+</button>
            </div>
            <strong>${(item.price * item.qty).toLocaleString()} ج.م</strong>
            <button class="remove-btn" onclick="removeFromCart(${item.id})"><i class="fa-solid fa-trash"></i></button>
        </div>`;
    }).join('');

    const totalEl = document.getElementById('totalPrice');
    if(totalEl) totalEl.textContent = total.toLocaleString() + ' ج.م';
}

function renderCheckoutSummary(){
    const cart = getCart();
    const itemsEl = document.getElementById('checkoutItems');
    const totalEl = document.getElementById('checkoutTotal');
    if(!itemsEl) return;

    let total = 0;
    itemsEl.innerHTML = cart.map(item => {
        total += item.price * item.qty;
        return `<div class="checkout-item-row"><span>${item.name} × ${item.qty}</span><span>${(item.price*item.qty).toLocaleString()} ج.م</span></div>`;
    }).join('');
    totalEl.textContent = total.toLocaleString() + ' ج.م';
}

document.addEventListener('DOMContentLoaded', updateCartCount);
