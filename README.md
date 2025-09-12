ZPanel
======
Modern, customizable web user panel system for RO emulators

Requirements
---------
* PHP 8.2
* MariaDB
* Official [Hercules Emulator](https://github.com/HerculesWS/Hercules)
* Apache Web Server


Current version
---------
* 1.0 - Initial Release
	- Only basic functions are available (See Features)
	- Only supports Hercules for now (at least only fully tested in)

Features
---------
* Core
	- Registration (email verification ready -- toggleable)
	- Password update
	- Configurable character unstuck
	- Game account basic dashboard (lists characters)
	- Character summary view
* Customization
	- Custom theme ready (May add your own style and javascripts)
	- Included Bootstrap 5.3.2 and FontAwesome 6.4.2 as global assets
* Security
	- Server side sessions handling
	- CSRF protection
	- SSL support
	
How To ... ?
---------
- Add files in your web Server
- Go to config.php and configure your server, application and feature settings
- (IMPORTANT) Modify the .htaccess file (in Rewrite Rules > RewriteBase ) following the value in config.php > root_path
- Import sql tables to your main database (sql-files)

To-do
---------
- Create a comprehensive documentation for installation and custom theme creation guides.
- Release version 1.5 in 2 weeks (list of additional features in the next version will be posted here).

Credits
---------
- My proud work. Future additional features will be added and I am open to suggestions!
[My Discord](https://discord.com/users/679171391395856394) | [Buy me a coffee for motivation](https://paypal.me/ZGDonate2020)
