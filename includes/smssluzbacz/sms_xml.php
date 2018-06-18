<?php
define('LOGIN','login');
define('PASSWORD','heslo');

require_once("./smsgateapi_sluzba_cz/apixml30.php");

$apixml = new ApiXml30(LOGIN, PASSWORD);

/* Odeslani SMS
*parametry:
*telefonni cislo - povinny
*text zpravy - povinny
*odeslat v - nepovinny , format je YmdHis 20090510122343   viz dokumentace API
*dorucenka - nepovinny, 20 = vyzadnovani dorucenky, 0 = nevyzadovani dorucenky

vraci XML s vysledkem odeslani SMS
*/
$res = $apixml->send_message("777123456","Text nejake zpravy ktery se bude odesilat", null,20);
echo $res;
echo "\n\n";


$what=array("query_incoming"=>1,"query_outgoing"=>1,"query_delivery_report"=>1, "count"=>30 );

$res = $apixml->get_incoming_messages($what);
echo $res;
echo "\n\n";


$what=array("type"=>"outgoing_message","id"=>"420777111222-20090213121534637");

$res = $apixml->confirm_message($what);
echo $res;
echo "\n\n";


?>
