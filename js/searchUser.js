let areVisibleFoundUsers = false;
let isVisibleNoUserFound = true;
let updateUserListXHR;

let typingTimer;
let doneTypingCountdown = 300;

let hoveredOnUser = false;

addEventListenersSearchInput();
addEventListenersFoundUsers();

function hideFoundUsers() {
    let foundUsers = document.getElementById("found-users");
    foundUsers.style.display = "none";
    areVisibleFoundUsers = false; 
    hoveredOnUser = false;
}

function addEventListenersSearchInput() {
    let searchInput = document.getElementById("user-search-input");
    let foundUsers = document.getElementById("found-users");
    searchInput.addEventListener('keyup', () => {
        clearTimeout(typingTimer);
        if (searchInput.value) {
            typingTimer = setTimeout(() => { updateUserList(searchInput.value) }, doneTypingCountdown);
        } else {
            hideFoundUsers();
        }
    });
    
    searchInput.addEventListener('focus', () => {
        if (searchInput.value) {
            foundUsers.style.display = "flex";
            areVisibleFoundUsers = true;
        }
    });

    searchInput.addEventListener('blur', () => {
        if (!hoveredOnUser) {
            hideFoundUsers();
        } 
    });
}

function addEventListenersFoundUsers() {
    let foundUsers = document.getElementById("found-users");
    foundUsers.addEventListener('mouseenter', () => {
        hoveredOnUser = true;
    });
    
    foundUsers.addEventListener('mouseleave', () => {
        hoveredOnUser = false;
    });
}

/**
 * Shows the found users whenever the user types in the search bar
 * 
 * @param {string} search 
 */
function updateUserList(search) {
    let foundUsers = document.getElementById("found-users");
    // If an AJAX call was already looking for the found users, abort it
    if (updateUserListXHR) {
        updateUserListXHR.abort();
    }
    // If the input isn't empty, retrieve found users
    if (search) {
        updateUserListXHR = $.ajax({
            type: 'GET',
            url: '../functions/chatDisplayCall.php',
            data: {
                section: "foundUsers",
                loggedUser: loggedUser,
                searchedUser: search,
            },
            success: function(users) {
                let noUserFound = document.getElementById("no-user-found");
                // Remove every child except the "No user found" panel
                while (foundUsers.childElementCount > 1) {
                    foundUsers.removeChild(foundUsers.lastChild);
                }
                // If users were found
                if (users) {
                    // Display the "Found users" panel if not already displayed
                    if (!areVisibleFoundUsers) {
                        foundUsers.style.display = "flex";
                        areVisibleFoundUsers = true;
                    }
                    // Hide the "No user found" panel
                    if (isVisibleNoUserFound) {
                        noUserFound.style.display = "none";
                        isVisibleNoUserFound = false;
                    }
                    // Append every found users to the panel
                    foundUsers.innerHTML += users;
                } else {
                    // Display the "Found users" panel if not already displayed
                    if (!areVisibleFoundUsers) {
                        foundUsers.style.display = "flex";
                        areVisibleFoundUsers = true;
                    }
                    // Show the "No user found" panel
                    if (!isVisibleNoUserFound) {
                        noUserFound.style.display = "flex";
                        isVisibleNoUserFound = true;
                    }
                }
            }
        })
    } else {
        // else, hide the found users tab, as the user isn't looking for an user
        hideFoundUsers();
    }
}