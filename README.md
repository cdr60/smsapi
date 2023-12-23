# smsapi
simple web api to delegate sending sms on a <b>linux server</b> that owns a sim card<br><br>

# How it works :
<ul>
<li>This api is php written and use a sqlite database.</li>
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
</ul>ul>




