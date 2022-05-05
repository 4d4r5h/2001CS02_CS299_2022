<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Index</title>
    <style>
        a:link,
        a:visited {
            background-color: white;
            color: black;
            border: 2px solid green;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
            font-size: medium;
            transition: background-color 0.3s;
        }

        a:hover,
        a:active {
            background-color: green;
            transition: background-color 0.3s;
            color: white;
        }

        input[type=number] {
            width: 20%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 2px solid black;
            border-radius: 5px;
            box-sizing: border-box;
            font-weight: bold;
            font-size: medium;
        }

        input[type=submit] {
            width: 15%;
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            font-size: medium;
        }

        input[type=submit]:hover {
            background-color: #367d39;
        }

        input[type=file] {
            width: 18%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 2px solid black;
            border-radius: 5px;
            box-sizing: border-box;
            font-weight: bold;
            font-size: medium;
        }

        div {
            text-align: center;
            padding: 198px 0px 198px 0px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            background-image: url('images/bg.jpg');
            background-size: 100% 100%;
        }
    </style>
</head>

<body>
    <?php
    include 'dbconnect.php';

    $tacnt = 0;
    $subcnt = 0;

    $sql = "SELECT * FROM subject";
    $result = mysqli_query($conn, $sql);
    if (empty($result)) {
        $ctsql = "CREATE TABLE subject (sno INT(20) AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL);";
        $ctresult = mysqli_query($conn, $ctsql);
        if ($ctresult == false) {
            die("Error creating table: " . mysqli_error($conn));
        }
        $sql = "SELECT * FROM subject";
        $result = mysqli_query($conn, $sql);
    }
    if ($result) {
        $subcnt = mysqli_num_rows($result);
    }

    $sql = "SELECT * FROM ta";
    $result = mysqli_query($conn, $sql);
    if (empty($result)) {
        $ctsql = "CREATE TABLE ta (sno INT(20) AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL);";
        $ctresult = mysqli_query($conn, $ctsql);
        if ($ctresult == false) {
            die("Error creating table: " . mysqli_error($conn));
        }
        $sql = "SELECT * FROM ta";
        $result = mysqli_query($conn, $sql);
    }
    if ($result) {
        $tacnt = mysqli_num_rows($result);
    }

    mysqli_close($conn);
    ?>
    <div>
        <a href="add_subject.php">Add Subjects</a>
        <a href="add_ta.php">Add TAs</a>
        <br>
        <br>

        <form action="start_selection.php" method="post">
            <input type="number" id="pref" name="pref" placeholder="Number of preferences" min="1" required><br>
            <input type="submit" value="Submit" <?php
                                                $res = $tacnt * $subcnt;
                                                if ($res == 0 || $tacnt < $subcnt) {
                                                    echo " disabled";
                                                }
                                                ?>>
            <br>
        </form>
        <br>
        <form method="POST" action="start_edit_selection.php" enctype="multipart/form-data">
            <input type='file' name="taFile" required accept=".csv" title="TAs File">
            <input type='file' name="selectionFile" required accept=".csv" title="Selection File">
            <br>
            <input type="submit" value="Edit Selection">
        </form>
    </div>
</body>

</html>