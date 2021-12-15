# ITFINDEN-DNS-CHECK
 Sistema de Chequeo de DNS para Cpanel


Install

mkdir -p /usr/local/cpanel/whostmgr/docroot/cgi/cpanel-account-dns-check

cd /usr/local/cpanel/whostmgr/docroot/cgi/cpanel-account-dns-check/

wget --no-check-certificate -O master.zip https://github.com/itfinden/ITFINDEN-DNS-CHECK/archive/refs/heads/main.zip

unzip master.zip

/bin/cp -rf /usr/local/cpanel/whostmgr/docroot/cgi/cpanel-account-dns-check/cPanel-DNS-Check-Account-WHM-Plugin-master/* /usr/local/cpanel/whostmgr/docroot/cgi/cpanel-account-dns-check/

/bin/rm -rvf /usr/local/cpanel/whostmgr/docroot/cgi/cpanel-account-dns-check/cPanel-DNS-Check-Account-WHM-Plugin-master

/bin/rm -f /usr/local/cpanel/whostmgr/docroot/cgi/cpanel-account-dns-check/master.zip

/usr/local/cpanel/bin/register_appconfig /usr/local/cpanel/whostmgr/docroot/cgi/cpanel-account-dns-check/cpanel-account-dns-check.conf