Easy-WI.com
=========

Reaching [Milestone 4.00](https://github.com/easy-wi/developer/issues?milestone=1&page=1&state=closed) Easy-WI becomes free Software with the terms of GNU GPL v3.

What is Easy-WI?
------------------------
First of all Easy-Wi is a Web-interface that allows you to manage server daemons like gameservers. In addition it provides you with a CMS which includes a fully automated game- and voiceserver lending service. 
The development goal is always to automate as far as possible. The daily work which requires an administrator should be reduced to a minimum.

Requirements
------------------------
* Web installation requires PHP 5.4 or later where the extensions openssl, json, hash, ftp, SimpleXML, curl, gd, PDO, pdo_mysql and fopen are installed
* The gameserver module requires sudo, cron, proftpd and the bash shell at the game root
* The Voicemodule works best with a linux based TS3 server

Who is the target group for Easy-WI?
------------------------
No matter if you are a commercial entity that is providing hosting solutions, are sponsoring (game)server daemons, organize a LAN party, need to manage clan server, or are a private individual, Easy-WI is meant for everybody.


Which functions and modules are available?
------------------------
What drives the development is the goal to automate all processes. Listing all available functions would result in an exploding thread. So here is the summary with the main features:
* Mobile ready. The default template has been made with Twitter Bootstrap and is responsive. That way Easy-WI becomes a Web App which can be easily used with a mobile or tablet.
* Multilingual. Currently supported are English, Danish, Italian and German. The text is maintained with XML files.
* We have a strict separation between PHP modules and HTML views. In case a view is missing at your custom templates the default will be used as fall-back.
* Gameserver management is nearly fully automated. All you need to do is updating add-ons at your central image server from time to time. After that the deployment to the individual servers is automated.
* The same applies to TS3 voiceserver.
* In addition to a TS3 server you can manage TSDNS either as standalone, or together with the TS3 master. 
* Game- as well as voiceserver can be monitored. Server offline? To many slots? Password is missing at a private server? Branding removed from the server`s name? Easy-WI will correct that for you.
* Already existing game- and voiceservers can be imported into assigned to an user.
* With the help of PXE, DHCP and TFT you can install images to your ESX(i) and dedicated servers.
* Reseller Accounts can be setup.
* All modules can be used with a REST API as well. That way you can include Easy-WI in already existing processes given with a shop like WHMCS or Magento.


How does the support work?
------------------------
Typical Open Source we have:
* Wiki [wiki.easy-wi.com](http://wiki.easy-wi.com)
* Forum [easy-wi.com/forum/](https://easy-wi.com/forum/)
* Bug tracker that can be used for feature requests as well at [github.com](https://github.com/easy-wi/developer/issues?state=open)


Where can I download Easy-WI?
------------------------
* The stable can be found at our [download area](https://easy-wi.com/uk/downloads/)
* If you like to experiment you might want to check out the [Github Repository](https://github.com/easy-wi/developer)
