<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Generate Edit Selection</title>
    <style>
        table {
            border-collapse: collapse;
            margin: auto;
            overflow: hidden;
            box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 50px;
            height: 50%;
            width: 50%;
            font-family: Arial, Helvetica, sans-serif;
            font-size: medium;
            font-weight: bold;
        }

        .theadtr {
            background-color: #005cab;
            color: #ffffff;
            text-align: left;
            font-weight: bold;
        }

        .theadth {
            padding: 13px 13px;
            text-align: center;
        }

        .tbodyth {
            border: 1.5px solid black;
            padding: 13px 13px;
            text-align: center;
        }

        .tbodytd {
            border: 1.5px solid black;
            padding: 13px 13px;
            text-align: center;
        }

        div {
            overflow-x: auto;
            text-align: center;
            background-color: #f2f2f2;
            background-image: url('images/bg.jpg');
            background-attachment: fixed;
            background-size: cover;
            padding: 10px;
        }

        * {
            margin: 0px;
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
            background-color: #ff4000;
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
            background-color: #e63900;
        }
    </style>
</head>

<body>
    <?php
    include 'dbconnect.php';

    $pref = 1;
    $subcnt = 1;
    $tacnt = 1;
    $taname = array();
    $taid = array();
    $freeta = array();

    $sql = "SELECT * FROM tsubject";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $subcnt = mysqli_num_rows($result);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $pref = $_POST["pref"];
        $i = 1;
        while ($i <= ($pref * $subcnt)) {
            $ddname = "dd" . "$i";
            if (isset($_POST[$ddname]))
                array_push($taid, $_POST[$ddname]);
            else
                array_push($taid, -1);
            $i += 1;
        }
    }

    $len = count($taid);
    for ($i = 0; $i < $len; $i += 1) {
        if ($taid[$i] != -1) {
            for ($j = $i + 1; $j < $len; $j += 1) {
                if ($taid[$j] == $taid[$i]) {
                    $taid[$j] = -1;
                }
            }
        }
    }

    $i = 0;
    while ($i < ($pref * $subcnt)) {
        if ($taid[$i] != -1) {
            $getrowsql = "SELECT name FROM tta WHERE sno=" . $taid[$i];
            $newresult = mysqli_query($conn, $getrowsql);
            if ($newresult) {
                $rowdata = mysqli_fetch_row($newresult);
                array_push($taname, $rowdata[0]);
            }
        } else
            array_push($taname, "None");
        $i += 1;
    }

    $sql = "SELECT * FROM tta";
    if ($result = mysqli_query($conn, $sql)) {
        $tacnt = mysqli_num_rows($result);
    }

    $emails = "";
    $names = "";

    for ($i = 1; $i <= $tacnt; $i += 1) {
        if (in_array($i, $taid) == false) {
            $getrowsql = "SELECT name FROM tta WHERE sno=" . $i;
            $newresult = mysqli_query($conn, $getrowsql);
            if ($newresult) {
                $rowdata = mysqli_fetch_row($newresult);
                array_push($freeta, $rowdata[0]);
            }
        } else {
            $sql = "SELECT * FROM tta where sno=" . $i;
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['email'] != "NA") {
                    $emails .= $row['email'] . ", ";
                    $names .= $row['name'] . ", ";
                }
            }
        }
    }

    $ctsql = "CREATE TABLE Mayank (sno INT(20) AUTO_INCREMENT PRIMARY KEY, subject_name VARCHAR(100) NOT NULL";
    $i = 1;
    while ($i <= $pref) {
        $temp = "ta" . "$i";
        $ctsql .= "," . $temp . " VARCHAR(100) NOT NULL";
        $i += 1;
    }
    $ctsql .= ")";

    if (mysqli_query($conn, $ctsql) == false) {
        die("Error creating table: " . mysqli_error($conn));
    }

    $j = 0;
    $sql = "SELECT * from tsubject";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $name = $row['name'];
        $sql = "INSERT INTO Mayank (sno, subject_name";
        $i = 1;
        while ($i <= $pref) {
            $temp = "ta" . "$i";
            $sql .= ", " . $temp;
            $i += 1;
        }
        $sql .= ") VALUES ( NULL, '$name'";
        $last = $j;
        while ($j < ($pref * $subcnt)) {
            $sql .= ", " . "'$taname[$j]'";
            $j += 1;
            if ($j - $last == $pref)
                break;
        }
        $sql .= ");";
        $newresult = mysqli_query($conn, $sql);
        if ($newresult == false) {
            die("Error: " . $sql . "<br>" . mysqli_error($conn));
        }
    }

    mysqli_close($conn);
    ?>

    <div>
        <a href="index.php">Go Back</a>
        <br>
        <br>
        <form method='post' action='download_allocated.php'>
            <table>
                <thead>
                    <tr class="theadtr">
                        <th class="theadth" scope="col">S. No.</th>
                        <th class="theadth" scope="col">Subject</th>

                        <?php
                        include 'dbconnect.php';

                        $i = 1;
                        while ($i <= $pref) {
                            echo "<th class='theadth' scope='col'>TA $i</th>";
                            $i += 1;
                        }

                        echo "</tr> </thead> <tbody>";

                        $temp = array();
                        array_push($temp, "S. No.");
                        array_push($temp, "Subject");

                        $i = 1;
                        while ($i <= $pref) {
                            array_push($temp, "TA $i");
                            $i += 1;
                        }

                        $current_allocation[] = $temp;

                        $sql = "SELECT * FROM Mayank";
                        $result = mysqli_query($conn, $sql);

                        while ($row = mysqli_fetch_assoc($result)) {
                            $temp = array();
                            echo "<tr> <th class='tbodyth' scope='row'>" . $row['sno'] . "</th>
                        <th class='tbodyth' scope='row'>" . $row['subject_name'] . "</th>";
                            array_push($temp, $row['sno'], $row['subject_name']);
                            $i = 1;
                            while ($i <= $pref) {
                                echo "<td class='tbodytd'>" . $row['ta' . $i] . "</td>";
                                array_push($temp, $row['ta' . $i]);
                                $i += 1;
                            }
                            array_push($current_allocation, $temp);
                            echo "</tr>";
                        }

                        $unallocated_ta[] = array("S. No.", "Name");

                        $i = 1;
                        foreach ($freeta as $value) {
                            $temp = array($i, $value);
                            array_push($unallocated_ta, $temp);
                            $i += 1;
                        }

                        $sql = "DROP table Mayank, tta, tsubject;";
                        $result = mysqli_query($conn, $sql);
                        if ($result == false) {
                            die("Error dropping table: " . mysqli_error($conn));
                        }

                        echo "</tbody> </table>";

                        /*
                        echo "<br> Free TAs: ";

                        if (count($freeta) == 0)
                            echo "None";
                        else {
                            for ($i = 0; $i < count($freeta); $i += 1) {
                                echo $freeta[$i];
                                if ($i + 1 != count($freeta))
                                    echo ", ";
                            }
                        }
                        */

                        $serialize_current_allocation = serialize($current_allocation);
                        $serialize_unallocated_ta = serialize($unallocated_ta);

                        mysqli_close($conn);
                        ?>

                        <textarea name='export_data' style='display: none;'><?php echo $serialize_current_allocation; ?></textarea>
                        <br>
                        <br>
                        <input type='submit' value='Export Current Allocation' name='Export'>
        </form>
        <form method='post' action='download_unallocated.php'>
            <textarea name='export_data' style='display: none;'><?php echo $serialize_unallocated_ta; ?></textarea>
            <input type='submit' value='Export Unallocated TAs' name='Export'>
        </form>
        <form method='post' action='send_mail.php'>
            <textarea name='export_allocation' style='display: none;'><?php echo $serialize_current_allocation; ?></textarea>
            <textarea name='export_emails' style='display: none;'><?php echo $emails; ?></textarea>
            <textarea name='export_names' style='display: none;'><?php echo $names; ?></textarea>
            <input type='submit' value='Send Email' name='Export'>
        </form>
    </div>
</body>

</html>