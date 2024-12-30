<?php
require_once("../DBConnection.php");

if(isset($_GET['id'])){
    // Fetch the product data based on the ID
    $qry = $conn->query("SELECT * FROM `product_list` WHERE product_id = '{$_GET['id']}'");
    foreach($qry->fetch_assoc() as $k => $v){
        $$k = $v;
    }
}
?>

<div class="container-fluid">
    <form action="" id="product-form">
        <!-- Hidden field for product ID -->
        <input type="hidden" name="id" value="<?php echo isset($product_id) ? $product_id : '' ?>">

        <!-- Product Name -->
        <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="control-label">Name</label>
                        <input type="text" name="name" id="name" required class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : '' ?>">
                    </div>

                    <!-- Product Description -->
                    <div class="form-group">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" id="description" required class="form-control rounded-0" cols="30" rows="4"><?php echo isset($description) ? $description : '' ?></textarea>
                    </div>

                    <!-- Category Selection -->
                    <div class="form-group">
                        <label for="category_id" class="control-label">Category</label>
                        <select name="category_id" id="category_id" required class="form-select form-select-sm rounded-0">
                            <option <?php echo (!isset($category_id)) ? 'selected' : '' ?> disabled>Please Select Category</option>
                            <?php
                            // Fetching categories from the database
                            $categories = $conn->query("SELECT * FROM category_list WHERE `status` = 1 ORDER BY `name` ASC");
                            while($row = $categories->fetch_assoc()):
                            ?>
                                <option value="<?php echo $row['category_id'] ?>" <?php echo (isset($category_id) && $category_id == $row['category_id'] ) ? 'selected' : '' ?>>
                                    <?php echo $row['name'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Product Price -->
                    <div class="form-group">
                        <label for="price" class="control-label">Price</label>
                        <input type="number" step="any" name="price" id="price" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($price) ? $price : '' ?>">
                    </div>

                    <!-- Thumbnail Upload -->
                    <div class="form-group">
                        <label for="thumbnail" class="control-label">Thumbnail</label>
                        <input type="file" name="thumbnail" id="thumbnail" class="form-control form-control-sm rounded-0" accept="image/png, image/jpeg">
                        <?php if(isset($product_id)): ?>
                            <small class="text-info"><i>Upload only if you wish to update the product thumbnail.</i></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="quantity" class="control-label">Quantity</label>
                        <input type="number" step="any" name="quantity" id="quantity" required class="form-control form-control-sm rounded-0 text-end" >
                    </div>

                    <!-- Additional Images Upload -->
                    <div class="form-group">
                        <label for="img" class="control-label">Images</label>
                        <input type="file" name="img[]" id="img" class="form-control form-control-sm rounded-0" multiple accept="image/png, image/jpeg">
                        <?php if(isset($product_id)): ?>
                            <small class="text-info"><i>Upload only if you wish to add more images.</i></small>
                        <?php endif; ?>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status" class="control-label">Status</label>
                        <select name="status" id="status" class="form-select form-select-sm rounded-0">
                            <option value="1" <?php echo (isset($status) && $status == 1) ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?php echo (isset($status) && $status == 0) ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
$(function() {
    $('#product-form').submit(function(e) {
        e.preventDefault();
        $('.pop_msg').remove();

        var form = $(this);
        var formData = new FormData(form[0]);
        var message = $('<div>').addClass('pop_msg');

        $('#uni_modal button').attr('disabled', true).text('Submitting form...');

        $.ajax({
            url: '../Actions.php?a=save_product',
            data: formData,
            type: 'POST',
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(resp) {
                if (resp.status === 'success') {
                    message.addClass('alert alert-success').text(resp.msg);
                    if (!form.find('input[name="id"]').val()) {
                        form[0].reset();
                    }
                    $('#uni_modal').on('hide.bs.modal', function() {
                        location.reload();
                    });
                } else {
                    message.addClass('alert alert-danger').text(resp.msg);
                }
                form.prepend(message).show('slow');
            },
            error: function(err) {
                console.log(err);
                message.addClass('alert alert-danger').text("An error occurred.");
                form.prepend(message).show('slow');
            },
            complete: function() {
                $('#uni_modal button').attr('disabled', false).text('Save');
            }
        });
    });
});
</script>
