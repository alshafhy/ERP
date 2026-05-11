<script>
function confirmDelete(id, type = "customer") {
    const text = type === "customer" 
        ? "{{ __("messages.confirm_delete_text_customer") }}" 
        : "{{ __("messages.confirm_delete_text_vehicle") }}";

    Swal.fire({
        title: "{{ __("messages.confirm_delete_title") }}",
        text: text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "var(--primary-red)",
        cancelButtonColor: "#aaa",
        confirmButtonText: "{{ __("messages.delete_button") }}",
        cancelButtonText: "{{ __("messages.cancel_button") }}",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("delete-form-" + id).submit();
        }
    });
}

// Global SweetAlert confirmations for forms with data-confirm
document.querySelectorAll(".confirm-action").forEach(button => {
    button.addEventListener("click", function(e) {
        e.preventDefault();
        const form = this.closest("form");
        const title = this.dataset.confirmTitle || "{{ __("messages.confirm_delete_title") }}";
        const text = this.dataset.confirmText || "";

        Swal.fire({
            title: title,
            text: text,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "var(--primary-red)",
            cancelButtonColor: "#aaa",
            confirmButtonText: "{{ __("messages.confirm_button") }}",
            cancelButtonText: "{{ __("messages.cancel_button") }}",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
