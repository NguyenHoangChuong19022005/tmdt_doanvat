<!DOCTYPE html >
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>    
<link href="../foldercss/style.css" rel="stylesheet" />
<link href="../foldercss/tintuc.css" rel="stylesheet" />
<script src="../jquery-3.7.1.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>
<div id="container">
    <div id="include-header"></div>
    <script>
        $(function () {
            $("#include-header").load("header.html");
        });
    </script>


<div id="include-footer">
            <script>
    $(function () {
        $("#include-footer").load("footer.html");
    });
    </script>
</div>
</body>
</html>
