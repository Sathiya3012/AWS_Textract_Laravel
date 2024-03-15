<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
</head>
<body>
    <form method="POST" action="/extract-text" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" accept=".pdf, .png, .jpg, .jpeg">
        <button type="submit">Upload File</button>
    </form>
</body>
</html>