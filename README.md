# mikit_wrapper_php
This is a PHP wrapper around the SEDNA MIKIT API - https://github.com/sednasystems/sedna-mikit-sample-node

The purpose is to simplify creating MIKIT applications.

Example
-------
```php
function hello_world() {

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
