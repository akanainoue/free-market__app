require('./bootstrap');

window.Echo.private(`user.${userId}`)
    .listen('UnreadCountUpdated', (e) => {
        document.getElementById('unread-count').innerText = e.unreadCount;
    });
