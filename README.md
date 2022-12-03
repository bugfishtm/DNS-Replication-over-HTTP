


# DNS Replication over HTTP
This script can make your Master DNS Replicate to Slave DNS Servers just over HTTP/HTTPS without the need to open additional Ports or having trouble with bind9 Domain Configuration. This Panel comes with an API which makes it possible to share all needed Informations via HTTP and so have better Control over Large Replication Systems (for example for huge hosting infrastructure)

## Installation
* Install SQL Files to Database
* Edit settings.php and Change needed Variables
* Add Line 'include "/etc/bind/dnshttp.conf.local";' to your Slave Servers named.conf.local 
* Setup needed Cronjobs

## Cronjobs for Slave Server
cron/bind_domains.php [Run Every 10 Minutes]
This Cronjob Syncs the current Domains in named.conf.local.
with this Websites Database to provide it to slave Servers.

blacklist_reset [Every Day]
Resets the IP Blacklist for Login - This is an Anti BruteForce Feature

sync_names [Every 10 Minutes]
Sync the Names from Master Servers to This Server.

sync_content [Every 10 Minutes]
Sync Domain Content from Fetched Names to Domains on Slave Server from Master Server

sync_bind [Every 10 Minutes]
Setup Bind on Slave Server to run with new Settings Fetched from Master Server
**AFTER THIS CRONJOB HAS BEEN EXECUTED, BIND9 SHOULD BE 	RESTARTED OTHERWHISE CHANGES WILL NOT TAKE EFFECT. After the cronjob run, just 	restart or reload bind9.**

## Cronjobs for Master Server
cron/bind_domains.php [Run Every 10 Minutes]
This Cronjob Syncs the current Domains in named.conf.local.
with this Websites Database to provide it to slave Servers.

blacklist_reset [Every Day]
Resets the IP Blacklist for Login - This is an Anti BruteForce Feature

## Requirements  
-> Apache2 with PHP7.4 [PHP 8 untested]
-> PHP Modules: curl, intl, gd

##  Configure a Slave/Master Connection
- Enter the Slave Server in Panel on Master Server with token "123"  
- Enter Master Server in Slave Server with SAME token used on Master Server   
- Domains will be fetched from Master locally, than delivered to the Slave Server in intervalls you can setup by editing the cronjob execution intervalls. API-Token is the Token which needs to be the Same inside a Master/Slave Connection. API-Path is the Path to this Script installed on an External server. For example https://domain OR http://domain/dnsscriptsubfolder  

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
#### BIND Parameters
\_CRON_BIND_LIB\_ - Library Path where Bind Domain Files are Stored  
\_CRON_BIND_CACHE\_ - Library Path where Bind Domain Cache Files are Stored  
\__CRON_BIND_FILE_DNSHTTP\_ - DNS HTTP Bind File  
\_CRON_BIND_FILE\_ - 1 =  named.conf file Location  
  
### Bind 9 named.conf.options Config Example

	options {
		directory "/var/lib/bind";
	    dnssec-validation auto;
	    listen-on-v6 { any; };
	    version "None Available";
	    recursion no;
	    querylog yes;

    // Enter your Master-DNS IP here to allow transfers from it (It needs to be configured)
    allow-update { 127.0.0.1};
    // Allow Querys from Every Remote Host
    allow-query { any; };

    // Enable DNSSec
    check-names master ignore;
    check-names slave ignore;
    notify yes;
    allow-transfer {127.0.0.1};
    check-names response ignore;
	};

## Issues
If you encounter issues or have questions using this software, do not hesitate write us at contact@bugfish.eu or take a look at bugfish.eu!

## Default Login for Webinterface
Username: admin  
Passwort: changeme

----------------------------------------------------------------
##### more at www.bugfish.eu 
