<?php
function YasSendSMS($args, $to, $bodyId)
{
    //GitHub نمونه کدهای
    $username =  '09333023018';
    $password =  '41adbd72-8f88-448a-8765-2ca2664cab62';
    //بدون نیاز به پکیج گیت هاب Procedural PHP نمونه کدهای
    $data = array('username' => $username, 'password' => $password, 'text' => "$args;کاربر", 'to' => $to, "bodyId" => $bodyId);
    $post_data = http_build_query($data);
    $handle = curl_init('https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber');
    curl_setopt($handle, CURLOPT_HTTPHEADER, array(
        'content-type' => 'application/x-www-form-urlencoded'
    ));
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
    $response = curl_exec($handle);
    $response = json_decode($response);
    return $response;
}
