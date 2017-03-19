// PRODUCTS
// Remove product
$(document).on("click", ".open-RemoveProductModal", function () {
    var productId = $(this).data('id');
    $(".modal-body #productId").val(productId);
});