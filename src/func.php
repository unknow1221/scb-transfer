<?php

require_once __DIR__ . '/scb/scb.php';
function alert($msg, $status, $path = '')
{
    if ($path != "") {
        $path = 'window.location = "' . $path . '"';
    }
    $xx = "Good job!";
    if ($status == "error") {
        $xx = "Oops...";
    }
    echo '<body></body><script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">Swal.fire({
  icon: "' . $status . '",
  title: "' . $xx . '",
  text: "' . $msg . '",
}).then((result) => {
  ' . $path . '
});</script>';
}
function isLogin()
{
    if (isset($_SESSION['login'])) {
        return true;
    } else {
        return false;
    }
}

function isAdmin()
{
    if (isset($_SESSION['admin'])) {
        return true;
    } else {
        return false;
    }
}
function ps($path)
{
    return "https://fasteasy.scbeasy.com:8888/portalserver/content/bbp/repositories/contentRepository/?path=" . $path;
}

if (isLogin()) {
    global  $config;
    $bank = new SCB();
    $bank->setAccountNumber($config['bank']['account_number']);
    $bank->setLogin($config['bank']['deviceId'],getAPIRefresh());
    $login = $bank->login();
}
function getAPIRefresh(){
    global  $config;
    $curl = curl_init();
    $data = json_encode(array (
        'deviceid' =>$config['bank']['deviceId'],
        'pin' => $config['bank']['pin'],
        'account_no' =>$config['bank']['account_number'],
    ));
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api-v1.banktopup.com/api/v1/scb/login",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>$data  ,
        CURLOPT_HTTPHEADER => array(
            "x-auth-license: ".$config['license'],
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $json = json_decode($response, true);
    if ($json['error']['code'] != 100) {
        alert("เกิดข้อผิดพลาด getAPIRefresh",'error',"");
        exit(var_dump($json));
    }

    return $json['result']['api-refresh'];
}
function setSessionTime($_timeSecond)
{
    if (!isset($_SESSION['ses_time_life'])) {
        $_SESSION['ses_time_life'] = time();
    }
    if (isset($_SESSION['ses_time_life']) && time() - $_SESSION['ses_time_life'] > $_timeSecond) {
        if (count($_SESSION) > 0) {
            foreach ($_SESSION as $key => $value) {
                unset($$key);
                unset($_SESSION[$key]);
            }
        }
    }
}