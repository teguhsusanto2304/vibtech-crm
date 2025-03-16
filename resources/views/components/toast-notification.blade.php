<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0"
        role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">Success message here</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<script>
    function showToast(message, type = "success") {
        let toastId = type === "success" ? "successToast" : "errorToast";
        // Update the toast message
        $("#" + toastId + " .toast-body").text(message);
        // Show the toast
        let toast = new bootstrap.Toast(document.getElementById(toastId));
        toast.show();
    }
</script>
