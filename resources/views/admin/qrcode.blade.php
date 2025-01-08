
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div class="container">
        <h1>QR Code</h1>
        <img src="{{ asset('storage/' . $filePath) }}" alt="QR Code" style="max-width: 300px;">
    </div>
</body>
</html>