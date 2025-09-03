(function ($) {
    "use strict";

    const base_url =
        $('meta[name="base-url"]').attr("base_url") || window.location.origin;

    // Global AJAX setup for CSRF & JSON
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            Accept: "application/json",
        },
    });

    // Bootstrap Toast
    const toastEl = document.getElementById("attendanceToast");
    const toastBody = document.getElementById("toastMessage");
    const toast = new bootstrap.Toast(toastEl, { delay: 5000, autohide: true });

    function showToast(message, bgColor = "bg-primary") {
        toastBody.textContent = message;
        toastEl.className = `toast text-white ${bgColor} border-0`;
        toast.show();
    }

    // Handle attendance button click
    $(document).on("click", ".btn-manual-attendance", function (e) {
        e.preventDefault();

        let $btn = $(this); // clicked button
        let direction = $btn.data("direction");

        if (!navigator.geolocation) {
            showAlert("Geolocation not supported.", "danger");
            return;
        }

        const branch_id = $('#branch').val();
        if(direction === 'in' && !branch_id) {
            $('#branch').addClass('is-invalid');
            showAlert("Select branch first!", 'danger');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                let lat = pos.coords.latitude;
                let lng = pos.coords.longitude;

                $.ajax({
                    url: base_url + "/punch-manual",
                    type: "POST",
                    data: {
                        branch: branch_id,
                        direction: direction,
                        latitude: lat,
                        longitude: lng,
                    },
                    success: function (res) {
                        let type =
                            res.status === "success"
                                ? "success"
                                : res.status === "error"
                                ? "danger"
                                : "info";

                        showAlert(res.message, type);

                        // Disable the clicked button immediately
                        $btn.prop("disabled", true);

                        if (direction === "in") {
                            // If user checked in → disable check-in, enable check-out
                            $("#checkOutBtn").prop("disabled", false);
                            $("#checkInBtn").prop("disabled", true);
                            $("#branch").prop("disabled", true);
                        } else if (direction === "out") {
                            // If user checked out → disable check-out, enable check-in
                            $("#checkInBtn").prop("disabled", false);
                            $("#checkOutBtn").prop("disabled", true);
                            $("#branch").prop("disabled", false);
                        }
                    },
                    error: function (xhr) {
                        let response = xhr.responseJSON || {};
                        showAlert(response.error || "Failed", "danger");
                    },
                });
            },
            function (err) {
                showAlert("Cannot get location: " + err.message, "danger");
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });

    // Function to show in-page alert
    // Function to show in-page alert above buttons
    function showAlert(message, type = "primary") {
        // Remove any previous alert
        $(".alert-container").empty();

        // Create new alert
        let alertHtml = `
        <div class="alert alert-${type} fade show text-center py-1" role="alert" style="min-height:35px; width:100%;">
            ${message}
        </div>
    `;

        $(".alert-container").append(alertHtml);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            $(".alert-container .alert").alert("close");
        }, 5000);
    }
})(jQuery);
