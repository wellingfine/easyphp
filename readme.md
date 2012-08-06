edit by welling
2012-06-14

1.framework code only goes in lib/ ,easy to upgrade if new version is published.
2.config file is small and nothing to do with other app.
3.in public/ a PHP file is an app if it create an E object ,it takes the requests and dispatches to specify controller in apps/

./
	+apps
		+index
			+controller
			+model
			+view
				+controllername
					actionview.php
				xxxview.php
			+logs all kinds of logs 	
			db.php
			acl.php
			config.php app's config,can overwrite the globalconfig
			routes.php
		+other_apps...
	+model public model component
	+view public view component
	+lib framework dir
		+cache 
			cache.php
		+template 
		+core about MVC
			log.php
			controller.php
			view.php
			rbac.php
			route.php
		+db about db
			db.php
		main.php the main entrance
		classes.php necessary class include path
	globalconfig.php
	+public
		html,js,css
		index.php
		otherapp.php
		
	