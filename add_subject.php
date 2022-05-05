<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Subject</title>
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

        input[type=text] {
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
    <div>
        <a href="index.php">Go Back</a>
        <br>
        <br>
        <form action="add_subject.php" method="post">
            <input type="text" id="name" name="name" placeholder="Name of Subject" required><br>
            <input type="submit" value="Submit">
        </form>
        <br>
        <a href="show_subject.php">Show Subjects</a>
        <br>
        <br>
        <a href="import_subject.php">Import CSV File</a>
        <br>
        <p id="demo"></p>

        <?php
        include 'dbconnect.php';

        $sql = "SELECT * FROM subject";
        $result = mysqli_query($conn, $sql);
        if (empty($result)) {
            $ctsql = "CREATE TABLE subject (sno INT(20) AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL);";
            $ctresult = mysqli_query($conn, $ctsql);
            if ($ctresult == false) {
                die("Error creating table: " . mysqli_error($conn));
            }
        }

        $result = mysqli_query($conn, $sql);
        if ($result) {
            $rowcount = mysqli_num_rows($result);
            echo "<script>
    document.getElementById('demo').innerHTML = 'Number of Subjects = <b>$rowcount</b>';
    </script>";
        }

        function test_input($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = test_input($_POST["name"]);
            $sql = "INSERT INTO subject (sno, name) VALUES ( NULL, '$name');";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $sql = "SELECT * FROM subject";
                if ($result = mysqli_query($conn, $sql)) {
                    $rowcount = mysqli_num_rows($result);
                    echo "<script>
            document.getElementById('demo').innerHTML = 'Number of Subjects = $rowcount';
            </script>";
                }
                echo "New record created successfully.";
            } else {
                die("Error: " . $sql . "<br>" . mysqli_error($conn));
            }
        }

        mysqli_close($conn);
        ?>
    </div>
</body>

</html>