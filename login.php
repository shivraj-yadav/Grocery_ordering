<style>
    #uni_modal .modal-footer{
        display:none;
    }
</style>
<div class="container-fluid">
<form action="" id="login-form">
<input type="hidden" name="id">
    <div class="col-12">
        <div class="row">
            <div class="col-12 mb-2">
                <div class="form-group">
                    <label for="username" class="control-label">Username</label>
                    <input type="username" name="username" autofocus id="username" required class="form-control form-control-sm rounded-0" value="">
                </div>
                <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                    <input type="password" name="password" id="password" required class="form-control form-control-sm rounded-0" value="">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-sm btn-primary rounded-0 me-1">Login</button>
                <button class="btn btn-sm btn-dark rounded-0" type="button" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</form>
</div>
<script>
   $(function(){
    $('#login-form').submit(function(e){
        e.preventDefault(); // Prevent default form submission
        $('.pop_msg').remove(); // Remove previous messages
        var _this = $(this);
        var _el = $('<div>'); // Create a new div for messages
        _el.addClass('pop_msg');

        // Disable button and show logging in message
        _this.find('button').attr('disabled', true);
        _this.find('button[type="submit"]').text('Logging in...');

        // AJAX request to login
        $.ajax({
            url: 'Actions.php?a=customer_login',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'JSON',
            error: err => {
                console.log(err); // Log any error
                _el.addClass('alert alert-danger');
                _el.text("An error occurred.");
                _this.prepend(_el); // Prepend message to the form
                _el.show('slow');

                // Enable button and reset text after error
                _this.find('button').attr('disabled', false);
                _this.find('button[type="submit"]').text('Login'); // Fix 'Save' text
            },
            success: function(resp) {
                if (resp.status == 'success') {
                    _el.addClass('alert alert-success');
                    _el.text(resp.msg);

                    // Redirect after successful login
                    setTimeout(() => {
                        location.replace('./'); // Redirect to the main page
                    }, 2000);
                } else {
                    _el.addClass('alert alert-danger');
                    _el.text(resp.msg); // Show error message
                }

                _el.hide(); // Hide the message initially
                _this.prepend(_el); // Prepend the message to the form
                _el.show('slow'); // Show the message

                // Enable button and reset text after response
                _this.find('button').attr('disabled', false);
                _this.find('button[type="submit"]').text('Login'); // Fix 'Save' text
            }
        });
    });
});

</script>