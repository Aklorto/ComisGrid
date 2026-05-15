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