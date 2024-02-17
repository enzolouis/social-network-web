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

function updateUserList(search) {
    let foundUsers = document.getElementById("found-users");
    if (updateUserListXHR) {
        updateUserListXHR.abort();
    }
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
                while (foundUsers.childElementCount > 1) {
                    foundUsers.removeChild(foundUsers.lastChild);
                }
                if (users) {
                    if (!areVisibleFoundUsers) {
                        foundUsers.style.display = "flex";
                        areVisibleFoundUsers = true;
                    }
                    if (isVisibleNoUserFound) {
                        noUserFound.style.display = "none";
                        isVisibleNoUserFound = false;
                    }
                    foundUsers.innerHTML += users;
                } else {
                    if (!areVisibleFoundUsers) {
                        foundUsers.style.display = "flex";
                        areVisibleFoundUsers = true;
                    }
                    if (!isVisibleNoUserFound) {
                        noUserFound.style.display = "flex";
                        isVisibleNoUserFound = true;
                    }
                }
            }
        })
    } else {
        hideFoundUsers();
    }
}