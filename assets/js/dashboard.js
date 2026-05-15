const searchInput = document.getElementById("searchInput");
const artCards = document.querySelectorAll(".art-card");
const filterButtons = document.querySelectorAll(".filter-btn");

searchInput.addEventListener("input", function () {
    const searchValue = this.value.toLowerCase();

    artCards.forEach(card => {
        const title = card.getAttribute("data-title").toLowerCase();

        if (title.includes(searchValue)) {
            card.style.display = "inline-block";
        } else {
            card.style.display = "none";
        }
    });
});

function filterArt(category) {
    filterButtons.forEach(btn => btn.classList.remove("active"));
    event.target.classList.add("active");

    artCards.forEach(card => {
        const cardCategory = card.getAttribute("data-category");

        if (category === "all" || cardCategory === category) {
            card.style.display = "inline-block";
        } else {
            card.style.display = "none";
        }
    });
}

function openBuyModal(title, price, artist) {
const platformFee = price * 0.05;
const artistEarn = price - platformFee;

    document.getElementById("modalArtwork").textContent = title;
    document.getElementById("modalArtist").textContent = artist;
    document.getElementById("modalPrice").textContent = price;
    document.getElementById("modalFee").textContent = platformFee.toFixed(2);
    document.getElementById("modalArtistEarn").textContent = artistEarn.toFixed(2);

    const buyModal = new bootstrap.Modal(document.getElementById("buyModal"));
    buyModal.show();
}
function openProductPreview(title, artist, image, description, price) {
    document.getElementById("previewTitle").textContent = title;
    document.getElementById("previewArtist").textContent = artist;
    document.getElementById("previewImage").src = image;
    document.getElementById("previewDescription").textContent = description;
    document.getElementById("previewPrice").textContent = Number(price).toFixed(2);

    const modal = new bootstrap.Modal(document.getElementById("productPreviewModal"));
    modal.show();
}

function openPaymentModal(productId, title, artist, price) {
    const platformFee = Number(price) * 0.05;
    const artistEarn = Number(price) - platformFee;
    const afterBalance = currentUserBalance - Number(price);

    document.getElementById("paymentProductId").value = productId;

    document.getElementById("paySubtotal").textContent = Number(price).toFixed(2);
    document.getElementById("payFee").textContent = platformFee.toFixed(2);
    document.getElementById("payArtistEarn").textContent = artistEarn.toFixed(2);
    document.getElementById("payTotal").textContent = Number(price).toFixed(2);

    document.getElementById("walletBalance").textContent = currentUserBalance.toFixed(2);
    document.getElementById("afterBalance").textContent = afterBalance.toFixed(2);
    document.getElementById("payButtonAmount").textContent = Number(price).toFixed(2);
    const payBtn = document.getElementById("walletPayBtn");
const paymentError = document.getElementById("paymentError");

if (afterBalance < 0) {
    payBtn.disabled = true;
    paymentError.textContent = "Insufficient balance. Please top up your ComisGrid Wallet first.";
} else {
    payBtn.disabled = false;
    paymentError.textContent = "";
}

    const modal = new bootstrap.Modal(document.getElementById("paymentModal"));
    modal.show();
}
function closeSuccessModal(){
    document.getElementById("successOverlay").remove();

    const cleanUrl = window.location.pathname;
    window.history.replaceState({}, document.title, cleanUrl);
}