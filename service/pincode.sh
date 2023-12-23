#/bin/sh
#Mettre le code pin de la cle 3G au boot
pincode="0000"
/usr/bin/gammu entersecuritycode PIN "$pincode"
/usr/bin/gammu getsecuritystatus
