<?php
date_default_timezone_set("Europe/Paris"); 
ini_set("default_charset","utf-8");
$tabcli=array('demo');
function GetVariableFrom ($from,$name,$default="") 
{
	if (!is_array($from)) $from=array();
	if (!isset($from[$name]))   $from[$name]=$default;
	elseif (($from[$name]=="") and ($default!="")) $from[$name]=$default;
    return $from[$name];
}
$action=GetVariableFrom($_POST,"action","new");
$smscli=GetVariableFrom($_POST,"smscli","demo");
$smspassw=GetVariableFrom($_POST,"smspassw","demopass");
$idsms=GetVariableFrom($_POST,"idsms","");

$from=GetVariableFrom($_POST,"from","the SIM Card phone Number");
$body=GetVariableFrom($_POST,"body","body");
$body=stripslashes(strip_tags($body));
$body=substr(str_replace(chr(160)," ",$body),0,8191);

$cclist=GetVariableFrom($_POST,"cclist","");

$html="<!DOCTYPE html><html><head><meta charset='utf-8'><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><title>Test API SMS</title>";
$dt=date("YmdHis");
$html.="<link rel='stylesheet' href='./smsapi.css?ts=".$dt."'>\n";
$html.="</head>\r\n";
$html.="<body>\r\n";
$html.="<div class='flex-zone'>\r\n";
	$html.="<div class='flex-item'>\r\n";
	$html.="<fieldset style='border-radius:10px;'><legend>Commandes</legend>\r\n";
	$html.="<form method='post' action='index.php'>\r\n";
	$html.="<table style='border:none;'>\r\n";
	$html.="<tr><td style='width:200px;'>smscli</td><td><select name='smscli' style='width:200px;'>";
	foreach ($tabcli as $cli)
	{
		$html.="<option value='".$cli."' ".($smscli==$cli?" selected":"").">".$cli."</option>";
	}
	$html.="</select></td>\r\n";


	$html.="<tr><td>Password</td><td><input type='text' name='smspassw' value=\"".$smspassw."\"></td></tr>\r\n";
	$html.="<tr><td>Action</td><td><select name='action'>";
			$html.="<option value='new' ".($action=="new"?" selected":"").">new</option>";
			$html.="<option value='putcc' ".($action=="putcc"?" selected":"").">putcc</option>";
			$html.="<option value='getquotat' ".($action=="getquotat"?" selected":"").">getquotat</option>";
			$html.="<option value='send' ".($action=="send"?" selected":"").">send</option>";
			$html.="<option value='directsend' ".($action=="directsend"?" selected":"").">directsend</option>";
	$html.="</select></td></tr>\r\n";
	$html.="<tr><td>idsms</td><td><input type=number name=idsms value=\"".$idsms."\"></td></tr>\r\n";


	$html.="<tr><td>from</td><td><input type='mail' name='from' value=\"".$from."\"></td></tr>\r\n";
	$html.="<tr><td>body</td><td><textarea name='body' rows='3' cols='45'>".$body."</textarea></td></tr>\r\n";
	$html.="<tr><td>cclist: liste séparée par des ;</td><td><input style='width:350px;' type=text name=cclist value=\"".$cclist."\"></td></tr>\r\n";
	$html.="<tr><td></td><td><input type='submit' value='tester'></td></tr>\r\n";
	$html.="</table></form>\r\n";
	$html.="</fieldset>\r\n";
	$html.="</div>\r\n";
	
	$html.="<div class='flex-item'>\r\n";
	$html.="<fieldset style='border-radius:10px;'><legend>Commandes</legend>\r\n";
	$html.="Les méthodes utilisables sont :";
	$html.="<ul><li>New : création d'un SMS</li>";
	$html.="<li>putcc : mise en place - remplacement de la liste des destinataires</li>";
	$html.="<li>getquotat : Récupération du quotat</li>";
	$html.="<li>send : Envoi du SMS</li>";
	$html.="<li>directsend : Création et envoi immédiat du SMS</li>";
	$html.="</ul><br>";
	$html.="Remarques :";
	$html.="<ul><li>New permet fournir toutes les données d'un coup (sauf la récupération du quotat restant)</li>";
	$html.="<li>putcc remplace les destinataires par une nouvelle liste (écrase la liste précédente)</li>";
	$html.="<li>getquotat : Fournis le quotat restat</li>";
	$html.="<li>putcc et send EXIGENT que le numéro de sms (idsms) soit renseigné</li>";
	$html.="<li>directsend EXIGENT toutes les données soient renseignées (sauf l'id du sms) qui sera créer durant le processus</li>";
	$html.="</ul><br>";
	$html.="</fieldset>\r\n";
	$html.="</div>\r\n";
$html.="</div>\r\n";
	
$html.="</body></html>";

echo($html);



?>