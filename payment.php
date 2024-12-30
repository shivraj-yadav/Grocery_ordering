<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grocery Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .payment-container {
            padding: 30px;
            max-width: 400px;
            margin: 50px auto;
            border: 2px solid #4CAF50;
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .payment-container h3 {
            font-family: 'Lato', sans-serif;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        .payment-container img {
            width: 100px;
            margin-bottom: 15px;
        }

        .payment-container p {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
        }

        .payment-container script {
            display: inline-block;
        }

        .payment-container .btn-note {
            font-size: 14px;
            color: #888;
            margin-top: 15px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <img src="https://img.icons8.com/fluency/96/vegetarian-food.png" alt="Grocery Logo">
        <h3>Complete Your Grocery Payment</h3>
        <p>Secure payment for your Groceries.</p>
<form>
 <script src="https://checkout.razorpay.com/v1/payment-button.js" data-payment_button_id="pl_PLbNCg4EsIUpf7" async> 
</script> 
</form>
<span class="btn-note">100% secure payment with Razorpay</span>
    </div>
</body>
</html>