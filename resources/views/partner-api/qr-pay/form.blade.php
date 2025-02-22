<!DOCTYPE html>
<html>
<head>
    <title>QR Pay</title>
</head>
<body>
<h1>Enter Amount</h1>
<form action="{{ route('qr-pay.create') }}" method="POST">
    @csrf
    <label for="amount">Amount:</label>
    <input type="text" id="amount" name="amount" required>
    <button type="submit">Generate Cheque</button>
</form>
</body>
</html>
