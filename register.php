<?php
require_once("DBConnection.php");

?>
<style>
    #uni_modal .modal-footer{
        display:none;
    }
</style>
<div class="container-fluid">
<form action="" id="register-form">
<input type="hidden" name="id">
    <div class="col-12">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fullname" class="control-label">Full Name</label>
                    <input type="text" name="fullname" id="fullname" required class="form-control form-control-sm rounded-0" value="">
                </div>
                <div class="form-group">
                    <label for="email" class="control-label">Email</label>
                    <input type="email" name="email" id="email" required class="form-control form-control-sm rounded-0" value="">
                </div>
                <div class="form-group">
                    <label for="contact" class="control-label">Contact</label>
                    <input type="text" name="contact" id="contact" required class="form-control form-control-sm rounded-0" value="">
                </div>
                <div class="form-group">
                    <label for="address" class="control-label">Address</label>
                    <textarea rows="2" name="address" id="address" required class="form-control form-control-sm rounded-0"></textarea>
                </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
                    <label for="username" class="control-label">Username</label>
                    <input type="username" name="username" id="username" required class="form-control form-control-sm rounded-0" value="">
                </div>
                <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                    <input type="password" name="password" id="password" required class="form-control form-control-sm rounded-0" value="">
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-sm btn-primary rounded-0 me-1">Create Account</button>
                <button class="btn btn-sm btn-dark rounded-0" type="button" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</form>
</div>

<script>$(function(){
    $('#register-form').submit(function(e){
        e.preventDefault();
        $('.pop_msg').remove();
        var _this = $(this);
        var _el = $('<div>');
        _el.addClass('pop_msg');
        
        // Disable the button and show submitting text
        $('#uni_modal button').attr('disabled', true);
        $('#uni_modal button[type="submit"]').text('Submitting form...');
        
        $.ajax({
            url: 'Actions.php?a=save_customer',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'JSON',
            error: function(err){
                console.log(err);  // Log error for more detail in console
                _el.addClass('alert alert-danger');
                _el.text("An error occurred: " + (err.responseText || 'Unknown error'));
                _this.prepend(_el);
                _el.show('slow');
                
                // Enable the button and reset the text after error
                $('#uni_modal button').attr('disabled', false);
                $('#uni_modal button[type="submit"]').text('Save');
            },
            success: function(resp){
                if(resp.status == 'success'){
                    _el.addClass('alert alert-success');
                    _el.text(resp.msg);
                    
                    // Redirect after successful account creation
                    setTimeout(() => {
                        uni_modal('Please Enter your Login Credentials', "login.php");
                    }, 2500);
                } else {
                    _el.addClass('alert alert-danger');
                    _el.text(resp.msg);
                }

                _el.hide();
                _this.prepend(_el);
                _el.show('slow');

                // Enable the button and reset the text
                $('#uni_modal button').attr('disabled', false);
                $('#uni_modal button[type="submit"]').text('Save');
            }
        });
    });
});


</script>