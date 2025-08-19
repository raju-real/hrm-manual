(function ($) {
    "use strict";

    /**
     * Common Helper Module
     */
    window.AppHelpers = (function () {
        // Set base URL dynamically from a meta tag
        const base_url = $('meta[name="base-url"]').attr('base_url') || window.location.origin;
        const currentPage = window.location.pathname.split('/').filter(Boolean).pop();

        /**
         * Display a SweetAlert notification
         * @param {string} type - Type of alert: success, error, warning, info
         * @param {string} title - Title of the alert
         * @param {string} message - Message to display
         */
        const showAlert = (type, title, message) => {
            Swal.fire({
                icon: type,
                title: title,
                text: message,
                confirmButtonText: 'OK'
            });
        };

        /**
         * Perform an AJAX request with common error handling
         * @param {string} url - URL for the AJAX request
         * @param {string} method - HTTP method (GET, POST, etc.)
         * @param {object} data - Data to send with the request
         * @param {function} onSuccess - Callback function for successful response
         * @param {function} onError - Callback function for error response
         */
        const ajaxRequest = (url, method, data, onSuccess, onError) => {
            $.ajax({
                url: base_url + url,
                type: method,
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (onSuccess && typeof onSuccess === 'function') {
                        onSuccess(response);
                    }
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON?.errors || {};
                    const errorMessage = xhr.responseJSON?.message || 'Something went wrong!';
                    if (onError && typeof onError === 'function') {
                        onError(errorMessage, errors);
                    } else {
                        showAlert('error', 'Error', errorMessage);
                    }
                }
            });
        };

        /**
         * Format input value (e.g., for masked verification codes)
         * @param {string} value - Input value to format
         * @param {string} separator - Separator for formatting
         * @param {number} maxLength - Maximum length of formatted value
         * @returns {string} - Formatted value
         */
        const formatInput = (value, separator = ' - ', maxLength = 11) => {
            let cleanValue = value.replace(/[^0-9]/g, ''); // Only allow numeric values
            return cleanValue.split('').join(separator).slice(0, maxLength); // Format and truncate
        };

        /**
         * Show toast message
         * @param type
         * @param message
         * @param title
         * @param position
         */
        function showToast(type, message, title = '', position = 'top-right') {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-" + position, // e.g., toast-bottom-left
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": 300,
                "hideDuration": 1000,
                "timeOut": 5000,
                "extendedTimeOut": 1000,
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // Choose type: success, error, info, warning
            toastr[type](message, title);
        }


        // Expose public methods
        return {
            base_url: base_url,
            current_page: currentPage,
            showAlert: showAlert,
            ajaxRequest: ajaxRequest,
            formatInput: formatInput,
            showToast: showToast
        };
    })();
})(jQuery);
