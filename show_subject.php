<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Show Subject</title>
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
    <div>
        <a href="add_subject.php">Go Back</a>
        <br>
        <br>
        <table>
            <thead>
                <tr class="theadtr">
                    <th class="theadth" scope="col">S. No.</th>
                    <th class="theadth" scope="col">Name</th>
                </tr>
            </thead>
            <tbody>
                <?php
                ob_start();
                include 'dbconnect.php';

                $sql = "SELECT * FROM subject";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
          <th class='tbodyth' scope='row'>" . $row['sno'] . ".</th>
          <td class='tbodytd'>" . $row['name'] . "</td>
          </tr>";
                }

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $sql = "DROP table subject;";
                    $result = mysqli_query($conn, $sql);
                    if ($result == false) {
                        die("Error dropping table: " . mysqli_error($conn));
                    } else {
                        header("Location: add_subject.php");
                        exit();
                    }
                }

                mysqli_close($conn);
                ?>
            </tbody>
        </table>
        <br>
        <form action="show_subject.php" method="POST">
            <input type="submit" value="Erase Complete Data">
        </form>
    </div>
</body>

</html>