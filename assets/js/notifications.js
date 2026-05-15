document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('notifToggle');
    const dropdown = document.getElementById('notifDropdown');

    if (!toggle || !dropdown) return;

    let opened = false;

    function renderItems(items) {
        if (!items || !items.length) {
            dropdown.innerHTML = '<div class="notif-empty">No notifications.</div>';
            return;
        }

        dropdown.innerHTML = items.map(n => {
            const unreadClass = n.is_read ? '' : 'unread';
            const avatar = n.actor && n.actor.profile_image ? n.actor.profile_image : '../assets/images/default-profile.png';
            const time = new Date(n.created_at).toLocaleString();
            return `
                <a href="${n.link || '#'}" class="notif-item ${unreadClass}" data-id="${n.id}">
                    <img src="${avatar}" class="notif-item-avatar">
                    <div class="notif-item-body">
                        <div class="notif-item-msg">${escapeHtml(n.message)}</div>
                        <div class="notif-item-time">${time}</div>
                    </div>
                </a>
            `;
        }).join('');
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    async function fetchNotifications() {
        try {
            const res = await fetch('notifications_api.php', { credentials: 'same-origin' });
            if (!res.ok) throw new Error('Failed to fetch');
            const data = await res.json();
            renderItems(data.items || []);
        } catch (e) {
            dropdown.innerHTML = '<div class="notif-empty">Unable to load notifications.</div>';
            console.error(e);
        }
    }

    function openDropdown() {
        dropdown.hidden = false;
        dropdown.classList.add('open');
        opened = true;
        fetchNotifications();
    }

    function closeDropdown() {
        dropdown.hidden = true;
        dropdown.classList.remove('open');
        opened = false;
    }

    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        if (opened) closeDropdown(); else openDropdown();
    });

    // close when clicking outside
    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target) && e.target !== toggle) {
            closeDropdown();
        }
    });

    // keyboard escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDropdown();
    });

});

// minimal CSS insertion fallback if CSS not loaded
(function injectStyles(){
    const css = `
    .notif-dropdown{position:absolute;top:48px;right:0;width:340px;max-height:420px;overflow:auto;background:white;border-radius:12px;box-shadow:0 12px 40px rgba(0,0,0,0.12);padding:8px;z-index:9999}
    .notif-item{display:flex;gap:10px;padding:10px;border-radius:10px;text-decoration:none;color:inherit}
    .notif-item.unread{background:#f0f8ff}
    .notif-item-avatar{width:44px;height:44px;border-radius:8px;object-fit:cover}
    .notif-item-body{flex:1}
    .notif-item-msg{font-size:13px;color:#102a43}
    .notif-item-time{font-size:12px;color:#64748b;margin-top:6px}
    .notif-empty{padding:18px;color:#64748b;text-align:center}
    `;
    const s = document.createElement('style'); s.appendChild(document.createTextNode(css)); document.head.appendChild(s);
})();
