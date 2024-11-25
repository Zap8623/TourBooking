<?php
require "../connect.php";
$type = $kq ='';
$query_search = "SELECT * FROM `tour`";

$query = "SELECT * FROM `tour`";
$result = mysqli_query($conn,$query);
if(!$result){
    die("Query failed");
}

if (isset($_POST['submit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $dest = $_POST['dest'];
    $dura = $_POST['dura'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $gia = $_POST['gia'];
    $desc = $_POST['desc'];
    $errors = [];

    if(intval(substr($dest,0,2)) == 84)
        $type = 'Trong nước';
    else
        $type = 'Ngoài nước';


    if (!filter_var($id, FILTER_VALIDATE_INT))
        $errors[] = "Mã Tour sai định dạng.";
    if (!filter_var($dura, FILTER_VALIDATE_INT) || $dura<=0)
        $errors[] = "Lịch trình phải là số nguyên dương.";
    if (!filter_var($gia, FILTER_VALIDATE_INT) || $gia<=0)
        $errors[] = "Giá phải là số nguyên dương.";

    if (empty($errors)) {
        $query = "INSERT INTO `tour` (`id_tour`, `name`, `id_dest`, `duration`, `date`, `time`, `type`, `price`, `desc`) VALUES (
                    '$id',
                    '$name',
                    '$dest',
                    '$dura',
                    '$date',
                    '$time',
                    '$type',
                    '$gia',
                    '$desc'
                )";
        $result = mysqli_query($conn,$query);
        if(!$result){
            die("Query failed");
        }
        else{
            $kq = "Thêm thành công";
        }
    } else {
        foreach ($errors as $error) {
            $kq .= $error . "<br>";
        }
    }
}

if(isset($_POST['reset'])){
    $id = $name = $dest = $dura = $date = $time = $type = $gia = $desc = $kq ='';
}

if (isset($_POST["search"])) {
    $s_dest = $_POST["s_dest"];
    $query_search = "SELECT * FROM `tour` WHERE (`id_dest` = '$s_dest')";
}

if (isset($_POST["view"])) {
    $query_search = "SELECT * FROM `tour`";
}

if (isset($_POST["edit"])) {
    $idToEdit = $_POST["idToEdit"];
    header("Location: edit_tour.php?id=$idToEdit");
    exit;
}

if (isset($_POST["delete"])) {
    $idToDelete = $_POST["idToDelete"];
    $deleteQuery = "DELETE FROM `tour` WHERE `id_tour`='$idToDelete'";
    if (mysqli_query($conn, $deleteQuery)) {
        echo "<script>alert('Xóa thành công!');</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Tour</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            text-align: center;
        }
        nav {
            margin: 20px 0;
        }
        nav a {
            margin: 0 15px;
            color: white;
            text-decoration: none;
        }
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>
<body>
<header>
    <h1>Trang quản lý Tour Du Lịch</h1>
    <nav>
        <a href="../home.php">Quay lại trang chủ</a>
    </nav>
</header>
<hr style="display: block;border-top: 3px solid #4CAF50;  margin: 8px 0;" />
<form method="post" action="">
    <table align="center" style="background-color: lightgreen">
        <tr>
            <td colspan="2" align="center"><h4>Nhập dữ liệu Tour</h4></td>
        </tr>
        <tr>
            <td>Mã Tour</td>
            <td><input type="text" name="id" value="<?php echo isset($id) ? $id : '' ?>"></td>
        </tr>
        <tr>
            <td>Tên</td>
            <td><input type="text" name="name" value="<?php echo isset($name) ? $name : '' ?>"></td>
        </tr>
        <tr>
            <td>Điểm đến</td>
            <td>
                <select name="dest">
                    <?php
                    $query = "SELECT * FROM `destination`";
                    $result = mysqli_query($conn,$query);
                    if(!$result){
                        die("Query failed");
                    }
                    if(mysqli_num_rows($result) != 0){
                        while ($row = mysqli_fetch_row($result)){
                            echo "<option value='".$row[0]."'>".$row[1]."</option>";
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Lịch trình</td>
            <td><input type="text" name="dura" value="<?php echo isset($dura) ? $dura : '' ?>"> ngày</td>
        </tr>
        <tr>
            <td>Ngày khởi hành</td>
            <td><input type="date" name="date" value="<?php echo isset($date) ? $date : '' ?>"></td>
        </tr>
        <tr>
            <td>Giờ khởi hành</td>
            <td><input type="time" name="time" value="<?php echo isset($time) ? $time : '' ?>"></td>
        </tr>
        <tr>
            <td>Giá</td>
            <td><input type="text" name="gia" value="<?php echo isset($gia) ? $gia : '' ?>"></td>
        </tr>
        <tr>
            <td>Thông tin mô tả</td>
            <td><textarea rows="5" name="desc"></textarea></td>
        </tr>
        <tr style="font-weight: bold">
            <td colspan="2" align="center" style="color: red"><?php echo $kq?></td>
        </tr>
        <tr style="font-weight: bold">
            <td align="center" colspan="2">
                <input type="submit" name="submit" value="Thêm dữ liệu">
                <input type="submit" name="reset" value="Reset dữ liệu">
            </td>
        </tr>
    </table>
    <hr style="display: block;border-top: 3px solid #4CAF50;  margin: 8px 0;" />
    <table align="center" style="background-color: lightgreen">
        <tr>
            <td colspan="2" align="center"><h4>Tìm kiếm tour</h4></td>
        </tr>
        <tr>
            <td>Điểm đến</td>
            <td>
                <select name="s_dest">
                    <?php
                    $query = "SELECT * FROM `destination`";
                    $result = mysqli_query($conn,$query);
                    if(!$result){
                        die("Query failed");
                    }
                    if(mysqli_num_rows($result) != 0){
                        while ($row = mysqli_fetch_row($result)){
                            echo "<option value='".$row[0]."'>".$row[1]."</option>";
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr style="font-weight: bold">
            <td align="center" colspan="2">
                <input type="submit" name="search" value="Tìm kiếm">
                <input type="submit" name="view" value="Reset">
            </td>
        </tr>
    </table>
    <table align="center" border="1" width="100%">
        <tr style="font-weight: bold">
            <td colspan="9" align="center" ><h4>Thông tin tour</h4></td>
        </tr>
        <tr>
            <td>Mã Tour</td>
            <td>Tên</td>
            <td>Điểm đến</td>
            <td>Lịch trình</td>
            <td>Ngày khởi hành</td>
            <td>Giờ khởi hành</td>
            <td>Loại</td>
            <td>Giá</td>
            <td>Chức năng</td>
        </tr>
        <?php
        $result = mysqli_query($conn, $query_search);
        if (!$result) die("Query failed: ");
        if (mysqli_num_rows($result) != 0) {
            $i = 1;
            while ($row = mysqli_fetch_row($result)) {
                $q_dest = "SELECT * FROM `destination` WHERE `id_dest` = '$row[2]'";
                $r_dest = mysqli_query($conn, $q_dest);
                if ($r_dest && mysqli_num_rows($r_dest) > 0) {
                    $row_d = mysqli_fetch_assoc($r_dest);
                    $dia = $row_d['name'];
                }
                echo "<tr style='" . ($i % 2 == 0 ? "background-color: lightblue" : "") . "'>";
                echo "<td>$row[0]</td>
                  <td>$row[1]</td>
                  <td>$dia</td>
                  <td>$row[3]</td>
                  <td>$row[4]</td>
                  <td>$row[5]</td>
                  <td>$row[6]</td>
                  <td>$row[7]</td>
                  <td>
                      <form method='post' action=''>
                          <input type='hidden' name='idToEdit' value='$row[0]'>
                          <input type='hidden' name='idToDelete' value='$row[0]'>
                          <input type='submit' name='edit' value='Sửa'>
                          <input type='submit' name='delete' value='Xóa' onclick=\"return confirm('Bạn có chắc chắn muốn xóa không?');\">
                      </form>
                  </td>
              </tr>";
                $i++;
            }
        }
        ?>
    </table>
</form>

<footer>
    <p>Địa chỉ: 123 Đường Du Lịch, Thành Phố Hồ Chí Minh</p>
    <p>Điện thoại: 0123 456 789</p>
    <p>Email: info@tourdulich.com</p>
</footer>
</body>
</html>
