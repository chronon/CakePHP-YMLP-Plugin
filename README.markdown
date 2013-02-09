CakePHP YMLP (Your Mailing List Provider) Component
===================================================

This component interfaces a CakePHP app with YMLP's API. Pass the component some data containing at
least an email address and the API method to use, and it will make the API post and return
a formatted message.

Compatibility:
--------------

Tested with CakePHP 2.3.x. Requires PHP 5 with cURL support.

Installation:
-------------

Using git, something like this:

	git clone git@github.com:chronon/CakePHP-YmlpComponent-Plugin.git APP/Plugin/Ymlp  

Configuration:
--------------

All configuration is in APP/Config/bootstrap.php.

**Required:** Load the plugin:
	
	CakePlugin::load('Ymlp');

**Required:** Set your YMLP API key and username:

	Configure::write('Ymlp.settings', array(
		'Key' => '1234567890ABCDEFG',
		'Username' => 'yourusername',
	));

Usage:
------
