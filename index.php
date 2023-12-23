<?php
date_default_timezone_set("Europe/Paris"); 
ini_set("default_charset","utf-8");
DEFINE("TMPDIR","./tmp/");
DEFINE("GAMMU_CONFIG_FILE","/root/.gammurc");
#######################################################
# API DISTANTE D'ENVOI DE SMS                         #
# METHODE : POST                                      #
# VARIABLES :                                         #
# action = new/putcc/send/getquotat/directsend        #
# si putcc/send/getquotat alors :                     #
#              idsms doit contenir l'id du sms        #
#######################################################


function log_event($mess="")
{
    $logfilename="./log/logsms".date("Ym").".txt";
	$dt=date('Y-m-d H:i:s'); 
	$osclient=operating_system_detection();
	$browerclient=GetBrowserVersion();
    $s = $dt."\t".$mess."\t".$osclient."\t".$browerclient."\n";
    @$fp = fopen ($logfilename, "a");
    if ($fp)
    {
        @fwrite ($fp,$s);
        @fclose($fp);    
    }
}


/*obtenir le browser*/
function GetBrowserVersion()
{
	$result="";
	if (!isset($_SERVER["HTTP_USER_AGENT"])) $_SERVER["HTTP_USER_AGENT"]="";
	$opera = stripos($_SERVER["HTTP_USER_AGENT"], 'Opera') ? true : false;
	if (!$opera) $opera = stripos($_SERVER["HTTP_USER_AGENT"], 'OPR') ? true : false;
	$edge = stripos($_SERVER["HTTP_USER_AGENT"], 'Edge') ? true : false;
	$chrome = stripos($_SERVER["HTTP_USER_AGENT"], 'Chrome') ? true : false;	
	$safari = stripos($_SERVER["HTTP_USER_AGENT"], 'Safari') ? true : false;
	$firefox = stripos($_SERVER["HTTP_USER_AGENT"], 'Firefox') ? true : false;
	$msie = stripos($_SERVER["HTTP_USER_AGENT"], 'MSIE') ? true : false;
	$trident = stripos($_SERVER["HTTP_USER_AGENT"], 'Trident') ? true : false;
	
	if ($opera==TRUE) $result="opera";
	elseif ($edge==TRUE) $result="edge";
	elseif ($chrome==TRUE) $result="chrome";
	elseif ($safari==TRUE) $result="safari";
	elseif ($firefox==TRUE) $result="firefox";
	elseif ($msie==TRUE) $result="msie";
	elseif ($trident==TRUE) $result="trident";
	
	if ($msie==TRUE)
	{
		if (stripos($_SERVER["HTTP_USER_AGENT"],"MSIE 6")) $result="ie-6";
		elseif (stripos($_SERVER["HTTP_USER_AGENT"],"MSIE 7")) $result="ie-7";
		elseif (stripos($_SERVER["HTTP_USER_AGENT"],"MSIE 8")) $result="ie-8";
		elseif (stripos($_SERVER["HTTP_USER_AGENT"],"MSIE 9")) $result="ie-9";
		elseif (stripos($_SERVER["HTTP_USER_AGENT"],"MSIE 10")) $result="ie-10";
		else $result="ie";
	}
	if (stripos($_SERVER["HTTP_USER_AGENT"],"Trident/7.0; rv11.0")) $result="ie-11";	
	if ($result=="") $result=$_SERVER["HTTP_USER_AGENT"];
	else
	{
		$search=$result;
		if (($result=="opera") and (stripos($_SERVER["HTTP_USER_AGENT"], 'OPR')!==FALSE)) $search="OPR";
		$i=stripos($_SERVER["HTTP_USER_AGENT"],$search."/");
		$s=substr($_SERVER["HTTP_USER_AGENT"],$i+strlen($search)+1);
		$n=stripos($s," ");
		if ($n) $s=substr($s,0,$n);
		$n=stripos($s,".");
		if ($n) $s=substr($s,0,$n);
		if (is_numeric($s)) $result.="-".$s;
	}
	return $result;
}	

/* return Operating System */
function operating_system_detection()
{
    $agent = GetVariableFrom($_SERVER,"HTTP_USER_AGENT","");

    $ros[] = array('Windows XP', 'Windows XP');
    $ros[] = array('Windows NT 5.1|Windows NT5.1)', 'Windows XP');
	$ros[] = array('Windows NT 5.1;', 'Windows XP');
    $ros[] = array('Windows 2000', 'Windows 2000');
    $ros[] = array('Windows NT 5.0', 'Windows 2000');
    $ros[] = array('Windows NT 4.0|WinNT4.0', 'Windows NT');
    $ros[] = array('Windows NT 5.2', 'Windows Server 2003');
    $ros[] = array('Windows NT 6.0', 'Windows Vista');
    $ros[] = array('Windows NT 6.1', 'Windows 7');
	$ros[] = array('Windows NT 6.2', 'Windows 8');	
	$ros[] = array('Windows NT 10.0', 'Windows 10');
    $ros[] = array('Windows CE', 'Windows CE');
	$ros[] = array('Windows Phone OS', 'Windows Phone');
    $ros[] = array('(media center pc).([0-9]{1,2}\.[0-9]{1,2})', 'Windows Media Center');
    $ros[] = array('(win)([0-9]{1,2}\.[0-9x]{1,2})', 'Windows');
    $ros[] = array('(win)([0-9]{2})', 'Windows');
    $ros[] = array('(windows)([0-9x]{2})', 'Windows');
    // Doesn't seem like these are necessary...not totally sure though..
    //$ros[] = array('(winnt)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'Windows NT');
    //$ros[] = array('(windows nt)(([0-9]{1,2}\.[0-9]{1,2}){0,1})', 'Windows NT'); // fix by bg
    $ros[] = array('Windows ME', 'Windows ME');
    $ros[] = array('Win 9x 4.90', 'Windows ME');
    $ros[] = array('Windows 98|Win98', 'Windows 98');
    $ros[] = array('Windows 95', 'Windows 95');
    $ros[] = array('(windows)([0-9]{1,2}\.[0-9]{1,2})', 'Windows');
    $ros[] = array('win32', 'Windows');
    $ros[] = array('(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})', 'Java');
    $ros[] = array('(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}', 'Solaris');
    $ros[] = array('dos x86', 'DOS');
    $ros[] = array('unix', 'Unix');
	$ros[] = array('iPhone OS', 'iPhone OS');
	$ros[] = array('Macintosh; Intel Mac OS	X', 'Mac OS X');
    $ros[] = array('Mac OS X', 'Mac OS X');
    $ros[] = array('Mac_PowerPC', 'Macintosh PowerPC');
    $ros[] = array('(mac|Macintosh)', 'Mac OS');
	$ros[] = array('Darwin', 'Mac OS');
    $ros[] = array('(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'SunOS');
    $ros[] = array('(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'BeOS');
    $ros[] = array('(risc os)([0-9]{1,2}\.[0-9]{1,2})', 'RISC OS');
    $ros[] = array('os/2', 'OS/2');
    $ros[] = array('freebsd', 'FreeBSD');
    $ros[] = array('openbsd', 'OpenBSD');
    $ros[] = array('netbsd', 'NetBSD');
    $ros[] = array('irix', 'IRIX');
    $ros[] = array('plan9', 'Plan9');
    $ros[] = array('osf', 'OSF');
    $ros[] = array('aix', 'AIX');
    $ros[] = array('GNU Hurd', 'GNU Hurd');
    $ros[] = array('(fedora)', 'Linux - Fedora');
    $ros[] = array('(kubuntu)', 'Linux - Kubuntu');
    $ros[] = array('(ubuntu)', 'Linux - Ubuntu');
    $ros[] = array('; Ubuntu; Linux', 'Linux - Ubuntu');
    $ros[] = array('(debian)', 'Linux - Debian');
    $ros[] = array('(CentOS)', 'Linux - CentOS');
    $ros[] = array('(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)', 'Linux - Mandriva');
    $ros[] = array('(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)', 'Linux - SUSE');
    $ros[] = array('(Dropline)', 'Linux - Slackware (Dropline GNOME)');
    $ros[] = array('(ASPLinux)', 'Linux - ASPLinux');
    $ros[] = array('(Red Hat)', 'Linux - Red Hat');
	$ros[] = array('; Linux x86_64;', 'Linux - x86_64');
	$ros[] = array('; Linux x86;', 'Linux - x86');
	for ($i=20;$i>8;$i--) 
		{ $ros[] = array('Linux; Android '.$i,'Android '.$i); }
	$ros[] = array('Linux; Android','Android');
	$ros[] = array('Android ','Android');
    // Loads of Linux machines will be detected as unix.
    // Actually, all of the linux machines I've checked have the 'X11' in the User Agent.
    //$ros[] = array('X11', 'Unix');
    $ros[] = array('(linux)', 'Linux');
    $ros[] = array('(amigaos)([0-9]{1,2}\.[0-9]{1,2})', 'AmigaOS');
    $ros[] = array('amiga-aweb', 'AmigaOS');
    $ros[] = array('amiga', 'Amiga');
    $ros[] = array('AvantGo', 'PalmOS');
    //$ros[] = array('(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1}-([0-9]{1,2}) i([0-9]{1})86){1}', 'Linux');
    //$ros[] = array('(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1} i([0-9]{1}86)){1}', 'Linux');
    //$ros[] = array('(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1})', 'Linux');
    $ros[] = array('[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})', 'Linux');
    $ros[] = array('(webtv)/([0-9]{1,2}\.[0-9]{1,2})', 'WebTV');
    $ros[] = array('Dreamcast', 'Dreamcast OS');
    $ros[] = array('GetRight', 'Windows');
    $ros[] = array('go!zilla', 'Windows');
    $ros[] = array('gozilla', 'Windows');
    $ros[] = array('gulliver', 'Windows');
    $ros[] = array('ia archiver', 'Windows');
    $ros[] = array('NetPositive', 'Windows');
    $ros[] = array('mass downloader', 'Windows');
    $ros[] = array('microsoft', 'Windows');
    $ros[] = array('offline explorer', 'Windows');
    $ros[] = array('teleport', 'Windows');
    $ros[] = array('web downloader', 'Windows');
    $ros[] = array('webcapture', 'Windows');
    $ros[] = array('webcollage', 'Windows');
    $ros[] = array('webcopier', 'Windows');
    $ros[] = array('webstripper', 'Windows');
    $ros[] = array('webzip', 'Windows');
    $ros[] = array('wget', 'Windows');
    $ros[] = array('Java', 'Unknown');
    $ros[] = array('flashget', 'Windows');
    // delete next line if the script show not the right OS
    //$ros[] = array('(PHP)/([0-9]{1,2}.[0-9]{1,2})', 'PHP');
    $ros[] = array('MS FrontPage', 'Windows');
    $ros[] = array('(msproxy)/([0-9]{1,2}.[0-9]{1,2})', 'Windows');
    $ros[] = array('(msie)([0-9]{1,2}.[0-9]{1,2})', 'Windows');
    $ros[] = array('libwww-perl', 'Unix');
    $ros[] = array('UP.Browser', 'Windows CE');
    $ros[] = array('NetAnts', 'Windows');
    $file = count ( $ros );
    $os = '';
    for ( $n=0 ; $n<$file ; $n++ )
	{
		if ((isset($ros[$n][0])) and (isset($ros[$n][1])))
		{
			if(stripos($agent, $ros[$n][0]) !== false)
			{
				$os = @$ros[$n][1].' '.@$ros[1][$n];
				break;
			}
		}
    }
    return trim ( $os );
}

function encode_to_utf8($string) 
{
     $str=mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	 return $str;
}

function GetVariableFrom ($from,$name,$default="") 
{
	if (!is_array($from)) $from=array();
	if (!isset($from[$name]))   $from[$name]=$default;
	elseif (($from[$name]=="") and ($default!="")) $from[$name]=$default;
    return $from[$name];
}


//Check : pas de numéraux hors France, le +33 est toléré

function filterlist($string) 
{
   $tb=explode(";",$string);
   if (count($tb)==0) return False;
   $r=array();
   for ($i=0;$i<count($tb);$i++)
   {
	   //Retirer le +33 si présent
	   if (substr($tb[$i],0,3)=="+33") 
	   {
		   $tb[$i]=substr($tb[$i],3);
		   //s'assurer que le numéro commence bien par 0
		   if (substr($tb[$i],0,1)!="0") $tb[$i]="0".$tb[$i];
	   }
	   $tb[$i]=str_replace(array(".","+","-"),array("","",""),$tb[$i]);
	   //Retirer les caractères génant
	   $tb[$i]=filter_var($tb[$i], FILTER_SANITIZE_NUMBER_INT);
	   //vérifier qu'il reste 10 caractères
	   if (strlen(strval($tb[$i]))==10) $r[]=$tb[$i];
   }
   return $r;
}

function SqlString($String,$Upper)
{
	$String =str_replace(chr(160),chr(32),$String);
    $String=trim($String);
	if ($String=="") return "null";
    $String = strip_tags ($String);
    $String = html_entity_decode($String,ENT_QUOTES);
	$String = str_replace("\"", "'", $String);
	$String = str_replace("\"", "'", $String);
	$String = str_replace("\'", "'", $String);
	$String = str_replace("&#8216;", "'", $String);
    $String = str_replace("&#8217;", "'", $String);
	$String = str_replace("\\\\", "\\", $String);
    $String = str_replace("''", "'", $String);
    $String = str_replace("'", "''", $String);
    $String = rtrim($String);
	If ($Upper==TRUE)
			$String=strtoupper($String);
    return "'".$String."'";
}

function SqlInteger($String,$nullable)
{
	if ((strval($String)=="") and ($nullable==TRUE))
		$resultat="null";
	elseif ((strval($String)=="") and ($nullable==FALSE))
		$resultat="0";
	Else
		$resultat=$String;
    return $resultat;
}

function SqlBool($String,$nullable)
{
	if ((strval($String)=="") and ($nullable==TRUE))
		$resultat="null";
	elseif ((strtoupper(strval($String))!="Y") and (strval($String)!="1") and ($nullable==FALSE))
		$resultat="FALSE";
	Else
		$resultat="TRUE";
    return $resultat;
}


/*********************************************************************/
class db
{
	var $CR, $MSG, $db;
	function __construct($dbfile)
	{
		$this->CR="0";
		$this->MSG="";
		$ok=FALSE;
		foreach(PDO::getAvailableDrivers() as $driver) 
		{
			 $ok=($ok or (strtoupper($driver)=="SQLITE"));
		}  
		if (!$ok)
	    {
		   $this->CR="-1";
  		   $this->MSG="Extension PDO-SQLITE manquante";
		   return;
		}		
		try
		{
			$this->db = new PDO("sqlite:".$dbfile);
			$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES , FALSE);
		}
		catch (Exception $e)
		{
			$this->CR="-1";
			$this->MSG=$e->getMessage();
			return;
		}
		/*activer les foreign keys (regles de cascades)*/
		$this->db->exec("PRAGMA foreign_keys = on;");
	}
	
	function Close()
	{
		if ($this->db) 
		{
			$inttrans=$this->db->inTransaction();
			if ($inttrans==1) $this->db->commit();
			$this->db=NULL;
		}
	}

	function execute_query($stmt)
	{
		$err="";
		try { $st = $this->db->prepare($stmt);		}
		catch(PDOException $e)  {  $err = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();}	
		if ($err=="")
		{
			try {	$st->execute();   }
			catch(PDOException $e)  
			{
				$err = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage(); 
				return $err;
			}	
		}
		if ($err!="") return $err;
		return $st;
	}
	

	function check_cli($cli,$passw)
	{
		$result=new stdclass();
		$result->CR="1";
		$result->MSG="";
		$result->DATA="";
		$sql="SELECT IDSMSCLI, SMSCLI, COALESCE(PASSW,'') AS PASSW  FROM TBSMSCLI WHERE UPPER(SMSCLI) = ".SqlString($cli,TRUE);
		$sth = $this->execute_query($sql);
		if (is_string($sth))
		{
			$result->CR="-1";
			$result->MSG=$sql."  ".$sth;
			return $result;
		}
		$data=array();
		while ($row = $sth->fetchObject())
		{
			$data[]=$row;
		}
		$sth->closeCursor(); 
		$sth=NULL;

		if (count($data)!=1)
			{
				$result->CR="-1";
				$result->MSG="Client unknown";
			}			
		elseif ($data[0]->PASSW!=$passw)
			{
				$result->CR="-3";
				$result->MSG="Incorrect password or username";
			}
		else 
			{
				$result->CR="0";
				$result->DATA=$data[0]->IDSMSCLI;
			}
		return $result;
	}
	
	function get_quotatleft($smscli="demo")
	{
		$result=new stdclass();
		$result->CR="0";
		$result->MSG="";
		$result->DATA=0;
		$sql="SELECT QUOTAT FROM VQUOTATLEFT WHERE UPPER(SMSCLI)=".SqlString($smscli,TRUE).";";
		$sth = $this->execute_query($sql);
		if (is_string($sth))
		{
			$result->CR="-1";
			$result->MSG=$sql."  ".$sth;
			return $result;
		}
		if ($row = $sth->fetchObject()) $result->DATA=intval($row->QUOTAT);
		$sth->closeCursor(); 
		$sth=NULL;
		return $result;
	}

	//Un SMS mais multiple destinataire
	//Savoir si tous ont été envoyé
	function get_sms_status($idsms)
	{
		$result=new stdclass();
		$result->CR="0";
		$result->MSG="";
		$result->SENT="";
		$sql="SELECT SUM(CASE WHEN COALESCE(B.SEND,FALSE)=FALSE THEN 0 ELSE 1 END) AS SENT, COUNT(*) AS TOSEND FROM TBSMS AS A LEFT JOIN TBSMSDEST AS B ON A.IDSMS=B.IDSMS ";
		$sql.="WHERE A.IDSMS=".SqlInteger($idsms,FALSE).";";
		$sth = $this->execute_query($sql);
		if (is_string($sth))
		{
			$result->CR="-1";
			$result->MSG=$sql."  ".$sth;
			return $result;
		}
		if ($row = $sth->fetchObject()) $result->SENT=(((intval($row->SENT)==intval($row->TOSEND)) and (intval($row->SENT)>0))?"Y":"N");
		$sth->closeCursor(); 
		$sth=NULL;
		return $result;
	}

	function create_sms($sms_obj)
	{
		$result=new stdclass();
		$result->CR="0";
		$result->MSG="";
		$result->DATA="";
		$sms_obj->createts=date("Y-m-d H:i:s");
		$sql="INSERT INTO TBSMS (IDSMSCLI,SENDER,BODY,CREATETS) VALUES ( ";
		$sql.=SqlInteger($sms_obj->idsmscli,FALSE).",";
		$sql.=SqlString($sms_obj->from,FALSE).",";
		$sql.=SqlString($sms_obj->body,FALSE).",";
		$sql.=SqlString($sms_obj->createts,FALSE)." ";
		$sql.=");";
		
		$sth = $this->execute_query($sql);
		if (is_string($sth))
		{
			$result->CR="-1";
			$result->MSG=$sql."  ".$sth;
			return $result;
		}
		$sth->closeCursor(); 
		$sth=NULL;
		
		$sql="SELECT MAX(IDSMS) AS IDSMS FROM TBSMS;";
		$sth = $this->execute_query($sql);
		if (is_string($sth))
		{
			$result->CR="-2";
			$result->MSG=$sql."  ".$sth;
			//Pas réussi alors je supprime au cas où
			$this->delete_sms($sms_obj->createts);
			return $result;
		}
		if ($row = $sth->fetchObject()) $result->DATA=$row->IDSMS;
		$sth->closeCursor(); 
		$sth=NULL;
		$result->MSG="Création réussie";
		return $result;
	}
	
	function delete_sms($createts)
	{
		$result=new stdclass();
		$result->CR="0";
		$result->MSG="";
		$result->DATA="";
		$sql="DELETE FROM TBSMS WHERE SEND=FALSE AND CREATETS=".SqlString($createts,FALSE);
		$sth = $this->execute_query($sql);
		if (is_string($sth))
		{
			$result->CR="-2";
			$result->MSG=$sql."  ".$sth;
			return $result;
		}
		$sth->closeCursor(); 
		$sth=NULL;
		return $result;
	}


	function remove_dest($idsms)
	{
		$result=new stdclass();
		$result->CR="0";
		$result->MSG="";
		$result->DATA="";
		$sql="DELETE FROM TBSMSDEST WHERE IDSMS=".SqlInteger($idsms,FALSE).";";
		$sth = $this->execute_query($sql);
		if (is_string($sth))
		{
			$result->CR="-2";
			$result->MSG=$sql."  ".$sth;
			return $result;
		}
		$sth->closeCursor(); 
		$sth=NULL;
		return $result;
	}

	function put_dest($sms_obj)
	{
		$result=new stdclass();
		$result->CR="0";
		$result->MSG="";
		$result->DATA="";
		$result=$this->remove_dest($sms_obj->idsms);
		if ($result->CR!="0") return $result;
		
		$tbdest=$sms_obj->cctb;
		$sql="";		
		$sql.="INSERT INTO TBSMSDEST (IDSMS,DEST) VALUES ";
		for($i=0;$i<count($tbdest);$i++)
		{
			 $sql.="( ".SqlString($sms_obj->idsms,FALSE).",".SqlString($tbdest[$i],FALSE).") ";
			 if ($i<count($tbdest)-1) $sql.=", ";
			 else $sql.=";";
		}
		$sth = $this->execute_query($sql);
		if (is_string($sth))
		{
			$result->CR="-2";
			$result->MSG=$sql."  ".$sth;
			return $result;
		}
		$sth->closeCursor(); 
		$sth=NULL;
		return $result;
	}

	//Les destinaitaires (pour qui le SMS n'a pas encore été envoyé)
	function get_dest_sms($idsms)
	{
		$result=new stdclass();
		$result->CR="0";
		$result->MSG="";
		$result->CCLIST=array();
		$sql="SELECT DEST FROM TBSMSDEST WHERE IDSMS=".SqlInteger($idsms,FALSE)." AND SEND=0";
		$sth = $this->execute_query($sql);
		if (is_string($sth))
		{
			$result->CR="-2";
			$result->MSG=$sql."  ".$sth;
			return $result;
		}
		while ($row = $sth->fetchObject()) 
		{
			if ($row->DEST!="") $result->CCLIST[]=$row->DEST;
		}
		$sth->closeCursor(); 
		$sth=NULL;
		return $result;
	}

	function get_sms_data($idsms)
	{
		$result=new stdclass();
		$result->CR="0";
		$result->MSG="";
		$sql= "SELECT A.IDSMS, A.IDSMSCLI, B.SMSCLI, A.SENDER, A.BODY, A.CREATETS FROM TBSMS AS A ";
		$sql.="INNER JOIN TBSMSCLI AS B ON A.IDSMSCLI = B.IDSMSCLI ";
		if ($idsms!="") $sql.="WHERE A.IDSMS=".SqlInteger($idsms,FALSE);
		$sth = $this->execute_query($sql);
		if (is_string($sth))
		{
			$result->CR="-2";
			$result->MSG=$sql."  ".$sth;
			return $result;
		}
		if ($row = $sth->fetchObject()) 
		{
			$result=$row;
			$result->CR="0";
			$result->MSG="";
		}
		$sth->closeCursor(); 
		$sth=NULL;
		return $result;
	}

		
	function update_sms_dest($idsms,$dest="")
	{
		$result=new stdclass();
		$result->CR="0";
		$result->MSG="";
		$result->DATA="";
		$sql="UPDATE TBSMSDEST SET SEND=1, SENDTS=CURRENT_TIMESTAMP WHERE IDSMS=".SqlInteger($idsms,FALSE);
		if ($dest!="") $sql.=" AND DEST=".SqlString($dest,FALSE);
		$sth = $this->execute_query($sql);
		if (is_string($sth))
		{
			$result->CR="-2";
			$result->MSG=$sql."  ".$sth;
			return $result;
		}
		$sth->closeCursor(); 
		$sth=NULL;
		$result->DATA=$idsms;
		return $result;
	}
	

}
class command
{
	var $smspassw, $smscli, $from, $body, $action, $idsms, $cclist, $cctb, $idsmscli, $createts;

	function __construct()
	{
		$this->smspassw=GetVariableFrom($_POST,"smspassw","");
		$this->smscli=GetVariableFrom($_POST,"smscli","");
		$this->from=GetVariableFrom($_POST,"from","");
		$this->body=encode_to_utf8(GetVariableFrom($_POST,"body",""));
		$this->action=GetVariableFrom($_POST,"action","");
		$this->idsms=GetVariableFrom($_POST,"idsms","");
		$this->cclist=GetVariableFrom($_POST,"cclist","");
		$this->idsmscli=array();
		$this->createts=date("Y-m-d H:i:s");
	}

	function checkinit()
	{
		$this->cctb=filterlist($this->cclist);
		if (($this->smspassw=="") or ($this->smscli=="")) return 403;
		elseif (($this->action!="new") and ($this->action!="putcc") and ($this->action!="getquotat") and ($this->action!="send") and ($this->action!="directsend")) return 1402;
		elseif (($this->action!="new") and ($this->action!="getquotat") and ($this->action!="directsend") and (!is_numeric($this->idsms))) return 2402;
		elseif ((($this->action=="new") or ($this->action=="getquotat") or ($this->action=="directsend")) and ($this->smscli=="")) return 6402;
		elseif ((($this->action=="new") or ($this->action=="directsend")) and (filter_var($this->from, FILTER_SANITIZE_NUMBER_INT)===False)) return 7402;
		elseif ((($this->action=="new") or ($this->action=="directsend")) and ($this->body=="")) return 1400;
		elseif ((($this->action=="putcc") or ($this->action=="directsend")) and (count($this->cctb)<=0)) return 1400;
		elseif (($this->action=="send") and (!is_numeric($this->idsms))) return 1409;
		return 200;
	}
}

function envoie_un_sms($text,$dest)
{
	$result=new stdclass();
	$result->CR="0";
	$result->MSG="";
	if ($text=="") 
	{
		$result->CR=-1;
		$result->MSG="Il manque le texte au sms";
		return $result;
	}
	if ($dest=="") 
	{
		$result->CR=-2;
		$result->MSG="Il manque un destinataire au sms";
		return $result;
	}

	$command = "gammu -c ".GAMMU_CONFIG_FILE." sendsms TEXT 	\"".$dest."\" -text \"".$text."\" -autolen ".strlen($text);
	$locale = 'fr_FR.UTF-8';
	setlocale(LC_ALL, $locale);
	putenv('LC_ALL='.$locale);
	if  (!shell_exec($command))
	{
		$result->CR=-2;
		$result->MSG="Erreur à l'envoie de ".$text." vers ".$dest;
		return $result;
	}
	$result->MSG="Envoi réussi";
	return $result;
}

function MkXML($cr,$msg,$data="")
{
	$result="<?xml version='1.0' encoding='UTF-8'?>\r\n";
	$result.="<DATAS>\r\n";
	$result.="<CR>".$cr."</CR>\r\n";
	$result.="<MSG>".$msg."</MSG>\r\n";
	if ($data!="") $result.="<DATA>".$data."</DATA>\r\n";
	$result.="</DATAS>\r\n";
	return $result;
}
	
/****************************************************************************/
$cmd=new command();
$r=$cmd->checkinit();
if ($r!=200) 
{ 
	log_event("Pb checkinit CR=".strval($r)); 
	echo($r); 
	http_response_code($r%1000); 
	die(); 
}
/****************************************************************************/
$data=new db("./db/smsapi.db");
$r=$data->check_cli($cmd->smscli,$cmd->smspassw);
if ($r->CR!="0") 
{ 
	log_event("Pb check_cli CR=".strval($r->CR)." MSG=".$r->MSG); 
	echo($r->MSG); 
	http_response_code(401); 
	die(); 
}
$cmd->idsmscli=$r->DATA;

/****************************************************************************/
if ($cmd->action=="new")
{
	$r=$data->create_sms($cmd);
	$s=MkXML($r->CR,$r->MSG,$r->DATA);
	if ($r->CR!="0") 
	{ 
		log_event("Pb create_sms CR=".strval($r->CR)." MSG=".$r->MSG); 
		echo($s);
		http_response_code(402); die(); 
	}
	echo($s);
	$cmd->idsms=$r->DATA;
}
/****************************************************************************/
if ($cmd->idsms!="")
{
	$r=$data->get_sms_status($cmd->idsms);
	$s=MkXML($r->CR,$r->MSG,$r->SENT);
	if ($r->CR!="0") 
	{ 
		log_event("Pb get_sms_status CR=".strval($r->CR)." MSG=".$r->MSG); 
		echo($s); 
		http_response_code(400);  
		die();
	}
	//if ($r->SENT=="Y") { echo("Message déjà envoyé"); http_response_code(400); die(); }
	//elseif ($r->SENT=="") { echo("Le message num ".$cmd->idsms." n'existe pas"); http_response_code(400); die(); }
}
/****************************************************************************/
if (($cmd->action=="putcc") and (count($cmd->cctb)>0))
{
	$r=$data->put_dest($cmd);
	$s=MkXML($r->CR,$r->MSG,$r->DATA);
	if ($r->CR!="0") 
	{ 
		log_event("Pb put_dest CR=".strval($r->CR)." MSG=".$r->MSG); 
		echo($s); 
		http_response_code(400);  
		die(); 
	}
	echo($s);
}
/****************************************************************************/
if ($cmd->action=="getquotat")
{
	$r=$data->get_quotatleft($cmd->smscli);
	$s=MkXML($r->CR,$r->MSG,$r->DATA);
	if ($r->CR!="0") 
	{ 
		log_event("Pb get_quotatleft CR=".strval($r->CR)." MSG=".$r->MSG); 
		echo($s); 
		http_response_code(400); 
		die(); 
	}
	echo($s);
}
/****************************************************************************/
if ($cmd->action=="send")
{
	if (count($cmd->cctb)>0)
	{
		$r=$data->put_dest($cmd);
		$s=MkXML($r->CR,$r->MSG,$r->DATA);
		if ($r->CR!="0") 
		{ 
			log_event("Pb put_dest CR=".strval($r->CR)." MSG=".$r->MSG); 
			echo($s); 
			http_response_code(400);  
			die(); 
		}
	}
	
	//A faire : vérifier le quotat
	$r=$data->get_quotatleft($cmd->smscli);
	$s=MkXML($r->CR,$r->MSG,$r->DATA);
	if ($r->CR!="0") 
	{ 
		log_event("Pb get_quotatleft CR=".strval($r->CR)." MSG=".$r->MSG); 
		echo($s); 
		http_response_code(400); 
		die(); 
	}
	$quotatleft=$r->DATA;
	
	
	//récupérer les destinataires
	$r=$data->get_dest_sms($cmd->idsms);
	$s=MkXML($r->CR,$r->MSG,"cclist:".implode(",",$r->CCLIST));
	if ($r->CR!="0") 
	{ 
		log_event("Pb get_dest_sms CR=".strval($r->CR)." MSG=".$r->MSG); 
		echo($s); 
		http_response_code(400); die(); 
	}	
	$cmd->cclist=implode(";",$r->CCLIST);
	$cmd->cctb=filterlist($cmd->cclist);
	
	//récupérer le sms
	$r=$data->get_sms_data($cmd->idsms);
	$s=MkXML($r->CR,$r->MSG,"");
	if ($r->CR!="0") 
	{
		log_event("Pb get_sms_data CR=".strval($r->CR)." MSG=".$r->MSG); 
		echo($s); 
		http_response_code(400); 
		die(); 
	}
	$cost=count($cmd->cctb)*ceil(strlen($r->BODY)/160);
	if ($cost>$quotatleft)
	{
		$s=MkXML("1","Quotat insuffisant",strval($cost)." / ".strval($quotatleft));
		echo($s);
	}
	else
	{
		$cmd->from=$r->SENDER;
		$cmd->body=$r->BODY;
		$ok=True;
		foreach ($cmd->cctb as $cc)
		{
			$r=envoie_un_sms($cmd->body,$cc);
			if ($r->CR=="0") 
			{
				$u=$data->update_sms_dest($cmd->idsms,$cc);
			}
			else 
			{
				log_event("Pb envoie_un_sms CR=".strval($r->CR)." MSG=".$r->MSG); 
				$ok=False;
				$s=MkXML($r->CR,$r->MSG);
				echo($s);
			}
		}
		if ($ok!==True)
		{ http_response_code(400); die(); }
		else
		{
			$s=MkXML($r->CR,$r->MSG,$cmd->idsms);
			echo($s);
		}
	}
}

/****************************************************************************/
if ($cmd->action=="directsend")
{
	$r=$data->create_sms($cmd);
	$s=MkXML($r->CR,$r->MSG,$r->DATA);
	if ($r->CR!="0") 
	{ 
		log_event("Pb create_sms CR=".strval($r->CR)." MSG=".$r->MSG); 
		echo($s);
		http_response_code(402); die(); 
	}
	$cmd->idsms=$r->DATA;

	$r=$data->put_dest($cmd);
	$s=MkXML($r->CR,$r->MSG,$r->DATA);
	if ($r->CR!="0") 
	{ 
		log_event("Pb put_dest CR=".strval($r->CR)." MSG=".$r->MSG); 
		echo($s); 
		http_response_code(400);  
		die(); 
	}
	
	$r=$data->get_quotatleft($cmd->smscli);
	$s=MkXML($r->CR,$r->MSG,$r->DATA);
	if ($r->CR!="0") 
	{ 
		log_event("Pb get_quotatleft CR=".strval($r->CR)." MSG=".$r->MSG); 
		echo($s); 
		http_response_code(400);
		die();
	}
	$quotatleft=$r->DATA;
	
	//récupérer les destinataires
	$r=$data->get_dest_sms($cmd->idsms);
	$s=MkXML($r->CR,$r->MSG,"ccdlist:".implode(",",$r->CCLIST));
	if ($r->CR!="0") 
	{ 
		log_event("Pb get_dest_sms CR=".strval($r->CR)." MSG=".$r->MSG); 
		echo($s); 
		http_response_code(400); 
		die(); 
	}
	$cmd->cclist=implode(";",$r->CCLIST);
	$cmd->cctb=filterlist($cmd->cclist);
	
	//récupérer le sms
	$r=$data->get_sms_data($cmd->idsms);
	$s=MkXML($r->CR,$r->MSG,"");
	if ($r->CR!="0") 
	{ 
		log_event("Pb get_sms_data CR=".strval($r->CR)." MSG=".$r->MSG); 
		echo($s); 
		http_response_code(400); 
		die(); 
	}
	$cost=count($cmd->cctb)*ceil(strlen($r->BODY)/160);
	if ($cost>$quotatleft)
	{
		log_event("Pb quotat insuffisant");
		$s=MkXML("1","Quotat insuffisant",strval($cost)." / ".strval($quotatleft));
		echo($s);
	}
	else
	{
		$cmd->from=$r->SENDER;
		$cmd->body=$r->BODY;
		$ok=True;
		foreach ($cmd->cctb as $cc)
		{
			$r=envoie_un_sms($cmd->body,$cc);
			if ($r->CR=="0") 
			{
				$u=$data->update_sms_dest($cmd->idsms,$cc);
			}
			else 
			{
				log_event("Pb envoie_un_sms CR=".strval($r->CR)." MSG=".$r->MSG); 
				$ok=False;
				$s=MkXML($r->CR,$r->MSG);
				echo($s);
			}
		}
		if ($ok!==True)
		{ http_response_code(400); die(); }
		else
		{
			log_event("Réussite envoie_un_sms CR=".strval($r->CR)." MSG=".$r->MSG); 
			$s=MkXML($r->CR,$r->MSG,$cmd->idsms);
			echo($s);
		}
	}
}
$data->close();
http_response_code(200);
?>