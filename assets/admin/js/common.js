(function ($) {
    "use strict";
    /**
     * Ajax csrf token setup
     */
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $(".select2").select2();
    $(".select2-search-disable").select2({ minimumResultsForSearch: 1 / 0 });
    // $(".form-select").select2({ minimumResultsForSearch: 1 / 0 });



    $(document).on("click", ".view-image", function () {
        const imageUrl = $(this).data("image-url");
        $("#modalImage").attr("src", imageUrl); // Set the image URL in the modal
        $("#viewImageModal").modal("show"); // Show the modal
    });

    $(document).on("submit", "#prevent-form", function () {
        let spinTag = "<i class='fa fa-spinner fa-spin me-2 spinner'></i>";
        let text = " Please wait...";
        let buttonText = spinTag + text;
        $(".submit-button").prop("disabled", true).html(buttonText);
    });

    $(document).on("click", ".delete-data", function (e) {
        e.preventDefault();
        let target = $(this).attr("data-id");
        Swal.fire({
            title: "Are you sure?",
            text: "You won't to delete this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#" + target).submit();
            }
        });
    });


})(jQuery);
