function switchTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.classList.remove('active');
    });

    document.querySelectorAll('.tab-btn').forEach(button => {
        button.classList.remove('active');
    });

    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}
function setReviewHiddenValues(select){
    if(!select.value) return;

    const parts = select.value.split('|');

    document.getElementById('reviewOrderId').value = parts[0];
    document.getElementById('reviewProductId').value = parts[1];
}