<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Import Subject</title>
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
            padding: 265px 0px 265px 0px;
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
    include "dbconnect.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $target_dir = "";
        $target_file = $target_dir . basename($_FILES["file"]["name"]);

        $fileType = pathinfo($target_file, PATHINFO_EXTENSION);

        if ($fileType != "csv") {
            die("Error: Please upload only CSV File.");
        }

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {

            $fileExists = 0;
            if (!(file_exists($target_file))) {
                die("Error: File do not exists.");
            }

            $file = fopen($target_file, "r");
            $i = 0;
            $importData_arr = array();

            while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                $num = count($data);
                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = mysqli_real_escape_string($conn, $data[$c]);
                }
                $i++;
            }

            fclose($file);

            $skip = 0; // initialize $skip with 0 for skipping first row, with 1 otherwise

            foreach ($importData_arr as $data) {
                if ($skip != 0) {
                    $name = $data[0];
                    if (ctype_digit(strval($name)))
                        $name = $data[1];
                    $sql = "INSERT INTO subject (sno, name) VALUES ( NULL, '$name');";
                    $result = mysqli_query($conn, $sql);
                    if ($result == false) {
                        die("Error: " . $sql . "<br>" . mysqli_error($conn));
                    }
                }
                $skip++;
            }

            $newtargetfile = $target_file;

            if (file_exists($newtargetfile)) {
                unlink($newtargetfile);
            }

            header("Location: add_subject.php");
            exit();
        }
    }
    ?>

    <div>
        <a href="add_subject.php">Go Back</a>
        <br>
        <br>
        <form method="POST" action="import_subject.php" enctype="multipart/form-data">
            <input type='file' name="file" required accept=".csv">
            <br>
            <input type="submit" value="Import">
        </form>
    </div>
</body>

</html>