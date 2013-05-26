<?php
    if (isset($_POST['postcode'])):
        $req_postcode = urlencode($_POST['postcode']);
        $json_string  = "http://e-mergency.herokuapp.com/hospitals/by/distance.json?from={$req_postcode}";
        $ch = curl_init($json_string);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => array('Content-type: application/json'),
            CURL_POSTFIELDS        => $json_string
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        echo $result;

        $obj = json_decode($result);
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
        <h3>NHS Waitless <small><?= $_POST['postcode'] ?></small></h3>
            <ol>
<?php
        foreach ($obj as $clinic) {
            $encoded_odscode  = urlencode($clinic->odscode);
            $encoded_postcode = urlencode($clinic->postcode);
            echo "<li><a href='{$clinic->url}' title='{$clinic->odscode}'>{$clinic->name}</a>, {$clinic->postcode} ";
            echo "<div class='btn-group'><a class='btn btn-link btn-mini' href='{$_SERVER['PHP_SELF']}?details#{$encoded_odscode}'>Details</a>";
            echo "<a class='btn btn-link btn-mini' href='http://maps.google.com/maps?q={$encoded_postcode}'>Map</a></div> ";

            echo "wait time <span class='";

            // Different colour for wait times
            if ($clinic->delay <= 10) {
                echo "fast";
            } elseif ($clinic->delay <= 20) {
                echo "normal";
            } else {
                echo "slow";
            }

            echo "'>{$clinic->delay} mins</span></li>";
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
            <form action="<?= $_SERVER['PHP_SELF'] ?>">
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
