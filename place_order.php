<style>
    #uni_modal .modal-footer{
        display:none;
    }
</style>
<?php
session_start();
require_once("DBConnection.php");
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
        <form action="" id="place-order">
            <input type="hidden" name="total_amount" value="<?php echo $_GET['total'] ?>">
            <div class="form-group">
                <label for="fee_id" class="control-label">Delivery Location</label>
                <select name="fee_id" id="fee_id" class="form-select form-select-sm select2" required>
                    <option value="" disabled <?php echo !isset($fee_id) ? "selected" : '' ?>></option>
                    <?php 
                    $fees = $conn->query("SELECT * FROM `fees_list` where status = 1 order by `location` asc ");
                    while($row = $fees->fetch_assoc()):
                    ?>
                    <option value="<?php echo $row['fee_id'] ?>" data-amount = "<?php echo $row['amount'] ?>"><?php echo $row['location'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="delivery_address" class="control-label">Delivery Address Other Information</label>
                <textarea name="delivery_address" id="delivery_address" cols="30" rows="3" class="form-control rounded-0" placeholder="ie. Lot 23 Block 6, There Ville" required></textarea>
            </div>
        </form>
        </div>
        <div class="col-md-6">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th>Sub-Total</th>
                        <th class="text-end" id="csub-total"><?php echo $_GET['total'] ?></th>
                    </tr>
                    <tr>
                        <th>Delivery Fee</th>
                        <th class="text-end" id="cfee">0</th>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <th class="text-end" id="ctotal"><?php echo $_GET['total'] ?></th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-12 mt-3 text-center">
                <button class="btn btn-sm btn-primary rounded-0 my-1" form="place-order">Place Order</button>
                <button class="btn btn-sm btn-dark rounded-0" type="button" data-bs-dismiss="modal">Cancel</button>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#fee_id').change(function(){
            var fee_id = $(this).val();
            var amount = $('#fee_id option[value="'+fee_id+'"]').attr('data-amount');
            var sub = $('#csub-total').text().replace(/\,/gi,'');
            var total = parseFloat(sub) + parseFloat(amount);
            $('#cfee').text(parseFloat(amount).toLocaleString('en-US'));
            $('#ctotal').text(parseFloat(total).toLocaleString('en-US'));
        });

        $('#place-order').submit(function(e){
            e.preventDefault();
            if($('#fee_id').val() == ""){
                alert("Please select a delivery location.");
                return false;
            }
            $.ajax({
                url: 'Actions.php?a=place_order',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'JSON',
                error: function(err){
                    alert("An error occurred. Please try again.");
                    console.log(err);
                },
                success: function(resp){
                    if(resp.status == 'success'){
                        // Redirect to payment.php with the order ID
                        if(resp.redirect) {
                            location.replace(resp.redirect);
                        } else {
                            alert("Order placed, but redirection information is missing.");
                        }
                    } else {
                        alert(resp.msg);
                    }
                }
            });
        });
    });
</script>

