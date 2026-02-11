OroCommerce Geo Detection Bundle
===============================================

Facts
-----
- version: 6.0.0
- composer name: aligent/oro-geo-detection

Description
-----------
This bundle provides users with a pop-up redirect to an alternative website if they are visiting your site from outside the selected region. It will also add a dropdown to the menu to allow users to switch to alternative websites.

Installation Instructions
-------------------------
1. Install this module via Composer

        composer require aligent/oro-geo-detection

1. Clear cache

        php bin/console cache:clear --env=prod

Set up Instructions
-----------
Head to System > Configuration > Aligent > Geo Detection 

Enable Geo Detection and add as many countries and alternative URLs as you like. 

Support
-------
If you have any issues with this bundle, please feel free to open [GitHub issue](https://github.com/aligent/oro-geo-detection/issues) with version and steps to reproduce.

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Adam Hall <adam.hall@aligent.com.au>

License
-------
[GPL-3.0](https://opensource.org/licenses/GPL-3.0)

Copyright
---------
(C) 2021 Aligent Consulting
