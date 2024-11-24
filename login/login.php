<?php
session_start(); // Bắt đầu phiên làm việc

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tourbooking";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Xử lý khi người dùng nhấn nút đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_login'])) {
    $email = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Kiểm tra nếu các trường không được điền
    if (empty($email) || empty($password)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin!');</script>";
    } else {
        // Thoát dữ liệu đầu vào để tránh SQL Injection
        $email = mysqli_real_escape_string($conn, $email);
        $password = mysqli_real_escape_string($conn, $password);

        // Truy vấn kiểm tra tài khoản
        $sql = "SELECT id_user, name, password, status FROM user WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            // Kiểm tra trạng thái tài khoản
            if ($row['status'] === 'Banned') {
                echo "<script>alert('Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên!');</script>";
            } elseif ($row['status'] === 'Inactive') {
                echo "<script>alert('Tài khoản của bạn chưa được kích hoạt. Vui lòng kiểm tra email!');</script>";
            } else {
                // Kiểm tra mật khẩu
//                if (password_verify($password, $row['password'])) { //Kiểm tra password dạng mã hóa
                if ($password === $row['password']) {
                    // Đăng nhập thành công
                    $_SESSION['id_user'] = $row['id_user'];
                    $_SESSION['name'] = $row['name'];

                    // Điều hướng đến trang dashboard
                    header("Location: /home.php");
                    exit();
                } else {
                    echo "<script>alert('Sai mật khẩu! Vui lòng thử lại.');</script>";
                }
            }
        } else {
            echo "<script>alert('Email hoặc tài khoản không tồn tại!');</script>";
        }
    }
}

// Xử lý khi người dùng nhấn nút đăng ký
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_register'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra nếu các trường bị bỏ trống
    if (empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin!');</script>";
    } elseif ($password !== $confirm_password) {
        // Kiểm tra mật khẩu khớp
        echo "<script>alert('Mật khẩu và xác nhận mật khẩu không khớp!');</script>";
    } else {
//        // Mã hóa mật khẩu
//        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Thêm tài khoản mới vào bảng `user`
        $sql = "INSERT INTO user (email, phone, password) VALUES ('$email', '$phone', '$confirm_password')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.');</script>";
            header("Location: login.php"); // Điều hướng về trang đăng nhập
            exit();
        } else {
            if (mysqli_errno($conn) == 1062) {
                echo "<script>alert('Email đã được sử dụng. Vui lòng sử dụng email khác!');</script>";
            } else {
                echo "<script>alert('Đã xảy ra lỗi. Vui lòng thử lại sau.');</script>";
            }
        }
    }
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Đăng Nhập</title>
    <link rel="stylesheet" href="login_style.css">

</head>
<body>
    <div class="container">
        <!-- Phần bên trái: Slideshow -->
        <div class="left-side">
            <div class="slideshow">
                <img src="images/tour1.jpg" alt="Ảnh 1">
                <img src="images/tour2.jpg" alt="Ảnh 2">
                <img src="images/tour3.jpg" alt="Ảnh 3">
            </div>
        </div>

        <!-- Phần bên phải: Đăng nhập -->
        <div class="right-side">
            <div class="form-container">
                <h1>Đăng Nhập</h1>
                <form action="login.php" method="POST">
                    <input type="text" name="username" placeholder="Tên đăng nhập" required>
                    <input type="password" name="password" placeholder="Mật khẩu" required>
                    <input type="submit" name="submit_login" value="Đăng Nhập">
                </form>
                <a  href="#" id="forgot-password-link">Quên mật khẩu?</a>
                <a href="#" id="register-link">Đăng ký tài khoản</a>
            </div>
        </div>
    </div>

    <!-- Popup Đăng ký -->
    <div class="popup-overlay" id="popup-register">
        <div class="popup-content">
            <h2>Đăng Ký Tài Khoản</h2>
            <form action="login.php" method="POST">
                <input style="width: 80%" type="email" name="email" placeholder="Email" required>
                <input style="width: 80%" type="text" name="phone" placeholder="Số điện thoại" required>
                <input style="width: 80%" type="password" name="password" placeholder="Mật khẩu" required>
                <input style="width: 80%" type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
                <input style="width: 40%" type="submit" name="submit_register" value="Đăng Ký">
            </form>

            <button style="width: 40%" class="close-btn" id="close-register">Đóng</button>
        </div>
    </div>

    <!-- Popup Quên mật khẩu: Nhập email -->
    <div class="popup-overlay" id="popup-forgot-step1">
        <div class="popup-content">
            <h2>Quên Mật Khẩu</h2>
            <form id="forgot-step1-form">
                <input style="width: 80%" type="email" name="email" placeholder="Nhập email của bạn" required>
                <input style="width: 40%" type="submit" id="send-token" value="Xác nhận">
            </form>
            <button style="width: 40%" class="close-btn" id="close-forgot-step1">Đóng</button>
        </div>
    </div>

    <!-- Popup Quên mật khẩu: Nhập Code -->
    <div class="popup-overlay" id="popup-forgot-step2">
        <div class="popup-content">
            <h2>Xác Nhận Code</h2>
            <form id="forgot-step2-form">
                <input style="width: 80%" type="text" name="token" placeholder="Nhập mã code" required>
                <input style="width: 40%" type="submit" id="verify-token" Xác nhận>
            </form>
            <button style="width: 40%" class="close-btn" id="close-forgot-step2">Đóng</button>
        </div>
    </div>

    <!-- Popup Quên mật khẩu: Đổi mật khẩu -->
    <div class="popup-overlay" id="popup-forgot-step3">
        <div class="popup-content">
            <h2>Đặt Mật Khẩu Mới</h2>
            <form id="forgot-step3-form">
                <input style="width: 80%" type="password" name="new_password" placeholder="Mật khẩu mới" required>
                <input style="width: 80%" type="password" name="confirm_password" placeholder="Xác nhận mật khẩu mới" required>
                <input style="width: 40%" type="submit" Đổi Mật Khẩu>
            </form>
            <button style="width: 40%" class="close-btn" id="close-forgot-step3">Đóng</button>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
