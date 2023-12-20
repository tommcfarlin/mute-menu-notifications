/**
 * Registers the menu visibility option.
 */
function registerMenuVisibilityOption() {
    const ajaxurl = TmMuteMenuNotifications.ajaxurl + '?action=tm_mute_menu_notifications&security=' + TmMuteMenuNotifications.nonce;
    const data = new URLSearchParams();
    data.append('action', 'tm_mute_menu_notifications');
    data.append('security', TmMuteMenuNotifications.nonce);

    fetch(ajaxurl, {
        method: 'GET',
        credentials: 'same-origin',
        headers: new Headers({
            'Content-Type': 'application/json'
        })
    })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                console.log(response);
            }
        })
        .then(data => {
            if (data.success) {
                toggleNotificationsCount();
                togglePluginUpdateNotifications();
            }
        })
        .catch(error => {
            console.error('Error during AJAX request:', error);
        });
}

/**
 * Toggles the visibility of notifications count.
 */
function toggleNotificationsCount() {
    // Find all elements on the page that have a class name with a substring of 'update-'.
    const updateElements = document.querySelectorAll('span[class*="update-"]');

    // Iterate through each of the elements with the class 'update-count'.
    for (let i = 0; i < updateElements.length; i++) {
        // Locate the parent element.
        const currentElement = updateElements[i];

        // If the element has a class called 'tm-hide-update-count' then remove it; otherwise, add it.
        (currentElement.classList.contains('tm-hide-update-count')) ?
            currentElement.classList.remove('tm-hide-update-count') :
            currentElement.classList.add('tm-hide-update-count');
    }
}

/**
 * Toggles the visibility of plugin update notifications on the plugins page.
 */
function togglePluginUpdateNotifications() {
    // Check the URL to see if plugins.php is contained within it.
    const currentUrl = window.location.href;
    const isPluginsPage = currentUrl.indexOf('plugins.php');
    if (-1 === isPluginsPage) {
        return;
    }

    // Find all elements with the class name 'plugin-update'.
    const pluginUpdateElements = document.querySelectorAll('tr[class*="plugin-update"]');

    // Iterate through the elements and toggle the visibility of the element..
    for (let i = 0; i < pluginUpdateElements.length; i++) {
        // Locate the parent element.
        const currentElement = pluginUpdateElements[i];

        // If the element has the hidden class then remove it; otherwise, add it.
        (currentElement.classList.contains('hidden')) ?
            currentElement.classList.remove('hidden') :
            currentElement.classList.add('hidden');
    }
}

/**
 * Retrieves the update count notification status.
 * This function makes an AJAX request to get the update count and toggles the notification count and plugin update notifications accordingly.
 */
function getUpdateCountNotificationStatus() {
    // Set up a handler to determine if the update count should be visible or not.
    const ajaxurl = TmMuteMenuNotifications.ajaxurl + '?action=tm_get_menu_notifications&security=' + TmMuteMenuNotifications.nonce;
    const data = new URLSearchParams();
    data.append('action', 'tm_get_menu_notifications');
    data.append('security', TmMuteMenuNotifications.nonce);

    fetch(ajaxurl, {
        method: 'GET',
        credentials: 'same-origin',
        headers: new Headers({
            'Content-Type': 'application/json'
        })
    })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                console.log(response);
            }
        })
        .then(data => {
            if (data) {
                toggleNotificationsCount();
                togglePluginUpdateNotifications();
            }
        })
        .catch(error => {
            console.error('Error during AJAX request:', error);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    getUpdateCountNotificationStatus();

    // Setup a handler for when the user clicks to toggle menu items.
    const notificationsMenu = document.getElementById('wp-admin-bar-tm-admin-bar-mute-menu-notifications').firstChild;
    notificationsMenu.addEventListener('click', function (event) {
        event.preventDefault();
        registerMenuVisibilityOption();
    });
});