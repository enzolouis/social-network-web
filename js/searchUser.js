var foundUsers = document.getElementById("found-users");
var areVisibleFoundUsers = false;
var isVisibleNoUserFound = true;
var updateUserListXHR;

var typingTimer;
var doneTypingCountdown = 300;
var input = document.getElementById("user-search-input");

var hoveredOnUser = false;

foundUsers.addEventListener('mouseenter', () => {
    hoveredOnUser = true;
});

foundUsers.addEventListener('mouseleave', () => {
    hoveredOnUser = false;
});

function hideFoundUsers() {
    foundUsers.style.display = "none";
    areVisibleFoundUsers = false; 
    hoveredOnUser = false;
}

input.addEventListener('keyup', () => {
    clearTimeout(typingTimer);
    if (input.value) {
        typingTimer = setTimeout(() => { updateUserList(input.value) }, doneTypingCountdown);
    } else {
        foundUsers.style.display = "none";
        areVisibleFoundUsers = false;
    }
});

input.addEventListener('blur', () => {
    if (!hoveredOnUser) {
        foundUsers.style.display = "none";
        areVisibleFoundUsers = false;
    } 
});

function updateUserList(search) {
    if (updateUserListXHR) {
        updateUserListXHR.abort();
    }
    if (search) {
        updateUserListXHR = $.ajax({
            type: 'POST',
            url: '../functions/userSearch.php',
            data: {
                search: search,
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
        foundUsers.style.display = "none";
        areVisibleFoundUsers = false;
    }
}
/*
let searchInput = document.getElementById("user-search-input");
searchInput.addEventListener("input", function() { updateUserList(searchInput.value) });
*/