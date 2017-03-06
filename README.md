## Spot2KML

*Convert SPOT's API data to KML for live updating in Google Earth.*


#### Requirements

* Web server (e.g. Apache or NGINX)
* PHP 5.3+
* A [SPOT account](https://www.findmespot.com) with a [shared page](https://faq.findmespot.com/index.php?action=showEntry&data=236) and a valid [GlID](https://faq.findmespot.com/index.php?action=showEntry&data=69).

#### Installation

1. Copy spot2kml.kml.php to your webserver and remove the .php file extension.

    ```bash
    $ mv spot2kml.kml.php spot2kml.kml
    $ ls
    README.md SPOT_Network_Link.kml spot2kml.kml
    ```
    
* NOTE:  Your webserver must be explicitly set-up to execute this KML file as PHP.  In Apache, you can use the included .htaccess file.  In NGINX, you'll need to write a specific location block.

2. Edit lines ```40``` and ```42``` of ```spot2kml.kml```, replacing the GlID with your own GlID (from your SPOT shared page link).

3. Copy ```SPOT_Network_Link.kml``` to a convenient location on your computer; this is the file you will open in Google Earth.

4. Edit line 7 of ```SPOT_Network_Link.kml``` to reflect the URL for the ```spot2kml.kml``` file on your webserver, such as

    ```html 
    <href>https://www.example.com/spot2kml.kml</href>
    ```
    
5. Open the ```SPOT_Network_Link.kml``` file in Google Earth.  If your SPOT has transmitted a message since you created your shared page on the SPOT website, you should see it populate in Google Earth.  If not, check your GlID for accuracy and ensure that you can see messages on the shared page you created on the SPOT website.

#### Author

Austin [email](mailto:insdavm@gmail.com)

