
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content rounded-4 shadow-lg">
        <div class="modal-header border-bottom-0">
          <h5 class="modal-title fw-bold" id="modal-title"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0" id="modal-body">
        </div>
        <div class="modal-footer border-top-0 d-block" id="modal-footer">
        </div>
      </div>
    </div>
</div>

<div class="container-fluid max-w-7xl">
    <div id="summary-cards" class="row g-4 mb-5">
    </div>
</div>

<script>
    // ⚠️ 1. API Configuration: Update this URL to your actual Laravel endpoint
    const API_URL = "{{ route('notifications.groups') }}"; 

    // 2. Global Data Variable (changed to 'let' and initialized empty)
    let notificationsData = []; 
    let totalUnreadCount = 0;
    let activeGroupIndex = -1; 
    const notificationModalElement = document.getElementById('notificationModal');
    const notificationModal = notificationModalElement ? new bootstrap.Modal(notificationModalElement) : null;

    // --- UTILITY: Data Fetching ---

    /**
     * Fetches grouped notification data from the Laravel API.
     * @returns {Promise<Array>} A promise that resolves with the notification data.
     */
    async function fetchNotificationsData() {
        try {
            const response = await fetch(API_URL);

            if (!response.ok) {
                // If the response is not 2xx, throw an error.
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            // The data structure should match the format produced by getGroupedNotifications
            const data = await response.json(); 
            return data;

        } catch (error) {
            console.error('Error fetching notification data:', error);
            // Return an empty array on failure
            return []; 
        }
    }

    // --- MAIN INITIALIZER ---

    /**
     * Replaces the old hardcoded data load. Fetches data, updates state, then renders.
     */
    async function initializeAndRender() {
        // 3. Fetch data from API and update the global variable
        notificationsData = await fetchNotificationsData(); 
        
        // 4. Proceed with rendering based on the fetched data
        renderNotifications();
    }

    // --- RENDERING & INTERACTION (Functions below are updated to use the 'let' notificationsData) ---

    function renderNotifications() {
        renderSummaryCards();
        updateTotalCountBadge();
    }

    function renderSummaryCards() {
        const summaryPanel = document.getElementById('summary-cards');
        if (!summaryPanel) return;
        summaryPanel.innerHTML = '';
        
        // Custom CSS for colored borders based on Bootstrap colors (FIXED)
        if (!document.getElementById('custom-color-styles')) {
            const style = document.createElement('style');
            style.id = 'custom-color-styles';
            
            style.innerHTML = `
                /* ========================================================= */
                /* 1. GENERAL RULE: Set Border Style and Width for ALL classes */
                /* ========================================================= */
                .summary-card.border-start-primary,
                .summary-card.border-start-success,
                .summary-card.border-start-danger,
                .summary-card.border-start-warning,
                .summary-card.border-start-secondary {
                    /* Overrides border-0 and sets up the left border */
                    border: 1px solid rgba(0,0,0,.125) !important;
                    border-left-width: 5px !important; 
                    border-left-style: solid !important;
                    border-left: 5px solid !important; /* Ensure left border takes precedence */
                }

                /* ========================================================= */
                /* 2. SPECIFIC RULES: Set the Color for each class */
                /* ========================================================= */
                .border-start-primary { border-left-color: #0d6efd !important; } 
                .border-start-success { border-left-color: #198754 !important; }
                .border-start-danger { border-left-color: #dc3545 !important; }
                .border-start-warning { border-left-color: #ffc107 !important; }
                .border-start-secondary { border-left-color: #6c757d !important; }
                
                .unread-indicator {
                    content: '';
                    position: absolute;
                    top: 50%;
                    left: 0.5rem; /* Adjust positioning */
                    width: 8px;
                    height: 8px;
                    border-radius: 50%;
                    transform: translateY(-50%);
                }
            `;
            document.head.appendChild(style);
        }

        notificationsData.forEach((groupData, index) => {
            const unreadCount = groupData.items.filter(item => !item.isRead).length;
            
            const col = document.createElement('div');
            col.className = 'col-12 col-sm-6 col-lg-2';
            
            const card = document.createElement('div');
            
            // Determine card classes based on unread status
            const borderColor = unreadCount > 0 ? groupData.colorClass : 'border-start-secondary';
            const cardClasses = `card rounded-3 shadow-sm summary-card border-0 ${borderColor}`;
            
            card.className = cardClasses;
            card.onclick = () => openGroupModal(index); 

            // NOTE: Removed inline style from innerHTML, as it is now in the CSS block
            card.innerHTML = `
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="text-sm text-uppercase fw-semibold ${unreadCount > 0 ? 'text-dark' : 'text-muted'}">${groupData.group}</span>
                        <svg class="text-muted opacity-50" width="24" height="24" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                    </div>
                    <div class="mt-2 d-flex align-items-baseline">
                        <span class="display-4 fw-bolder ${unreadCount > 0 ? 'text-dark' : 'text-muted'}">${unreadCount}</span>
                        <span class="ms-2 text-muted fw-medium">New</span>
                    </div>
                </div>
            `;
            col.appendChild(card);
            summaryPanel.appendChild(col);
        });
    }

    // The rest of the functions (openGroupModal, markGroupAsReadInModal, viewNotification, updateTotalCountBadge) 
    // remain the same as they correctly use the global 'notificationsData' array.


    function openGroupModal(groupIndex) {
        if (!notificationModal) return;
        // ... (rest of openGroupModal logic)
        activeGroupIndex = groupIndex;
        const groupData = notificationsData[groupIndex];
        const unreadItems = groupData.items.filter(item => !item.isRead);
        const totalUnread = unreadItems.length;

        // 1. Populate Header
        const modalTitleElement = document.getElementById('modal-title');
        if (modalTitleElement) modalTitleElement.textContent = `${groupData.group} (${totalUnread} New)`;

        // 2. Populate Body (List)
        const listContainer = document.getElementById('modal-body');
        if (listContainer) {
            listContainer.innerHTML = '';
            
            const list = document.createElement('ul');
            list.className = 'list-group list-group-flush';

            groupData.items.forEach(item => {
                const listItem = document.createElement('li');
                const isUnread = !item.isRead;
                const itemClasses = isUnread 
                    ? 'list-group-item list-group-item-action unread position-relative' 
                    : 'list-group-item list-group-item-action text-muted';

                listItem.className = itemClasses;
                listItem.onclick = () => viewNotification(groupIndex, item.id);

                let contentHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        ${isUnread ? '<span class="unread-indicator bg-primary"></span>' : ''}
                        <span class="text-truncate">${item.title}</span>
                        <small class="text-muted ms-3">${item.time}</small>
                    </div>
                `;
                listItem.innerHTML = contentHTML;
                list.appendChild(listItem);
            });
            listContainer.appendChild(list);
        }

        // 3. Populate Footer (Action Button)
        const footerContainer = document.getElementById('modal-footer');
        if (footerContainer) {
            footerContainer.innerHTML = '';

            if (totalUnread > 0) {
                const markAllButton = document.createElement('button');
                markAllButton.type = 'button';
                markAllButton.className = `btn btn-primary w-100 fw-bold`;
                markAllButton.textContent = `Mark All ${totalUnread} Items as Read`;
                markAllButton.onclick = () => markGroupAsReadInModal(groupIndex); 
                footerContainer.appendChild(markAllButton);
            } else {
                footerContainer.innerHTML = '<p class="text-center text-muted mb-0">All caught up!</p>';
            }
        }

        // 4. Show Modal
        notificationModal.show();
    }

    function markGroupAsReadInModal(groupIndex) {
        // NOTE: In a real app, you'd send an API call here to mark them as read on the server.
        notificationsData[groupIndex].items.forEach(item => {
            item.isRead = true;
        });
        notificationModal.hide();
        renderNotifications(); 
    }
    
    function viewNotification(groupIndex, itemId) {
        const item = notificationsData[groupIndex].items.find(i => i.id === itemId);
        if (item && !item.isRead) {
            // NOTE: In a real app, you'd send an API call here to mark it as read on the server.
            item.isRead = true;
            
            if (groupIndex === activeGroupIndex) {
                openGroupModal(groupIndex); 
            }
            renderNotifications();
        }
    }

    function updateTotalCountBadge() {
        totalUnreadCount = notificationsData.reduce((sum, group) => {
            return sum + group.items.filter(item => !item.isRead).length;
        }, 0);

        const badge = document.getElementById('total-count-badge');
        const countSpan = document.getElementById('total-unread-count');
        
        if (countSpan) countSpan.textContent = totalUnreadCount;
        
        if (badge) {
            if (totalUnreadCount > 0) {
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }
    }


    // 5. Initial render when the page loads now calls the async initializer
    document.addEventListener('DOMContentLoaded', initializeAndRender);

</script>