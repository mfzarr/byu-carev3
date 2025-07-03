@props(['title'])
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="border-b w-full h-16 shadow-sm fixed z-30 bg-white">
    <div class="flex h-full items-center justify-between px-6">
        <!-- Left side: Title -->
        <div class="flex-1 flex items-center">
            <h1 class="text-2xl font-bold text-yellow-400">{{ $title }}</h1>
        </div>

        <!-- Right side: Navigation buttons, notifications, and user dropdown -->
        <div class="flex items-center space-x-4">
            <!-- Conditional Navigation buttons (only visible when user is logged in) -->
            @if (Auth::check())
                <a href="{{ route('home.cart') }}"
                    class="text-sm px-6 py-2 border bg-gray-700 text-white rounded-md hover:bg-gray-800">My Cart</a>
                <a href="{{ route('home.history-reservation') }}"
                    class="text-sm px-6 py-2 border bg-gray-700 text-white rounded-md hover:bg-gray-800">Riwayat
                    Reservasi</a>
                <a href="{{ route('home.history') }}"
                    class="text-sm px-6 py-2 border bg-gray-700 text-white rounded-md hover:bg-gray-800">Riwayat
                    Pembelian</a>

                <!-- Notification Bell -->
                <div class="relative">
                    <button
                        class="relative p-2 text-gray-600 hover:text-gray-800 focus:outline-none"
                        id="notification-btn">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <!-- Notification Badge -->
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden" id="notification-badge">0</span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div class="absolute right-0 mt-2 w-80 bg-white shadow-lg rounded-md border border-gray-200 hidden z-50" id="notification-dropdown">
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-800">Notifikasi</h3>
                                <button class="text-sm text-blue-600 hover:text-blue-800" id="mark-all-read">
                                    Tandai Semua Dibaca
                                </button>
                            </div>
                        </div>
                        <div class="max-h-96 overflow-y-auto" id="notification-list">
                            <!-- Notifications will be loaded here -->
                        </div>
                        <div class="p-2 border-t border-gray-200 text-center">
                            <span class="text-sm text-gray-500">Tidak ada notifikasi</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- User dropdown (only visible when user is logged in) -->
            @if (Auth::check())
                <div class="relative">
                    <button
                        class="flex items-center space-x-2 px-4 py-1 border bg-gray-700 text-white rounded-md hover:bg-gray-800 focus:outline-none"
                        id="user-dropdown-btn">
                        <img src="https://www.w3schools.com/howto/img_avatar.png" alt="User Avatar"
                            class="h-8 w-8 rounded-full">
                        <span class="text-sm font-semibold">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <!-- Dropdown content (hidden by default) -->
                    <div class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-md border border-gray-200 hidden"
                        id="user-dropdown">
                        <div class="p-2">
                            <p class="px-4 py-2 text-sm text-gray-700">Welcome, {{ Auth::user()->name }}!</p>
                            @if (Auth::user()->role === 'admin')
                                <a href="{{ route('dashboard') }}"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">
                                    Dashboard
                                </a>
                            @endif
                            <form action="{{ route('logout') }}" method="post" class="mt-2">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <!-- Login/Register Buttons (for non-authenticated users) -->
                <a href="{{ route('login') }}"
                    class="text-sm px-7 py-2 border bg-gray-300 text-center rounded-md hover:bg-gray-400">Login</a>
                <a href="{{ route('register') }}"
                    class="text-sm px-5 py-2 border bg-gray-700 text-center text-white rounded-md ms-2 hover:bg-gray-800">Register</a>
            @endif
        </div>
    </div>
</div>

<script>
    // Toggle user dropdown on button click
    const dropdownBtn = document.getElementById('user-dropdown-btn');
    const dropdownMenu = document.getElementById('user-dropdown');

    if (dropdownBtn) {
        dropdownBtn.addEventListener('click', () => {
            dropdownMenu.classList.toggle('hidden');
        });
    }

    // Close the dropdown if clicked outside of it
    window.addEventListener('click', (event) => {
        if (dropdownBtn && !dropdownBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.add('hidden');
        }
    });

    // Notification functionality
    const notificationBtn = document.getElementById('notification-btn');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const notificationBadge = document.getElementById('notification-badge');
    const notificationList = document.getElementById('notification-list');
    const markAllReadBtn = document.getElementById('mark-all-read');

    if (notificationBtn) {
        // Toggle notification dropdown
        notificationBtn.addEventListener('click', () => {
            notificationDropdown.classList.toggle('hidden');
            if (!notificationDropdown.classList.contains('hidden')) {
                loadNotifications();
            }
        });

        // Close notification dropdown when clicking outside
        window.addEventListener('click', (event) => {
            if (!notificationBtn.contains(event.target) && !notificationDropdown.contains(event.target)) {
                notificationDropdown.classList.add('hidden');
            }
        });

        // Mark all as read
        markAllReadBtn.addEventListener('click', () => {
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                    updateNotificationBadge(0);
                }
            });
        });

        // Load notifications
        function loadNotifications() {
            fetch('/notifications')
                .then(response => response.json())
                .then(data => {
                    displayNotifications(data.notifications);
                    updateNotificationBadge(data.unread_count);
                });
        }

        // Display notifications
        function displayNotifications(notifications) {
            if (notifications.length === 0) {
                notificationList.innerHTML = '<div class="p-4 text-center text-gray-500">Tidak ada notifikasi</div>';
                return;
            }

            let html = '';
            notifications.forEach(notification => {
                const isRead = notification.is_read;
                const bgClass = isRead ? 'bg-gray-50' : 'bg-blue-50';
                const textClass = isRead ? 'text-gray-600' : 'text-gray-800';
                
                html += `
                    <div class="p-4 border-b border-gray-100 ${bgClass} notification-item" data-id="${notification.id}">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="font-semibold ${textClass}">${notification.judul}</h4>
                                <p class="text-sm text-gray-600 mt-1">${notification.pesan}</p>
                                <span class="text-xs text-gray-400 mt-2 block">${formatDate(notification.created_at)}</span>
                            </div>
                            ${!isRead ? '<div class="w-2 h-2 bg-blue-500 rounded-full ml-2 mt-1"></div>' : ''}
                        </div>
                    </div>
                `;
            });
            
            notificationList.innerHTML = html;

            // Add click event to mark individual notifications as read
            document.querySelectorAll('.notification-item').forEach(item => {
                item.addEventListener('click', () => {
                    const notificationId = item.dataset.id;
                    markAsRead(notificationId);
                });
            });
        }

        // Update notification badge
        function updateNotificationBadge(count) {
            if (count > 0) {
                notificationBadge.textContent = count;
                notificationBadge.classList.remove('hidden');
            } else {
                notificationBadge.classList.add('hidden');
            }
        }

        // Mark individual notification as read
        function markAsRead(notificationId) {
            fetch('/notifications/mark-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id: notificationId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                }
            });
        }

        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Load notifications on page load
        loadNotifications();
        
        // Refresh notifications every 30 seconds
        setInterval(loadNotifications, 30000);
    }
</script>