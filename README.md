# smsapi
simple web api to delegate sending sms on a <b>linux server</b> that owns a sim card<br><br>

# What you needs :
<ul>
<li>A linux web server using apache or nginx or lighttp.</li>
<li>Php from 7.x to 8.2</li>
<li>sqlite3 and php-sqlite module</li>
<li>gammu : the SMS sender binary file</li>
<li>An USB Sim card adaptater</li>
<li>An activated Sim card </li>
<li>Kownledges to call a web api with your prefered language </li>
</ul>

# How it works :
<ul>
<li>This api is php written and uses a sqlite database.</li>
<li>It calls gammu binary executable and of course needs a SIM Card</li>
<li>At boot, your SIM card must be PIN unlocked (a service is used to send your PIN Code at boot time)</li>
<li>Your application (web site or whatever) has to call this web api using curl (is your langage is php) or requests (if python)</li>
<li>Be carefull about execution time (sometimes it takes several secondes to send a SMS)</li>
<li>Datas are in a sqlite database</li>
<li>This api  provides a test web page do test it manually.</li>
<li>You have to create clients ( = which account is sending a SMS)</li>
<li>This api provides quotat by clients (account) (sending SMS wil decrease it, and you have to increase it manually using update via sqlite studio, cli or whatever you want)</li>
<li>Accessing this api is secured by user/password that must exists in TBSMSCLI sqlite database (account list)</li>
<li>Each account owns  user/password in TBSMSCLI table</li>
<li>These api checks quotat via the VQUOTATLEFT view in SQLITE database</li>
<li>Quotats can be changed using TBQUOTAT table in sqlite database</li>
<li>Sqlite database access must to be secured my an .htaccess file</li>
<li>Sqlite database provides SMS history</li>
</ul>

# What you will find here :
<ul>
<li>404.gif : a simple 404 gif.</li>
<li>index.html : a simple root file that show 404.</li>
<li>test.php : a web page to use this api manually, it uses demo acocunt (password is demopass : take a look at the beginning of this file)</li>
<li>smsapi.css : the style file for test.php</li>
<li>index.php : the root api php file (I suggest you to user another name)</li>
<li>robots.txt : a file to prevent search indexing</li>
<li>A log folder, it must be web server writable : you will find log files here.I recommend to put an htaccess file here to prevent direct downloading.</li>
<li>A tmp folder for temporary files, it must be web server writable.I recommend to put an htaccess file here to prevent direct downloading.</li>
<li>A db folder, it must be web server writable. It contains smsapi.db file (the sqlite database) that must be web server writable</li>
<li>In db folder <b>you have to put a .htaccess file to prevent direct download of database !</b><br>Use someting like this :<br><br>
  <blockquote>&lt;Files 'smsapi.db'&gt;Require all denied&lt;/Files&gt;</blockquote>blockquote></li>
<li>A service folder that contains what is needed to send SMS :
  <ul>
    <li>.gammurc is a file used by gammu (the sms binary file). You have to check the line "port = /dev/ttyUSB0" is your Sim card USB adaptater is in another port, and "connection = at19200" if your adaptater is using another dial speed </li>
    <li>group.txt : A CLI example to add your web serveur user to dialout group : beacause the web server must access to the com port. This exemple is for apache 2.4 on Fedora. Check you web server account, it can be www-data on Debian.</li>
    <li>usbmodeswitch.conf : This is my usbmodeswitch configuration file : I'm using a Huawei USB Sim Card adaptater : check what configuration you need.</li>
    <li>pincode.sh : this script must be run at boot time : It will unlock Sim card's protection using your PIN Code : replace your pincode at line "pincode='0000'"</li>
    <li>pincode.service : The pincode unlocking service description  file. Check the pincode.sh folder (I'm using /opt/gammu folder tu put pincode.sh)</li>
  </ul>
</li>
</ul>

# Installation :
Assuming you have a web service that is running<br>
Assuming you have installed gammu<br>
If gammu needs your pin code at each boot (you can check it with <b>gammu getsecuritystatus</b>), you can use my pincode.sh and pîncode.service files to do it.<br>
Assuming that gamm can send SMS by cli<br>
Check it with <b>gammu sendsms TEXT aphonenumber -text "Test by CLI"</b><br>
<br>
If you need some gammu config file, define it in index.php like this<br>
<b>DEFINE("GAMMU_CONFIG_FILE","/root/.gammurc");</b> or <b>DEFINE("GAMMU_CONFIG_FILE","");</b><br>
<br>
Put all the content repo in a document root web server folder (or subfolder)<br>
make ./log, ./tmp and ./db/smsdb.db writable by your web server 
<br>
Open ./db/smsdb.db with sqlite and create or choose password for the demo account (see TBPARAM).<br>
Check TBQUOTAT for this useror insert a row in TBQUOTAT to make your new user own some SMS to send.<br>
<br>
Go to a web navigator and ask for index.php, you will see : 403 (Forbidden)<br>
<br>
Open test.php, search at the beginning of the file for : <b>$smspassw=GetVariableFrom($_POST,"smspassw","demopass");</b> and replace <b>demopass</b> by your demo password if you changed it in TBPARAM.<br>
in index.php search for <b>filterlist</b> function, because the script only accept French phone number, you will have to change the rules if you leave in another country.
<br>
Go to a web navigator and ask for test.php you will see a test page, here chose a client, put the from phone numer (SIM Card phone number), give one or more recipients, and a message to send.<br>
Chose directsend<br>
<br>
You have received this SMS ? Yeah, next one how to use this api programmaticaly.<br>

# How to use this api programmaticaly
See in test.php the action listbox :<br>
You can :
<ul>
  <li>new : create a new SMS without sending : you have to give the from number (SIM Card phone Number) , the message body, the accound and his password. It will return a SMS Id</li>
  <li>putcc : remplace or put recipients in an existing SMS (you will have to give the SMS Id, the accound and his password)</li>
  <li>getquotat : giving the accound and his password. It will return, how much SMS this account can send</li>
  <li>directsend : create and send a new SMS : you have to give the from number (SIM Card phone Number) , the message body, a recipient, the accound and his password. It will return the SMS Id</li>
  <li>send : send a SMS previously fully created : you have to give ths SMS Id ,the accound and his password.</li>
</ul>

# Calling api
Use curl, python requests library, javascript fetch or whatever you want.
You have to call the <b>index.php</b> script using <b>POST</b>
You have to give these POST values :
<ul>
  <li><b>smscli</b> : this is the string id of the account that want to send a message (ex : demo)</li>
  <li><b>smspassw</b> : this is the string password of the account that want to send a message (ex : demopass)</li>
  <li><b>from</b> : The SIM card phone number </li>
  <li><b>body</b> : The message (use UTF-8 !)</li>
  <li><b>action</b> : The action to do (new, putcc, getquotat, directsend or send).</li>
  <li><b>idsms</b> : The smsid if you want to update or send a message that you have only created.</li>
  <li><b>cclist</b> : Recipients list, using semi-colon separator.</li>
</ul>

# Returning values
This api will return you infomations for debugging and check what has been done.<br>
Theses informations are in XML format and UTF-8 encoded. You will find :<br>
<ul>
  <li><b>CR</b> : The return code : 0 If everything is OK</li>
  <li><b>MSG</b> : A message if something went wrong</li>
  <li><b>DATA</b> : Some data if neccessary, it can be <b>idsms</b> or <b>quotatleft</b></li>
<ul>

