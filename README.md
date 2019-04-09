[![CircleCI](https://circleci.com/gh/jnbarlow/pholdbox.svg?style=shield)](https://circleci.com/gh/jnbarlow/pholdbox)

# Introduction

Pholdbox is a lightweight PHP framework that supports clustering and includes IOC (inversion of control) and an ORM layer.  It 
is designed to make authoring sites an easy, fun process with structure and logical control paths 
(sure, we all say that about our frameworks) :). It is based on Coldbox, a ColdFusion framework.

Pholdbox is free to use, and contributions are welcome.

The framework requires PDO and PHP 7 and above, so keep that in mind :).  The main system files 
(in /system) are fairly well documented and should give you a decent idea about how to use it, as 
will the sample project skeleton and content.

Also included is a vagrant config to get a test environment up and running quickly.  

Note: Any modifications to the framework must be submitted for consideration in inclusion to the 
framework.

Note: Any enhancements/modifications/contributions you submit to the project are done so with no 
expectation for compensation (just maybe some fame, no fortune).  

Check the Releases section for the latest releases. Master may or may not be stable.

# Getting Started
Before you get started you need to make sure you're using PHP 7 and above and PDO.  Currently, only MySQL is supported.

## Anatomy of a PholdBox Site
The directory structure of PholdBox very closely matches that of ColdBox. Your site skeleton should look like:

* /
* config
  * config.php - Main framework config file.
* handlers - Event handlers (controller)
* includes - Site includes, css, js, images, etc
  * css
  * js
  * images
* layouts - Page layouts
* model - Site model objects (model)
* system - PholdBox Core
* views - Site views (view)
* index.php - driver

# config.php
This file is the main config for PholdBox.  It is in this file that the **$SYSTEM** object is created ($GLOBALS["SYSTEM"] that is used throughout the codebase).  

### Keys
| $SYSTEM key | Definition |
| ----------- | -------------- |
| ["debug"]    | Boolean to enable debug output at the bottom of the page layout. This is useful to get program flow, memory usage, and dumps of various system structures at the time they were used.
| ["dbBatchSize"] | beta key, currently under construction (may not be final)
| ["dsn"]        | System datasources.  You can define multiple ones here for different databases if you like
| ["dsn"]["default"] | The default datasource used by PhORM.
| ["dsn"]["(datasource name)"] | a datasource
| ["dsn"]["(datasource name)"]["connection_string"] | PDO connection string to a database `array("mysql:host=localhost;dbname=pholdbox", "root", "root");`
| ["default_layout"] | default layout for the site (required)
| ["default_event"] | default event to fire if none is specified in the URL (required)

One other feature is being able to provide site specific configurations to override any config already defined.  These overrides are based on URL, and take the form of:

`["site.url"]["key"] = value; `

A DSN of: 

`$SYSTEM["dsn"]["pholdbox"]["connection_string"] = array("mysql:host=localhost;dbname=pholdbox", "root", "root");`

Could be overridden by: 

`$SYSTEM["pholdbox.local.dev"]["dsn"]["pholdbox"]["connection_string"] = array("mysql:host=localhost;dbname=pholdbox", "local", "pw");`

### Example Config
```
<?php
/*
 * Created on Dec 29, 2010
 *
 * PholdBox Config
 */
 
$SYSTEM = array();
$GLOBALS["SYSTEM"] = &$SYSTEM;

//-- Production values --
//Debug Output
$SYSTEM["debug"] = true;

//Datasources
$SYSTEM["dbBatchSize"] = 100;
$SYSTEM["dsn"] = array();
$SYSTEM["dsn"]["default"] = "pholdbox";

// Add as many datasources as you like
// The connection string is an array that takes the PDO connection string, username, and password
// PDO DB syntax: array("dbtype:host=<hostname>;dbname=<dbname>", "username", "password")
$SYSTEM["dsn"]["pholdbox"] = array();
$SYSTEM["dsn"]["pholdbox"]["connection_string"] = array("mysql:host=localhost;dbname=pholdbox", "root", "root");

//Default Layout/view
$SYSTEM["default_layout"] = "layout.main";
$SYSTEM["default_event"] = "main.home"; 

//per app settings go here
$SYSTEM["app"]["mysetting"] = "PholdBox Rocks!";

//-- Site Specific Configs --
//example Site Specific: Override any setting by prepending the hostname
$SYSTEM["test.local.dev"]["dsn"]["pholdbox"]["connection_string"] = array("mysql:host=localhost;dbname=pholdbox", "someother", "credentials");
```
# Request Collection (rc)
The Request Collection is an array, or collection of variables to be used by the Handlers, Layouts, and Views.

## What is IN the RC?
The very basic incarnation of the Request Collection is everything that was passed to the application through a GET or POST.  In the event of variable collision, the POST variables always win.  The user can also put whatever information they like into the request collection, and it is encouraged to do so.

As mentioned above, the request collection is available to the application at the handler level, layout level, and view level.  At any point, any one of those objects can access something out of the collection to use or display.  It is encouraged to use your model objects to gather data, your handler objects to collect that data into the request collection, and finally for your views and layouts to consume this data trough the request collection.  By following this model, you can swap out any piece of the MVC layer without the other two caring about it.

## How Do I Access the RC?
From within an event, you can use `$this->getValue("key")`, or `$this->setValue("key")` to interact with the RC.  From within a Layout or a View, simply call `<?php echo $rc["MyData"]?>` to access a key.  You can do more complex things here if you like.

# Handlers
Handlers are the main controller and nerve center of PholdBox.  Every request that hits a PholdBox application must be routed through a handler.  

### What is a Handler?
A handler is an object that extends the system/Event object.  This object gives you some pretty nice utility, including:

* IOC support
* Request Collection scope handling

You can think of a Handler as an endpoint to your application, a client facing page if you will.  It is how the world interacts with your site.

For example, let's assume you have a default event defined as main.home.  What this means to PholdBox is that you have a class(file) called Main in your handlers directory, and you want to call the "home" function.  The URL would look like:

`http://mysite.com/?event=main.home`

As you can probably guess, to access other methods in the file, you simply need to change "home" to the name of the function you wish to call:

`http://mysite.com/?event=main.myFunction`

You can also nest things in the handlers directory.  To access a handler in `/handlers/subhandler/leaf` the call looks like:
`http://mysite.com/?event=subhandler.leaf.myFunction`

### Basic Function List: 
| Function | Definition | 
| ------ |------ |
| getValue($key) | Gets a value from the RC |
| setValue($key) | Sets a value to the RC |
| preEvent()     | Event that fires BEFORE the specified event |
| renderView()   | Renders the current view in the current layout |
| runEvent($event) | Fires an additional event (location: ?event= $event) |
| setLayout($layout) | Defines a layout (layouts/$layout.php -- layouts/mainLayout.php) |
| setView($view, $useLayout=true) | Defines a view (views/$view.php - views/home.php). $useLayout can be passed to suppress rendering the view in a layout (render the view directly - this is useful for returning JSON strings |

### Ok, What's In These User Defined Functions? 
The functions themselves is where you can do things like interact with the Request Collection, interact with model objects to prepare the view, set the view, and set the layout for display.

A very basic main.home would do nothing but render a View in the default Layouts:
```
class Main extends system\Event
{
    public function home()
    {
        $this->setView("home");
        $this->renderView();
    }
}
```
What this does is set the View to "home", which instructs PholdBox to go find a view (template) named home.php in the /views folder of your site, and load it inside of the default layout (defined in config.php)

A slightly more complex example would be to specify a Layout:
```
class Main extends system\Event
{
    public function home()
    {
        $this->setView("home");
        $this->setLayout("myLayout");
        $this->renderView();
    }
}
```
As you might have guessed, this works very similarly to how views work.  The code above would look for **/views/home.php** and try to load it inside of **layouts/myLayout.php**

### The preEvent()
The preEvent is a special function that is called before any other events are fired.  In the base class, this function is blank, so to do anything, you need to define it in your subclass.

```
class Main extends system\Event
{
    public function home()
    {
        $this->setView("home");
        $this->setLayout("myLayout");
        $this->renderView();
    }
   
    public function preEvent()
    {
        //Do cool pre stuff here.
    }
}
```
In this example, when `?event=main.home` was called, before the home() function is fired, the system would call the preEvent() function defined above.  Whatever that "cool pre stuff" is that you want to do, you would plop the code here and then home() would execute.  I've typically used this for access checks (don't run anything in this file unless you've got a session, things of that nature).  It is a handy way to encapsulate logic that you want to fire against ALL functions in your handler.

### runEvent()
runEvent is a utility function designed to fire a handler from another handler.  Right now, it simply redirects the browser to a specified function, but I want to update it to actually be able call the event in the background if needed as well.  

For example, say you have a call to `?event=home.secureArea`.  Since you want to restrict this to only logged in users, you can easily control this by using preEvent and runEvent together.  In this example, your preEvent() would check to see if you were logged in and if not, would fire a call to runEvent("main.home"); to safely redirect the user back to the main login screen. 

### RC Functions ###
As mentioned above, getValue() and setValue() are there to allow you to easily interact with the Request Collection.  Go to that section to learn more about what the Request Collection is and what it can do for you.

# Views
Views are the basic view layer of PholdBox.  These are standard PHP pages that are included into the layout (or in the case of `$useLayout=false`, rendered directly).  Given that, you should still (as best practices) avoid doing any kind of data processing in the view files.  Any data manipulation should be handled in the Handlers, and the result passed to a structure the view can access (preferably the Request Collection scope).

### How Do I Use a View? ###

To invoke a view, from a handler you just need to call `$this->setView("<viewname>")`.  If you pass "home" in as the view name, the system will look for a file called `/views/home.php`.  Likewise, if you had a theme or more complicated directory structure, you could pass in "mytheme/default/home" to setView, and it would load `/views/mytheme/default/home.php`.

### What Does A View Look Like? ###

Here is an example view:

```
#!php
<h2>This is a test</h2>

<p>
This is what I stored: <?php echo $rc["MyVar"]?>
</p>
```

As you can see, a view for the most part is a simple HTML page, with hooks to insert values of stored variables.  These variables can be anything from simple text strings, to entire generated blocks of HTML for different areas of your page.  

The contents of a view are rendered inside of a specific section of a Layout in a much similar way to how variables are inserted into a view (see the layout page for more details)


# Layouts
Layouts are the basic skeleton to your site where data is rendered.  Like views, they are loaded by name:

`$this->setView("<viewname>");`

By specifying "layout.main" to this function, it will look for a file called `/layouts/layout.main.php`.  

### What Is a Layout? ###

This is an example layout:

```
#!php
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>PholdBox - <?php echo $rc["PB_VERSION"]?></title>
		<style>
			@import "includes/styles/styles.css";
		</style>
	</head>
	<body>
		<div id="content-container">
			<div id="header">
				<h1>Welcome to PholdBox</h1>
				<h4>Version <?php echo $rc["PB_VERSION"]?></h1>
			</div>
			<div id="content">
				<?php include($view);?>			
			</div>
		</div>
	</body>
</html>
```

Much like views, the layouts also have access to the Request Collection scope, so anything you need pre-rendered or generated you can shove here before rendering the layout.

The `<?php include($view);?>` statement is where the pre-selected view is loaded in the layout.

# to be documented... (documented better at least)
* Models: see objects in the models folder of the skeleton project for examples
* IOC: Look at the example handler for how to add things in to the IOC array
  * TL;DR: things in the ioc array are loaded from the model folder
  * you can load them via `$this->Instance['modelname']`
* Phorm: see system/PhORM for some phorm docs
  * Access table columns via `$obj->getColName()` and `$obj->setColName('value')`
  * save with `$obj->save()`
    * if you pass save an array of objects, it does a bulk save. Must be like objects
  * load with `$obj->load()`
    * if you give an id, it will load that specific ID (all tables must have an id)
    * if you set any other value on the object, it does an AND lookup
* Clustering & Session management
  * clustering works via storing the session on the server
  * see `model/test/MyObj.php` for an example of how to use `getSessionValue` and `setSessionValue`
    
