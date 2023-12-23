# smsapi
simple web api to delegate sending sms on a server that owns a sim card

# How it works :
This api is php written and use a sqlite database.
It calls gammu binary executable and of course needs a SIM Card
At boot, your SIM card must be PIN unlocked (a service is used to send your PIN Code at boot time)
Your application (web site or whatever) has to call this web api using curl (is your langage is php) or requests (if python)
Be carefull about execution time (sometimes it takes several secondes to send a SMS)
Datas are in a sqlite database
This api  provides a test web page do test it manually.
You have to create clients ( = which account is sending a SMS)
This api provides quotat by clients (account) (sending SMS wil decrease it, and you have to increase it manually using update via sqlite studio, cli or whatever you want)
Accessing this api is secured by user/password that must exists in TBSMSCLI sqlite database (account list)
Each account owns  user/password in TBSMSCLI table
These api checks quotat via the VQUOTATLEFT view in SQLITE database
Quotats can be changed using TBQUOTAT table in sqlite database
Sqlite database access must to be secured my an .htaccess file
Sqlite database provides SMS history





