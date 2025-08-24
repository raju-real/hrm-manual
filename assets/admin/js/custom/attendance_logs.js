(function ($) {
    "use strict";
    let base_url = AppHelpers.base_url;

    $(document).on("click", ".show-punch-history", function () {
        $("#data-view-modal-dialog").addClass("modal-xl");
        $("#data-view-modal").modal("show");
        const tbody = $("#data-view-modal-body"); // Target the tbody element
        const user_id = $(this).data("user-id");
        const attendance_date = $(this).data("attendance-date");

        // Clear existing content and set a loading row
        tbody.html('<tr><td colspan="5">Loading...</td></tr>');

        // Make an Axios request with query params
        axios
            .get(`${base_url}/user-punch-history`, {
                params: {
                    user_id: user_id,
                    attendance_date: attendance_date,
                },
            })
            .then((response) => {
                if (response.data) {
                    $(".data-view-modal-header")
                        .empty()
                        .text(response.data.title);
                    tbody.empty().html(response.data.html); // Replace tbody content
                } else {
                    tbody.html(
                        '<p class="alert alert-danger">No Data Found!.</p>'
                    );
                }
            })
            .catch((error) => {
                tbody.html(
                    '<p class="alert alert-danger">Error loading punch history. Please try again.</p>'
                );
                console.error(error);
            });
    });
})(jQuery);
