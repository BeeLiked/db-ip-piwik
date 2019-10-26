# BeeLiked DB-IP Matomo (Piwik)

## Description

This non-official DB-IP plugin adds an extra Location Provider to Geolocation, providing better accuracy for user location lookup in your Matomo (Piwik) visitor log.

It uses the free API by default which allows a quota of up to 1000 requests per day. For more requests, a commercial DB-IP API Key must be purchased. More details at: https://db-ip.com



## Installation / Update

See http://piwik.org/faq/plugins/#faq_21

## FAQ

__How to I configure the plugin?__

After installed, login as administrator.

- Go to __System__ > __DB-IP__ to configure your __API Key__.
- Go to __Geolocation__ to enable __BeeLikedDBIP__ as a __Location Provider__.


__How can I increase the limit of queries per day?__

Purchase a commercial API Key on https://db-ip.com and insert it in the DB-IP settings page.


__What location data can this plugin retrieve?__

This plugin currently supports the following properties: Continent, Country, Region and City.



## Change Log

__2.0.3__
* Add support to Core API by adding ISP, Organization, ZipCode, Latitude, Longitude and Area Code fields

__2.0.2__
* Improves documentation
* Improves translations
* Fixes bug when leaving the API Key empty

__2.0.1__
* First release for Piwik 2.0
* Retrieve basic DB-IP API fields: Continent, Country, Region and City
 ​
## License

GPL v3 / fair use



## Support

Please contact us at support@beeliked.com or check out the Github repository: https://github.com/BeeLiked/db-ip-piwik
