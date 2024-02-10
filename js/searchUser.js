function updateUserList(search) {
    console.log(search);
    if (search != null || search != "") {
        let foundUsers = null;
        $.ajax({
            type: 'POST',
            url: '../functions/userSearch.php',
            data: {
                search: search,
            },
            dataType: 'json',
            success: function(users){
                console.log(users);
            }
        })
    }
}

/*
let searchInput = document.getElementById("user-search-input");
searchInput.addEventListener("input", function() { updateUserList(searchInput.value) });
*/