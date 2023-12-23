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
<li>A log folder, it must be web server writable : you will find log files here</li>
<li>A tmp folder for temporary files, it must be web server writable</li>
<li>A db folder, it must be web server writable. It contains smsapi.db file (the sqlite database) that must be web server writable</li>
<li>In db folder <b>you have to put a .htaccess file to prevent direct download of database !</b><br>Use someting like this :<Files "smsapi.db">Require all denied</Files></li>
<li>A service folder that contains what is needed to send SMS :
  <ul>
    <li>.gammurc is a file used by gammu (the sms binary file). You have to check the line "port = /dev/ttyUSB0" is your Sim card USB adaptatrer is in another port, and "connection = at19200" if your adaptater is using another dial speed </li>
    <li>group.txt : A CLI example to add your web serveur user to dialout group : beacause the web server must access to the com port. This exemple is for apache 2.4 on Fedora. Check you web server account, it can be www-data on Debian.</li>
    <li>usbmodeswitch.conf : This is my usbmodeswitch configuration file : I'm using a Huawei USB Sim Card adaptater : check what configuration you need.</li>
    <li>pincode.sh : this script must be run at boot time : It will unlock Sim card's protection using your PIN Code : replace your pincode at line "pincode='0000'"</li>
    <li>pincode.service : The pincode unlocking service description  file. Check the pincode.sh folder (I'm using /opt/gammu folder tu put pincode.sh)</li>
  </ul>
</li>
</ul>




