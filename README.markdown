CakePHP [YMLP](http://www.ymlp.com/) Component
==============================================

[YMLP](http://www.ymlp.com/), or Your Mailing List Provider, is a email newsletter and marketing
service with free and paid accounts, and a well written API.

This component interfaces a CakePHP app with YMLP's API.  

You will need a free or paid account at [YMLP](http://www.ymlp.com/), along with your username and 
API key (found at ymlp.com under "Configuration", "API"). Make sure to enable API access on YMLP's 
API page! 

Compatibility:
--------------

Tested with CakePHP 2.3.x. Requires PHP 5 with cURL support.

Installation:
-------------

Using git, something like this:

```sh
git clone git@github.com:chronon/CakePHP-YmlpComponent-Plugin.git APP/Plugin/Ymlp  
```

Configuration:
--------------

All configuration is in APP/Config/bootstrap.php.

**Required:** Load the plugin:

```php
CakePlugin::load('Ymlp');
```
or 

```php
CakePlugin::loadAll();
```

**Required:** Set your YMLP API key and username. 

```php
Configure::write('Ymlp.settings', array(
	'Key' => '1234567890ABCDEFG',
	'Username' => 'yourusername',
));
```

**Required:** Set your YMLP field mapping, which is your local field => YMLPField. 

```php
Configure::write('Ymlp.fieldMap', array(
	'email' => 'Email',
));
```

Usage:
------

Add the component to your controller:

```php
public $components = array('Ymlp.Ymlp');
```

Example: if you have a subscriber form with a field named `email`, `$this->request->data` would look 
something like this:

```php
['Subscriber'] => array(
	'email' => 'some@email.com'
)
```

To add this email address to YMLP using the component:

```php
$result = $this->Ymlp->command('Contacts.Add', $this->request->data['Subscriber']);
```
	
The `$result` would be the response from YMLP's `Contacts.Add()` method. See the YMLP docs for all
available API commands. 

The available methods of this component are:

```php
utility($method, $data = array())

/**
 * A utility method to send anything to the YMLP API
 *
 * @param string $method The YMLP API method call
 * @param array  $data Data to pass to the YMLP method
 * @return string
 * @access public
 */
 
command($method, $data)

/**
 * The primary method to format data, post it to the YMLP API, and then format
 * the returned result.
 *
 * @param string $method The YMLP API method call
 * @param array  $data Data to pass to the YMLP method
 * @return string
 * @access public
 */
``` 

Example usage of the `utility` method to view a list of configured YMLP fields. If you created
a form at YMLP to collect name, email address, and mailing address, you need to match your app's
fields with YMLP fields. The YMLP API `Fields.GetList()` method can give you what you need. 

Create a temporary method in your controller and use the component's `utility` method:

```php
public function utility() {
	$result = $this->Ymlp->utility('Fields.GetList');
	debug($result);
	exit;
}
```

Visit /utility and you should see a list of YMLP configured fields with field id, which you can use 
to map to your app fields with the `Ymlp.fieldMap` configuration array. 

```php
Configure::write('Ymlp.fieldMap', array(
	'email' => 'Email',
	'first_name' => 1,
	'last_name' => 2,
	'address' => 3,
	'city' => 4,
	'state' => 5,
	'zip' => 6,
));
```
