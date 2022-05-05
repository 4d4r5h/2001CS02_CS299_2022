<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Start Selection</title>
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

                $sql = "SELECT * FROM ta ORDER BY name";
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

            $.each(json.TAs, function(key, value) {
                var option = $('<option />').val(value.Sno).text(value.Name);
                $('.dropdown').append(option);
            });

            $(".dropdown").val("-1");

            // $(function() {
            //     $('.dropdown').change(function() {
            //         var selectedValue = $("option:selected", this).val();
            //         var elementName = ($(this).attr('id'));
            //         var arr = [];
            //         $(this).prevAll(".dropdown").each(function(i, t) {
            //             arr.push($("option:selected", t).val())
            //         });
            //         arr.push(selectedValue);
            //         $(this).nextAll(".dropdown").each(function(t, q) {
            //             if ($(q).attr('id') != elementName) {
            //                 $(q).empty();
            //                 $.each(json.TAs, function(key, value) {
            //                     if ($.inArray(json.TAs[key].Sno, arr) == -1 || json.TAs[key].Sno=="-1") {
            //                         var optionn = $('<option />').val(value.Sno).text(value.Name);
            //                         $(q).append(optionn);
            //                     }
            //                 });
            //                 $(q).val("-1");
            //             }
            //         });
            //     });
            // });

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
        <form action="generate_selection.php" method="post">

            <?php
            include 'dbconnect.php';

            $pref = 1;
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $pref = $_POST["pref"];
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
            $sql = "SELECT * FROM subject";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<span class='fcol'>" . $row['sno'] . ".</span>";
                echo "<span class='scol'>" . $row['name'] . "</span>";
                $j = 1;
                while ($j <= $pref) {
                    echo "<select id='dd$i' name='dd$i' class='dropdown'></select>";
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