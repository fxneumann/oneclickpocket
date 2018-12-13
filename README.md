oneclickpocket
==============

Plugin for Tiny Tiny RSS. Add articles to Pocket with a single click or press of a button. Tested with TT-RSS 1.7.9 and TT-RSS 1.8.


Requirements
------------
Requirements that exceed TT RSS' requirements: PHP CURL extension has to be enabled

Installation
------------
* Copy the *oneclickpocket* folder to your tt-rss *plugins/* folder.
* Go to your tt-rss Preference page
* Under *Plugins* section enable oneclickpocket plugin
* A new pref pane will show up, named *Pocket* where you have to enter Pocket credentials (this is *not* your Pocket username and password!):
+ *Pocket Consumer Key* -- to generate a Consumer Key, head to http://getpocket.com/developer/apps/new -- you only need the permission to Add
+ *Pocket Access Token* -- to generate an Access Token, click "Generate Access Token" or open [plugins]/oneclickpocket/auth.php.

Version history
---------------
* 0.1 Initial Version
* 0.2 Icon changes colour when clicked
* 0.3 Added a hotkey (thanks to Bas1c), since 0.31 change icon colour for hotkey, too.
* 0.32 Check for CURL and throw error if missing.
* 0.33 Updated for PDO

Credits
-------
I used Acaranta's (https://github.com/acaranta) Yourls-plugin as template.

