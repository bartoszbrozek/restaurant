// PRODUCTS
// Remove product
$(document).on("click", ".open-RemoveEntityModal", function () {
    var entityId = $(this).data('id');
    $(".modal-body #entityId").val(entityId);
});

