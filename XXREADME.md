

# Mail Relaying over DNS
This is a PHP/MySQL Software which can be run in a webinterface
and with a cronjob to sync to domains registered on a secondary DNS to the Postfix Relaydomains and Transportmaps File. You can Enter Manual Domains to Override Relaying Settings and there is a simple User Management with Permissions. Add Relay Servers and Add Corrosponding TXT Entries to your Domains Record to let the Script regonize the belonging relay.

## Issues
If you encounter issues or have questions using this software, do not hesitate write us at contact@bugfish.eu or take a look at bugfish.eu!

## Requirements
-> Server which is already Set-Up as the secondary Bind Slave DNS Server with Domains replicated.  
-> Apache2 with PHP7.4 [PHP 8 untested]
-> PHP Modules: curl, intl, gd
-> Postfix installed with Config for this Interface (see below)  

## Installation
* Check settings.php and set up as needed, look at the comments to find out what the differents settings do or take a look below.  
* Rename dot.htaccess to .htaccess on webserver
* Install the MySQL Tables located in Folder "sql-install"
* Check if your Postfix is configured for relaying (See example below)
* Setup Cronjob to run every day at midnight

##  Configuration Files

### Configuration File Parameters
#### Default Site Runtime Parameters
\_TITLE\_ - Title for Page to be recognizes in Cronjobs or misc.
\_COOKIES\_ - Pre-String for Cookies to run multiple Instances
\_MAIN_PATH\_ - Document Root Where the Website is Located
#### Mysql Parameters
\_SQL_HOST\_- MySQL Server Host
\_SQL_USER\_ - MySQL Server Username
\_SQL_PASS\_ - MySQL Server Password
\_SQL_DB\_ - MySQL Server Database
#### Security Parameters
\_LOGIN_SESSION_BLOCK_LIMIT\_  - Login Session Ban Fail Limit
\_IP_BLACKLIST_DAILY_OP_LIMIT\_ - Login IP Ban Limit for Fails
\_CSRF_VALID_LIMIT_TIME\_ - Set Valid Time in Seconds for CSRF
#### Postfix Parameters
\_CRON_RELAYMAP\_ - Path to Transport Map.. Default is: /etc/postfix/relaydomains
\__CRON_TRANSMAP\_ - Path to Transport Map.. Default is: /etc/postfix/transportmaps
#### Bind/Postfix Parameters 
\_CRON_ARRAY\_ - Array to Order Priorities for DNS MX Fetch Methods (See Below)
\_CRON_MODE\_ - 1 = Get Domains from named.conf file
\_CRON_MODE_1_PATH\_ - Set Path to named.conf file
\_CRON_MODE\_ - 2 = Get Domains cached File Names in Folder
\_CRON_MODE_1_PATH\_ - Set Path to Bind Cache Files Folder
\_CRON_CLEANUP\_ - Delete all Domains at Start of Cronjob to keep them updated, otherwhise they wont be updated if for example TXT Entrie on Domain Changes
##### DNS MX Fetch Method (CRON ARRAY) "dns-dom"
If Activated, will use domain name as Mail name if nothing else specified in before prioritised methods.
\_CRON_DOMAIN_AS_RELAY\_ - true = Active | false = Inactive
\_CRON_DOMAIN_AS_RELAY_PORT\_ - Port for Domain Relay
\_CRON_DOMAIN_AS_RELAY_PROT\_ - SMTP / SMTPS
##### DNS MX Fetch Method (CRON ARRAY) "dns-sub"
If Activated, will use domain name with added subdomain as Mail name if nothing else specified in before prioritised methods.
\_CRON_SUB_AS_RELAY\_ - true = Active | false = Inactive
\_CRON_SUB_AS_RELAY_SUB\_ - Subdomain to add before Domain for example "mail" without dot after.
\_CRON_SUB_AS_RELAY_PORT\_ - Port for Domain Relay
\_CRON_SUB_AS_RELAY_PROT\_ - SMTP / SMTPS
##### DNS MX Fetch Method (CRON ARRAY) "dns-txt"
If Activated, will use domain txt entrie with defined entrie name to get relay id and use this relay, see txt entrie which are possible in a created relay at "DNS" details..
\_CRON_TXT_TO_RELAY\_ - true = Active | false = Inactive
\_CRON_TXT_TO_RELAY_STRING\_ - TXT Entrie Descriptor which will be used to find the relay id
### Assign DNS Domain to User
If you assign a DNS Domain to a user, this Domain will not update anymore in DNS System until this domain has been removed by the user.
### Cronjob Example
Set up Cronjob, recommended is to run daily. 
Run this cronjob as Root, it needs to change Files on Postfix.
This also resets the Counter for IP-Bans, every day to prevent Password Brute Forcing.

This could be the cron command running as root daily:  
- php7.4 /var/www/html/cron/cron.php; postmap /etc/postfix/relaydomains; postmap /etc/postfix/transportmaps; service postfix restart

Replace /var/www/html/ with the Document Root WWW Folder where the Interface has been uploaded.

### Postfix Config Example
smtpd_banner = $myhostname ESMTP $mail_name (Debian/GNU)  
biff = no  
append_dot_mydomain = no  
readme_directory = no  
compatibility_level = 2  
  
smtpd_tls_cert_file=*****PATHTOSSLCERT***** 
smtpd_tls_key_file=*****PATHTOSSLKEY*****
smtpd_use_tls=yes  
smtpd_tls_session_cache_database = btree:${data_directory}/smtpd_scache  
smtp_tls_session_cache_database = btree:${data_directory}/smtp_scache  
  
smtpd_relay_restrictions = permit_mynetworks permit_sasl_authenticated reject_unauth_destination  
myhostname = *****THISMAILSRVHOSTNAME*****  
alias_maps = hash:/etc/aliases  
alias_database = hash:/etc/aliases  
myorigin = /etc/mailname  
mydestination = $myhostname, localhost  
relayhost =  
mynetworks = 127.0.0.0/8 [::ffff:127.0.0.0]/104 [::1]/128  
mailbox_size_limit = 0  
recipient_delimiter = +  
inet_interfaces = all  
inet_protocols = all  
maximal_queue_lifetime = 30d  
  
relay_recipient_maps =  
relay_domains = hash:/etc/postfix/relaydomains  
transport_maps = hash:/etc/postfix/transportmaps  


## Default Login for Webinterface
Username: admin  
Passwort: changeme

----------------------------------------------------------------
##### more at www.bugfish.eu 
