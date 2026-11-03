console.log("LIBRARE Store Running...");

// Toggle Buka/Tutup Menu Garis Tiga (Hamburger Menu)
function toggleMenu() {
    const menu = document.getElementById('dropdownMenu');
    menu.classList.toggle('active');
}

// Toggle Buka/Tutup Sub Kategori Shop
function toggleShopSub() {
    const sub = document.getElementById('shopSubCategory');
    sub.classList.toggle('active');
}

// Toggle Buka/Tutup Popup WhatsApp
function toggleWaChat() {
    const chatBox = document.getElementById('waChatBox');
    if (chatBox.style.display === 'flex') {
        chatBox.style.display = 'none';
    } else {
        chatBox.style.display = 'flex';
    }
}

// Tutup Popup Welcome Banner
function closeWelcomeModal() {
    document.getElementById('welcomeModal').style.display = 'none';
}