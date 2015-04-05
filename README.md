# Game Rankings

## Github Pages
You can view the Github page here: https://loganrickert.github.io/PHP-Game-Ranking/

## Setting up SQL database
The SQL was written for MySQL. To set up the database from PHPMyAdmin, goto PHPMyAdmin, click on the 'SQL' tab, and paste in the contents of the file DatabaseCreate and press go in the bottom right-hand corner.

To enter the SQL information, goto src/Constants and change the information:

```
define("DB_HOST","localhost");
define("DB_NAME","mike");
define("DB_USER","root");
define("DB_PASSWORD","");
```

To give an account admin status, go to the database 'groups' and create two groups, a default group and an admin group. Go to 'players' and set the group_id = the group_id of the admin group. Currently, you can only do this manually.

If the admin group id is not 2, please update the src/Constants file.

```
// The admin group
define("ADMIN_GROUP", 2);
```

## Basics
#### Defaults
* You can create accounts
* You can log into accounts
* You can log out of accounts
* You can create a team
* You can leave a team
* You can join a team
* You can view a team page which has the team name, points, and list of team mates
* You can view a player page which has their name and what team they are apart of.
* If you create a team, you are the leader of the team
* If the team is empty and you join, you are the leader of the team
* If you are the team leader, you can kick people from the team
* If you are the team leader, you can promote another team member to leader (You lose your leader position).
* The site uses a .htaccess that uses clean URLs.

#### Admins
* Admins can kick anyone from any team and promote anyone to team leader
* Admins can a list of events and change their name, password or point amount (view events.php)
* Admins can create a new event

## Walk Through
The website is written with PHP classes. There are three main classes: Player, Event and Team.

```
private $teamId; 		// The identification number for the team. This number is unique.
private $teamName; 		// The name of the team. This is unique.
private $teamPoints;	// The number of points a team has. This row is no longer used.
						// Instead, points_obtained where team_id = teamId are summed together.
private $teamLeader;	// Indicates who the team leader is by playerId.
private $teamStatus;	// Not yet used for anything. Thinking about is_banned, is_playing, etc.
```

```
private $playerId;		// The identification number for a player. This number is unique.
private $playerName;	// The name of the player. This name is unique.
private $playerEmail;	// Email of the player. This email is a valid email. This email is unique. This field currently has no uses.
private $playerPassword;// The hashed password of the player. Hashed using bcrypt.
private $teamId;		// The id of the team the player is on. teamId 0 means no team.
private $playerStatus;	// The Not yet used for anything. Thinking about is_banned, is_playing, etc.
private $groupId;		// The group id of the player.
```

```
private $pointId;		// The identification of the event
private $pointPassword;	// The password that corrisponds to the event. This password is stored in plain text.
private $pointAmount;	// The amount of points the event is worth
private $pointEvent;	// The id of the overall event that the site is currently on. Integer value.
private $eventName;		// The name of the event.
```

Create Event:
To create a new event, first create an account. Manually set the group_id for that user to the group_id of the admin group inside of the SQL database. Back at the website, at the very top right hand corner, click on 'Events'. If any events have already been created, you will see them listed here. You can very easily edit any event and click the submit button to save the changes. Click on 'create new event'. Type in the information. Once done, click submit. Your event has now been added to the unlockable event list.

Unlock Event:
To unlock an event, go to the sub header bar and click on the 'enter password' input box. Type in a password and press enter. If the password is in the events list and the team you are in has not already activated the password, the team will unlock the event. This will add a row into the points_obtained table, recording which event was unlocked, by whom, by which team, and the unix ephoch time it was unlocked.

To create an instance of a class, do the following:

```
$player = new Player($playerId, $playerName, $playerEmail, $teamId, $playerStatus, $groupId);
```

To get information from the class, you must call a getter. To do this do:

```
$player->getPlayerId();
```

Note: Some getters may not be there as I haven't needed them. Just copy / paste an existing one if you find yourself needing one.

There is almost no need to create a player or team instance manually. You can just call ```$db->loadPlayer($playerId)``` or ```$db->loadTeam($teamId)``` and it will return an instance ready to use.

```
$db = new Database();
$player = $db->loadPlayer($playerId);
```

There are a lot of functions in the Database class. Most could be removed and probably will be. Right now every function is being used somewhere but I am going to remove functions that don't need to be there and can be done using the loadPlayer or loadTeam.

All HTML is inside the Html class. Please do not place any HTML inside of any other classes.

The following is an example of a page that displays the header, footer and displays a simple message:

```
<?PHP

include './src/Constants.php';
include './autoloader.php';

$html = new Html("Simple Message");

$html->printHeader();

echo "Hello, how are you!";

$html->printFooter();
```

The autoloader is a class that automatically loads a class when you use it. Without this, you would have to include every php class file every time. The literal passed into the HTML class is what the title of the HTML page will be set to.

Only two things are stored in SESSION: playerName and playerId.

## Naming Conventions
PHP files that output HTML should be lowercase with underscores for spaces. PHP files that are used as scripts should be camel casing. PHP varibles should be camel casing. SQL names should be lowercase with underscores for spaces. PHP classes should start with an uppercase and be camel casing. Here are some examples:

```
$thisIsANumber
deleteAllUsers.php
view_all_users.php
MySuperCoolClass.php
player_coolness (SQL name)
```

## Plans
Not too sure yet.