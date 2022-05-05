<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Start Edit Selection</title>
    <style>
        .vl {
            border-left: 2px solid black;
        }

        .fcol {
            display: inline-block;
            width: 50px;
            padding: 5px;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
            font-size: medium;
        }

        .scol {
            display: inline-block;
            width: 150px;
            padding: 5px;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
            font-size: medium;
        }

        .tcol {
            display: inline-block;
            width: 170px;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
            font-size: medium;
        }

        select {
            margin: 5px 15px 5px 15px;
            width: 150px;
            padding: 5px;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
            font-size: medium;
        }

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

        div {
            overflow-x: auto;
            background-image: url('images/bg.jpg');
            background-attachment: fixed;
            background-size: cover;
            padding: 10px;
        }

        body {
            margin: 0px;
        }

        .backLink {
            text-align: center;
        }

        .submitButton {
            text-align: center;
        }

        hr {
            border-top: 2px solid black;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script>
        var json = {
            TAs: [
                <?php
                include 'dbconnect.php';

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $target_dir = "";
                    $target_file = $target_dir . basename($_FILES["taFile"]["name"]);

                    $fileType = pathinfo($target_file, PATHINFO_EXTENSION);

                    if ($fileType != "csv") {
                        die("Error: Please upload only CSV File.");
                    }

                    if (move_uploaded_file($_FILES["taFile"]["tmp_name"], $target_file)) {

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
                                $email = $data[1];
                                if (ctype_digit(strval($name))) {
                                    $name = $data[1];
                                    $email = $data[2];
                                }
                                if ($email == "" || (!isset($email)))
                                    $email = "NA";
                                $sql = "INSERT INTO tta (sno, name, email) VALUES (NULL, '$name', '$email');";
                                $result = mysqli_query($conn, $sql);
                                if (empty($result)) {
                                    $ctsql = "CREATE TABLE tta (sno INT(20) AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL);";
                                    $ctresult = mysqli_query($conn, $ctsql);
                                    if ($ctresult == false) {
                                        die("Error creating table: " . mysqli_error($conn));
                                    }
                                    $sql = "INSERT INTO tta (sno, name, email) VALUES (NULL, '$name', '$email');";
                                    $result = mysqli_query($conn, $sql);
                                }
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

                        // exit();
                    }
                }

                $sql = "SELECT * FROM tta ORDER BY name";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                    $name = $row['name'];
                    $sno = $row['sno'];
                    echo  " 
                        { 'Name': '$name',
                        'Sno': '$sno'
                    },
                        ";
                }
                echo  " { 'Name': 'None', 'Sno': '-1' } ";
                mysqli_close($conn);
                ?>
            ]
        };

        $(document).ready(function() {

            $(function() {
                $('.dropdown').change(function() {
                    var selectedValue = $("option:selected", this).val();
                    var elementName = ($(this).attr('id'));
                    var arr = [];
                    $(this).prevAll(".dropdown").each(function(i, t) {
                        arr.push($("option:selected", t).val())
                    });
                    arr.push(selectedValue);
                    $(this).nextAll(".dropdown").each(function(i, t) {
                        arr.push($("option:selected", t).val())
                    });
                    $(this).nextAll(".dropdown").each(function(t, q) {
                        if ($(q).attr('id') != elementName) {
                            var temp = $(q).val();
                            $(q).empty();
                            $.each(json.TAs, function(key, value) {
                                if ($.inArray(json.TAs[key].Sno, arr) == -1 || json.TAs[key].Sno == temp) {
                                    var optionn = $('<option />').val(value.Sno).text(value.Name);
                                    $(q).append(optionn);
                                }
                            });
                            $(q).val(temp);
                        }
                    });
                    $(this).prevAll(".dropdown").each(function(t, q) {
                        if ($(q).attr('id') != elementName) {
                            var temp = $(q).val();
                            $(q).empty();
                            $.each(json.TAs, function(key, value) {
                                if ($.inArray(json.TAs[key].Sno, arr) == -1 || json.TAs[key].Sno == temp) {
                                    var optionn = $('<option />').val(value.Sno).text(value.Name);
                                    $(q).append(optionn);
                                }
                            });
                            $(q).val(temp);
                        }
                    });
                });
            });
        });
    </script>
</head>

<body>
    <div>
        <div class="backLink">
            <a href="index.php">Go Back</a>
            <br>
            <br>
        </div>
        <form action="generate_edit_selection.php" method="post">

            <?php
            include 'dbconnect.php';

            $pref = 1;
            $rowdata = array();

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $target_dir = "";
                $target_file = $target_dir . basename($_FILES["selectionFile"]["name"]);

                $fileType = pathinfo($target_file, PATHINFO_EXTENSION);

                if ($fileType != "csv") {
                    die("Error: Please upload only CSV File.");
                }

                if (move_uploaded_file($_FILES["selectionFile"]["tmp_name"], $target_file)) {

                    $fileExists = 0;
                    if (!(file_exists($target_file))) {
                        die("Error: File do not exists.");
                    }

                    $file = fopen($target_file, "r");
                    $i = 0;
                    $skip = 0; // initialize $skip with 0 for skipping first row, with 1 otherwise

                    while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                        if ($skip != 0) {
                            $num = count($data);
                            for ($c = 0; $c < $num; $c++) {
                                $rowdata[$i][] = mysqli_real_escape_string($conn, $data[$c]);
                            }
                            $sql = "INSERT INTO tsubject (sno, name) VALUES ( NULL, '$data[1]');";
                            $result = mysqli_query($conn, $sql);
                            if (empty($result)) {
                                $ctsql = "CREATE TABLE tsubject (sno INT(20) AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL);";
                                $ctresult = mysqli_query($conn, $ctsql);
                                if ($ctresult == false) {
                                    die("Error creating table: " . mysqli_error($conn));
                                }
                                $sql = "INSERT INTO tsubject (sno, name) VALUES ( NULL, '$data[1]');";
                                $result = mysqli_query($conn, $sql);
                            }
                            if ($result == false) {
                                die("Error: " . $sql . "<br>" . mysqli_error($conn));
                            }
                            $i++;
                        }
                        $skip += 1;
                    }

                    fclose($file);

                    $pref = count($rowdata[0]) - 2;

                    $newtargetfile = $target_file;

                    if (file_exists($newtargetfile)) {
                        unlink($newtargetfile);
                    }

                    // exit();
                }
            }

            $tempdata = array();

            $sql = "SELECT * FROM tta ORDER BY name";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $name = $row['name'];
                $sno = $row['sno'];
                if (!isset($tempdata[$name]))
                    $tempdata[$name] = array();
                array_push($tempdata[$name], $sno);
            }

            $j = 1;
            $store = array();

            $selectedTAs = array();

            foreach ($rowdata as $row) {
                for ($i = 2; $i < $pref + 2; $i += 1) {
                    $name = $row[$i];
                    if ($name == "None") {
                        $store[$j] = -1;
                        array_push($selectedTAs, $store[$j]);
                    } else {
                        $k = count($tempdata[$name]);
                        if ($k > 0) {
                            $store[$j] = $tempdata[$name][$k - 1];
                            array_push($selectedTAs, $store[$j]);
                            unset($tempdata[$name][$k - 1]);
                        } else {
                            die("Error: Invalid data.");
                        }
                    }
                    $j += 1;
                }
            }

            echo "<hr>";
            echo "<span class='fcol'>" . "S.No." . "</span>";
            echo "<span class='scol'>" . "Subject" . "</span>";
            $j = 1;
            while ($j <= $pref) {
                echo "<span class='tcol'>" . "TA $j" . "</span>";
                if ($j != $pref)
                    echo "<span class='vl'></span>";
                $j += 1;
            }
            echo "<br><hr>";

            $i = 1;
            foreach ($rowdata as $row) {
                echo "<span class='fcol'>" . $row[0] . ".</span>";
                echo "<span class='scol'>" . $row[1] . "</span>";
                $j = 1;
                while ($j <= $pref) {
                    echo "<select id='dd$i' name='dd$i' class='dropdown'>";
                    $sql = "SELECT * FROM tta ORDER BY name";
                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $name = $row['name'];
                        $sno = $row['sno'];
                        if (!in_array($sno, $selectedTAs)) {
                            echo "<option value='$sno'>$name</option>";
                        } else if ($sno == $store[$i]) {
                            echo "<option value='$sno' selected>$name</option>";
                        }
                    }

                    $name = "None";
                    $sno = "-1";
                    if ($sno == $store[$i]) {
                        echo "<option value='$sno' selected>$name</option>";
                    } else {
                        echo "<option value='$sno'>$name</option>";
                    }

                    echo "</select>";
                    if ($j != $pref)
                        echo "<span class='vl'></span>";
                    $j += 1;
                    $i += 1;
                }
                echo "<br><hr>";
            }

            mysqli_close($conn);
            ?>

            <br>
            <br>
            <input type="hidden" id="pref" name="pref" value="<?php echo $pref; ?>">
            <div class="submitButton">
                <input type="submit" value="Submit">
            </div>
        </form>
    </div>
</body>

</html>