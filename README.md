# mikit_wrapper_php
This is a PHP wrapper around the SEDNA MIKIT API - https://github.com/sednasystems/sedna-mikit-sample-node

The purpose is to simplify creating MIKIT applications.

The MIKIT application will live at the endpoint specified in the Sedna MIKIT application setup. When Sedna opens a MIKIT sidebar, and http call will be made to the application.

The text output from the application will be sent back to the caller.

Note - only MIKIT syntax to be returned (no echoing for debugging, etc).

Example
-------
```php

function hello_world() {

	//To read POST data
	$post_data = file_get_contents('php://input'); //Read the post data
	$post_data = json_decode($post_data,true); //Change to PHP array

	//Create an mikit object with a url for the top right icon
	$url = 'http://app.sednanetwork.com/mikit/2020-07-07/definitions.schema.json';
	$mikit = new mikit_api($url);

	//Create a text display - type header
	$hello_world_text = $mikit->textDisplay('Hello there World','heading');

	//Add to the mikit object
	$mikit->add_to_blocks($hello_world_text);

	//Return the mikit object (json)
	return $mikit->render();
}
```

Output
------
```json
{
   "version":"2020-07-07",
   "trace":"",
   "href":"http:\/\/app.sednanetwork.com\/mikit\/2020-07-07\/definitions.schema.json",
   "blocks":[
      {
         "type":"textDisplay",
         "style":"heading",
         "value":"Hello there World"
      }
   ]
}
```
