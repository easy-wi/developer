# Easy-WI.com Links
- The stable build at [GitHub](https://github.com/easy-wi/developer/releases/latest)
- The developer builds at [Github](https://github.com/easy-wi/developer/tags)
- Developers ChatRoom: [![Join the chat at https://gitter.im/easy-wi](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/easy-wi?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
- Issue Tracking at [GitHub](https://github.com/easy-wi/developer/issues)
- [Installer](https://github.com/easy-wi/installer/)
- [Optional Server Side Scripts](https://github.com/easy-wi/server/)
- [External PHP Scripst](https://github.com/easy-wi/external/)
- [Easy-Wi WHMCS Addon](https://github.com/easy-wi/whmcs)

# What is Easy-WI?
First of all Easy-Wi is a Web-interface that allows you to manage server daemons like gameservers. In addition it provides you with a CMS which includes a fully automated game- and voiceserver lending service. 
The development goal is always to automate as far as possible. The daily work which requires an administrator should be reduced to a minimum.

# Requirements
- Web installation requires PHP 5.4 or later where the extensions openssl, json, hash, ftp, SimpleXML, curl, gd, PDO, pdo_mysql and fopen are installed
- The gameserver module requires sudo, cron, proftpd and the bash shell at the game root
- The Voicemodule works best with a linux based TS3 server

# Who is the target group for Easy-WI?
No matter if you are a commercial entity that is providing hosting solutions, are sponsoring (game)server daemons, organize a LAN party, need to manage clan server, or are a private individual, Easy-WI is meant for everybody.

# Which functions and modules are available?
What drives the development is the goal to automate all processes. Listing all available functions would result in an exploding thread. So here is the summary with the main features:
- Mobile ready. The default template has been made with Twitter Bootstrap and is responsive. That way Easy-WI becomes a Web App which can be easily used with a mobile or tablet.
- Multilingual. Currently supported are English, Danish, Italian and German. The text is maintained with XML files.
- We have a strict separation between PHP modules and HTML views. In case a view is missing at your custom templates the default will be used as fall-back.
- Gameserver management is nearly fully automated. All you need to do is updating add-ons at your central image server from time to time. After that the deployment to the individual servers is automated.
- The same applies to TS3 voiceserver.
- In addition to a TS3 server you can manage TSDNS either as standalone, or together with the TS3 master. 
- Game- as well as voiceserver can be monitored. Server offline? To many slots? Password is missing at a private server? Branding removed from the server`s name? Easy-WI will correct that for you.
- Already existing game- and voiceservers can be imported into assigned to an user.
- Reseller Accounts can be setup.
- All modules can be used with a REST API as well. That way you can include Easy-WI in already existing processes given with a shop like WHMCS or Magento.

