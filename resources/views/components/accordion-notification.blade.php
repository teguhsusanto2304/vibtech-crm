<div class="px-6 pb-2">
        <div id="notification-alerts-container" class="mt-3 mx-auto" >
            {{-- Alerts will be injected here by JavaScript --}}
                    </div>
                    <script>
                    $(document).ready(function () {
                        const notificationAlertsContainer = $("#notification-alerts-container");

                function fetchNotifications() {
                    $.ajax({
                        url: "{{ route('notifications.get-specifically') }}",
                        method: "GET",
                        success: function (data) {
                            notificationAlertsContainer.empty();

                            if (data.length > 0) {
                                $("#notification-count").text(data.length).show();

                                let username = @json(auth()->user()->name);

                                // Group notifications by category
                                let grouped = {
                                    "employee-handbooks": [],
                                    "management-memo": [],
                                    "submit-claims": [],
                                    "job-assignment-form": [],
                                    "general": []
                                };

                                data.forEach(function (notif) {
                                    let link = notif.data.url ? notif.data.url : "#";
                                    let notifUrl = notif.data.url?.toLowerCase() || "";

                                    if (notifUrl.includes('/employee-handbooks')) {
                                        grouped["employee-handbooks"].push(
                                            `${username}, a new employee handbook was uploaded. 
                                            <a href="${link}?notif=${notif.id}" class="alert-link">Read here</a>
                                            <br><small class="text-muted">(${notif.data.time})</small>`
                                        );
                                    } else if (notifUrl.includes('/management-memo')) {
                                        grouped["management-memo"].push(
                                            `${username}, a new management memo was issued. 
                                            <a href="${link}?notif=${notif.id}" class="alert-link">Read here</a>
                                            <br><small class="text-muted">(${notif.data.time})</small>`
                                        );
                                    } else if (notifUrl.includes('/submit-claims')) {
                                        grouped["submit-claims"].push(
                                            `${username}, ${notif.data.message}.
                                            <a href="${link}?notif=${notif.id}" class="alert-link">Read here</a>
                                            <br><small class="text-muted">(${notif.data.time})</small>`
                                        );
                                        
                                    } else if (notifUrl.includes('/job-assignment-form')) {
                                        grouped["job-assignment-form"].push(
                                            `${username}, ${notif.data.message}.
                                            <a href="${link}?notif=${notif.id}" class="alert-link">Read here</a>
                                            <br><small class="text-muted">(${notif.data.time})</small>`
                                        );
                                    } else {
                                        grouped["general"].push(
                                            `${username}, ${notif.data.message}.
                                            <br><small class="text-muted">(${notif.data.time})</small>`
                                        );
                                    }
                                });

                                // Accordion wrapper
                                let accordionHtml = `<div class="accordion" id="notificationsAccordion">`;

                                // Employee Handbooks
                                if (grouped["employee-handbooks"].length > 0) {
                                    accordionHtml += `
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-handbook">
                                                <button class="accordion-button collapsed bg-success text-white" 
                                                    type="button" data-bs-toggle="collapse" 
                                                    data-bs-target="#collapse-handbook" 
                                                    aria-expanded="false" aria-controls="collapse-handbook">
                                                    <i class="fas fa-book me-2"></i> Employee Handbooks
                                                </button>
                                            </h2>
                                            <div id="collapse-handbook" class="accordion-collapse collapse" 
                                                aria-labelledby="heading-handbook" data-bs-parent="#notificationsAccordion">
                                                <div class="accordion-body">
                                                    <ul class="list-group list-group-flush">
                                                        ${grouped["employee-handbooks"].map(item => `<li class="list-group-item">${item}</li>`).join("")}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }

                                // Management Memo
                                if (grouped["management-memo"].length > 0) {
                                    accordionHtml += `
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-memo">
                                                <button class="accordion-button collapsed bg-warning text-dark" 
                                                    type="button" data-bs-toggle="collapse" 
                                                    data-bs-target="#collapse-memo" 
                                                    aria-expanded="false" aria-controls="collapse-memo">
                                                    <i class="fas fa-exclamation-triangle me-2"></i> Management Memo
                                                </button>
                                            </h2>
                                            <div id="collapse-memo" class="accordion-collapse collapse" 
                                                aria-labelledby="heading-memo" data-bs-parent="#notificationsAccordion">
                                                <div class="accordion-body">
                                                    <ul class="list-group list-group-flush">
                                                        ${grouped["management-memo"].map(item => `<li class="list-group-item">${item}</li>`).join("")}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }
                                //submit-claims
                                if (grouped["submit-claims"].length > 0) {
                                    accordionHtml += `
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-general">
                                                <button class="accordion-button collapsed bg-info text-white" 
                                                    type="button" data-bs-toggle="collapse" 
                                                    data-bs-target="#collapse-general" 
                                                    aria-expanded="false" aria-controls="collapse-general">
                                                    <i class="fas fa-bell me-2"></i> Submit Claim
                                                </button>
                                            </h2>
                                            <div id="collapse-general" class="accordion-collapse collapse" 
                                                aria-labelledby="heading-general" data-bs-parent="#notificationsAccordion">
                                                <div class="accordion-body">
                                                    <ul class="list-group list-group-flush">
                                                        ${grouped["submit-claims"].map(item => `<li class="list-group-item">${item}</li>`).join("")}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }
                                //job-assignment-form
                                if (grouped["job-assignment-form"].length > 0) {
                                    accordionHtml += `
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-general">
                                                <button class="accordion-button collapsed bg-info text-white" 
                                                    type="button" data-bs-toggle="collapse" 
                                                    data-bs-target="#collapse-general" 
                                                    aria-expanded="false" aria-controls="collapse-general">
                                                    <i class="fas fa-bell me-2"></i> Job Requisition
                                                </button>
                                            </h2>
                                            <div id="collapse-general" class="accordion-collapse collapse" 
                                                aria-labelledby="heading-general" data-bs-parent="#notificationsAccordion">
                                                <div class="accordion-body">
                                                    <ul class="list-group list-group-flush">
                                                        ${grouped["job-assignment-form"].map(item => `<li class="list-group-item">${item}</li>`).join("")}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }

                                // General
                                if (grouped["general"].length > 0) {
                                    accordionHtml += `
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-general">
                                                <button class="accordion-button collapsed bg-info text-white" 
                                                    type="button" data-bs-toggle="collapse" 
                                                    data-bs-target="#collapse-general" 
                                                    aria-expanded="false" aria-controls="collapse-general">
                                                    <i class="fas fa-bell me-2"></i> General Notifications
                                                </button>
                                            </h2>
                                            <div id="collapse-general" class="accordion-collapse collapse" 
                                                aria-labelledby="heading-general" data-bs-parent="#notificationsAccordion">
                                                <div class="accordion-body">
                                                    <ul class="list-group list-group-flush">
                                                        ${grouped["general"].map(item => `<li class="list-group-item">${item}</li>`).join("")}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }

                                accordionHtml += `</div>`; // close accordion
                                notificationAlertsContainer.append(accordionHtml);

                            } else {
                                $("#notification-count").hide();
                                notificationAlertsContainer.html(`
                                    <div class="alert alert-info" role="alert">
                                        <i class="fas fa-info-circle me-2"></i> No new notifications at this time.
                                    </div>
                                `);
                            }
                        },
                        error: function (xhr) {
                            console.error("Error fetching notifications:", xhr.responseText);
                            notificationAlertsContainer.empty().html(`
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i> Failed to load notifications.
                                </div>
                            `);
                            $("#notification-count").hide();
                        }
                    });
                }


                

                fetchNotifications(); // Fetch notifications on page load

                // You might want to automatically refresh notifications periodically
                // setInterval(fetchNotifications, 60000); // Fetch every 60 seconds

                // Assuming you have a "Mark all as read" button/link with this ID/class
                $(document).on('click', '.dropdown-notifications-all', function () { // Use event delegation if element is dynamic
                    $.ajax({
                        url: "{{ route('notifications.read') }}",
                        method: "POST",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function () {
                            fetchNotifications(); // Re-fetch to update display
                            // Optionally hide the dropdown menu if it's open
                            // $('.dropdown-toggle').dropdown('hide');
                        },
                        error: function(xhr) {
                            console.error("Error marking notifications as read:", xhr.responseText);
                            // Display error in a temporary alert or console
                        }
                    });
                });

                // Optional: Function to mark a single notification as read (e.g., when clicked)
                // You'd need to add a click listener to the alert itself or its "View Details" link
                function markNotificationAsRead(notificationId) {
                    $.ajax({
                        url: `/notifications/${notificationId}/read`, // Example route
                        method: 'POST',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function() {
                            // No need to re-fetch all, just remove the specific alert if desired
                            $(`#notification-alert-${notificationId}`).remove(); // If you give alerts unique IDs
                        },
                        error: function(xhr) {
                            console.error(`Error marking notification ${notificationId} as read:`, xhr.responseText);
                        }
                    });
                }
            });
            </script>
        </div>