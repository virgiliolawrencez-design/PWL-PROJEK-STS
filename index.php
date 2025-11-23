<!DOCTYPE html>
<html lang="en">
<head>
    <title>Document</title>
    <link rel="stylesheet" href="index.css">
    <script>
        // Redirect to member.php for member login
        function loginMember() {
            window.location.href = 'main/member.php';
        }

        // Redirect to admin.php for admin login
        function loginAdmin() {
            window.location.href = 'Dahsboard/admin.php';
        }
    </script>
</head>
<body>
    <div class="foreground-text">AFA Internet CAFE</div>

    <div class="btn1">
        <button onclick="loginMember()" class="btn">Login Member</button>
        <button onclick="loginAdmin()" class="btn">Login Admin</button>
    </div>

    <div class="wrapper">
        <div class="skewed"></div>
    </div>
</body>
</html>