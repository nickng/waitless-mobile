<?php
    /**
     * Display results.
     *
     * url: /?postcode=(string)
     */
    if (isset($_POST['postcode'])):
        $req_postcode = urlencode($_POST['postcode']);
        $json_string  = "http://waitless.herokuapp.com/hospitals/by/distance.json?postcode={$req_postcode}";
        $ch = curl_init($json_string);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => array('Content-type: application/json'),
            CURL_POSTFIELDS        => $json_string
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        $objs = json_decode($result);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>NHS Waitless</title>
        <link rel='stylesheet' href='css/bootstrap.min.css'/>
        <link rel='stylesheet' href='css/app.css'/>
    </head>
    <body>
        <div class="container">
            <h3>Waiting times near <?= strtoupper($_POST['postcode']) ?></h3>
            <ol>
<?php
        foreach ($objs as $key => $clinic) {
            $encoded_odscode  = urlencode($clinic->odscode);
            $encoded_postcode = urlencode($clinic->postcode);
            $waittime_hours = intval($clinic->delay / 60);
            $waittime_mins  = $clinic->delay % 60;
            echo "<li><a href='{$clinic->url}' title='{$clinic->odscode}'>{$clinic->name}</a>, {$clinic->postcode} ";
            echo "<a href='{$_SERVER['PHP_SELF']}?details&amp;postcode={$clinic->postcode}&amp;odscode={$encoded_odscode}'>Details</a> ";
            echo "<a href='http://maps.google.com/maps?q={$clinic->location[0]},{$clinic->location[1]}'>Map</a><br/>";

            echo "Wait time <span title='{$clinic->delay} Mins' class='";

            // Different colour for wait times
            if ($clinic->delay <= 10) {
                echo "fast";
            } elseif ($clinic->delay <= 20) {
                echo "normal";
            } else {
                echo "slow";
            }

            echo "'>{$waittime_hours} Hours {$waittime_mins} Mins</span></li>";
            if ($key >= 2) break;
        }
?>
            </ol>
            <div class="well well-small" style="background: #f00; color: #fff; font-weight: bold">
            If you are in need of serious medical attention, please call 999 or got directly to your nearest emergency department
            </div>
        </div>
    </body>
</html>
<?php
    /**
     * Display clinic details.
     *
     * url: /?details&odscode=(str)
     */
    elseif (isset($_GET['details']) && isset($_GET['odscode'])):
        $encoded_odscode = urlencode($_GET['odscode']);
        $json_string  = "http://waitlist.herokuapp.com/hospitals/by/distance.json?odscode={$encoded_odscode}";
        $ch = curl_init($json_string);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => array('Content-type: application/json'),
            CURL_POSTFIELDS        => $json_string
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        $objs = json_decode($result);
        foreach ($objs as $obj) {
            if ($obj->odscode == $_GET['odscode']) {
                $clinic = $obj;
            }
        }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>NHS Waitless</title>
        <link rel='stylesheet' href='css/bootstrap.min.css'/>
    </head>
    <body>
        <div class="container">
            <h3><?= $clinic->name ?> <small>odscode: <?= strtoupper($clinic->odscode) ?></small></h3>
            <ul>
                <li>Address: <?= $clinic->postcode ?> (<a href='http://maps.google.com/maps?q=<?= $clinic->location[0] ?>,<?= $clinic->location[1] ?>'>Map</a>)</li>
                <li>Services available:
                    <ul>
                        <li>[dummy]</li>
                        <li>[dummy]</li>
                        <li>[dummy]</li>
                        <li>[dummy]</li>
                    </ul>
                </li>
                <li>Phone: <a href='tel:'020XXXXXXXX'>020XXXXXXXX</a></li>
                <li>Wait time: <?= intval($clinic->delay / 60) ?> Hours, <?= intval($clinic->delay % 60) ?> Mins (<?= $clinic->delay ?> Mins)</li>
            </ul>
            <script type="text/javascript" src="js/bootstrap.min.js"></script>
        </div>
    </body>
</html>
<?php
    /**
     * Display search form.
     *
     * url: /
     */
    else:
?>
<!DOCTYPE html>
<html>
    <head>
        <title>NHS Waitless</title>
        <link rel='stylesheet' href='css/bootstrap.min.css'/>
    </head>
    <body>
        <div class="container">
            <h3>NHS Waitless</h3>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                <div class="input-append">
                    <input class="input-medium" name="postcode" type="text" placeholder="Postcode, eg. E3"/>
                    <button class="btn">Find</button>
                </div>
            </form>
            <p> Are you sure you need to use A&amp;E services? </p>
            <script type="text/javascript" src="js/bootstrap.min.js"></script>
        </div>
    </body>
</html>
<?php
    endif;
?>
