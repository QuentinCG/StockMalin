# StockMalin

## What is it ?

This software is a website with a database that allows you to manage stocks / customers / suppliers / purchases / sales in real time.
It is available only in French !

More informations can be found in the explanation <a target="_blank" href="https://github.com/QuentinCG/StockMalin/raw/master/stockmalin_description.pdf">PDF file (in French)</a>

<img src="https://github.com/QuentinCG/StockMalin/raw/master/img/readme_demo.png" width="800">


## IMPORTANT WARNING BEFORE INSTALLING

This project was made when I was not that good at coding (I didn't have any professional experience at this time).

It may contain a lot of issues and will be hard to maintain.

I only publish it because some people asked me access to this tool for their own use.

Moreover, this project uses a very old revision of PHP (PHP5) that may not be supported in future revision of Wamp Server and hard to maintain with Linux server.


## How to install on Windows ?

1) Install <a target="_blank" href="https://www.wampserver.com/">Wamp server</a>

2) Once installed, right click on Wamp server icon and select "PHP->Version->5.x.x" with x any value.

<img src="https://github.com/QuentinCG/StockMalin/raw/master/img/readme_php_version.png" width="600">

3) Download the <a href="https://github.com/QuentinCG/StockMalin/archive/master.zip">project</a>

4) Unzip the project in `C:/wamp/www` or `C:/wamp64/www` depending on the folder that already exist (the folder can be found by doing a right click on Wamp Server icon and select ``www folder`)

<img src="https://github.com/QuentinCG/StockMalin/raw/master/img/readme_www.png" width="600">

5) Rename the unzipped folder `stockmalin`

6) Go to URL <a href="http://localhost/phpmyadmin">http://localhost/phpmyadmin</a> and use `root` as login and nothing as password

7) Once connected in PhpMyAdmin, select `Import->Choose a file`, then select file `C:/wamp/www/stockmalin/database/create.sql` then press `Execute`

<img src="https://github.com/QuentinCG/StockMalin/raw/master/img/readme_phpmyadmin.png" width="600">

8) You can now use StockMalin by going to this URL: <a href="http://localhost/stockmalin">http://localhost/stockmalin</a>


Note: If you want specific configuration, you can edit the `C:/wamp/www/stockmalin/config.php` file.


## How to install on Linux ?

Same steps as for Windows but you have to install your own PHP5 server and SQL database instead of installing Wamp Server.



## License

This project is under MIT license. This means you can use it as you want.

