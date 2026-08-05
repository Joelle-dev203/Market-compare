<!DOCTYPE html>
<html>
<body>
    <h1>Great news!</h1>
    <p>A product you are watching has dropped to your target price.</p>
    
    <p><strong>Target Price:</strong> {{ number_format($alert->target_price) }} CFA</p>
    
    <a href="{{ url('/product/' . $alert->product_id) }}" 
       style="background: #059669; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
       View Product Now
    </a>
</body>
</html>