THINGS TO REMEMBER
  * '$composer dump-autoload' reloads autoload, use after adding files to namespaces

# TODO
  ## BackEnd
    1. Sanitize form inputs (mysql injections)
    2. Pull ETF Funds (exchange-scrapper)
    3. Create JSON files for user to build profile
    4. Use PHP-JWT (web tokens) for session logins
    5. ~~Add IPO date to the company database.~~
      * Using IPOMonitor.com, some IPO dates were not found
    6. Build Admin panel for keyword matching
      * Match keywords generated from transactions to sectors.
      *
    7. Allow custom keywords to be inserted into keyword json
    8. Allow companies to have custom json for specific keywords
      * will search these once we have implemented custom user keywords
    9. Hash passwords, currently stored in plain text
      * How to connect passwords to yodlee DB???, hash before registering?
    10. Make a trade-life/src/api/data class to display tabless
  ## App
    1. Install Keyring, so user does not have to enter credientials every time
    2. Add ability to login with stored info during the splash screen

### 7.11.2017
    * Fixed issues with server. reload public key to fix in future
    * Began breaking down quayledbconnect into api. Continue this tomorrow
### 7.12.2017
    * Broke down quayledbconnect and renamed to taquayle/trade-life
    * Hooked into transaction database.
      * Simple_Desc, Original_Desc, Category on loan/insurance still wonky
### 7.13.2017
    * Fixed simple_desc, original_desc issues. bind param needs 'fresh' variables
    * Commented Transaction php file.
    * Commented User php file.
    * Parsed NASDAQ and NYSE
### 7.14.2017
    * Started Profile.php, currently parses 1 column
      * Need to add ability to parse mulitple arrays together.
### 7.17.2017
    * Decided to split parsing between DESC words and Sector words
    * Moved testing php files into public/tests.
    * Changed index.php to point to testsuite.php
    * Added market cap and price to companies DB.
    * Parsed user information for weighted keywords for users
    * Built a parse for companies. don't know if it'll do any good
### 7.18.2017
    * Split profile.php up a bit, separating out category and descriptions
    * Added the ability to look for direct matches from DESC for stock matches
    * Added ability to look for stats by looking weighting lookups
    * TODO 5
### 7.19.2017
    * Added in IPO lookups using ipomonitor.com, only goes to 2003.
      * Used after using NASDAQ csv, updates already inserted companies
    * Added ability to download company data to server and only redownload once per day
    * Broke up ExScrape/Data into Data + Keyword. Keyword class will now save to file a JSON of all keywords.
    * Commented much of ExScrape
### 7.20.2017
    * Added ability to update and retrieve keywords to json file
    * added ability for Profile.php to read keyword jsons.
### 7.21.2017
    * Moved exchange-scraper and trade-life to /apps
    * Broke up profile into profile+stocks
    * Fixed Laravel web route / ENV issues
      * Started converting from php-pages to laravel-blades
### 7.24.2017
    * Changed profile.php to userprofile.php
    * Changed stocks.php to userstocks.php
    * added profile.php to insert and update profiles
    * Added Client()->buildProfile to build 1 user profile
### 7.25.2017
    * Started the arduous task of breaking up trade-life and exchange-scrapper into repository/controller style
    * Successfully made login through Repo/Controller
    * ####FINISH INSTALLING JWT
### 7.26.2017
    * Begun to make models for all relavent classes
    * Started registration screen on App
      * Screen is old, never hooked to anything.
    * Able to successfully register users.
      * Still cannot register to yodlee in sandbox mode
    * php artisan companies:update is now a function. which updates the companies from mulitple sources
    * Controllers now for
      * Registering
      * Transactions
      * Login
    * Basic tranasaction screen implemented
### 7.27.2017
    * Basic profile screen implemented.
      * Cannot figure out how to pass JSON correctly
### 7.28.2017
    * 'Finished' bare outline of profile screen
      * Need 2 seperate pieces, stocks & Keywords
        * Allow custom keywords?
    * 'Finished' bare outline of sector/stock display
      * Link to some sort of chart for current stocks.
    * Look into reactive-elements for layouts
### 8.02.2017
    * Added many screens/stores to the app over past couple days
      * Profile_Keywords
      * Profile_Stocks
      * Keywords_Add
        * Stores
          * TradeLife (server URL)
          * Profile
    * Switched to react-native-elements for UI work
### 8.08.2017
    * Missed a bunch of days.
    * Commented every file in the REST API
    * Switched to react-native-elements for ui control.
### 8.09.2017
    * Added react-native-swiper@1.5.5
    * manual patch of react-navigation for:
      * drawerLockMode
### 8.10.2017
    * Added ability to logout
    * Redid transaction page to show current layout
    * Added headers to everything
    * Added Swiper to Keyword pages.
      * Need ability to show which page you are on
      * Need ability to set index on transaction. IE come with User or
        transaction keywords
    * Continue working on stocks layout
### 8.16.2017
    * COMBINE DESC-CATE
