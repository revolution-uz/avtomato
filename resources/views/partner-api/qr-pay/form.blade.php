<!DOCTYPE html>
<html>
<head>
    <title>QR Pay</title>
</head>
<body>
<h1>Enter Amount</h1>
<form action="{{ route('qrpay.create') }}" method="POST">
    @csrf
    <label for="amount">Amount:</label>
    <input type="text" id="amount" name="amount" required>
    <button type="submit">Generate Cheque</button>
</form>
@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
</body>
</html>
