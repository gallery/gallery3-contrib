<?php 

/**
 * RabidRatings - Simple and Pretty Ratings for Everyone
 * <http://widgets.rabidlabs.net/ratings>
 * 
 * See CONFIGURATION below.
 *
 * @author Michelle Steigerwalt <http://msteigerwalt.com>
 * @copyright 2007 Michelle Steigerwalt
 * @license LGPL 2.1 <http://creativecommons.org/licenses/LGPL/2.1/>.
 */

class RabidRatings { 

	/* Edit the lines below to configure this widget. */
	function configuration() { 

///////////////////////////////////////////////////////////////////////////////
//////////                       CONFIGURATION                       //////////
///////////////////////////////////////////////////////////////////////////////

    	/* Database Connection Options */
#		$this->dbHost     = $dbhost;          //The host to connect to.
#		$this->dbUser     = $dbuser;          //The username to login as.
#		$this->dbPassword = $dbpass;          //The database password.
#		$this->dbDatabase = $dbdatabase;      //The database to utilize.
#		$prefix = $dbprefix;                  //The prefix to the db table.


/** gallery3 config stuff **/

if(isset($_GET['votestring'])){
  $this->votestring = $_GET['votestring']; 
} else {
  $this->votestring = module::get_var("ratings","votestring");
}
if(isset($_GET['imageword'])){
  $this->imageword = $_GET['imageword'];
} else {
  $this->imageword = module::get_var("ratings","imageword");
}

if(isset($_GET['varpath'])){
  $varpath = (string)$_GET['varpath'];
} else {
  $varpath = VARPATH;
}
if(isset($_GET['modpath'])){
  $modpath = (string)$_GET['modpath'];
} else {
  $modpath = MODPATH;
}

if(!file_exists($varpath."modules/ratings/db.settings.php")){
  include($modpath."ratings/vendor/ratings_db.php");
}
if(!isset($this->dbUser)){
  include($varpath."modules/ratings/db.settings.php");
}

/** end gallery3 config stuff **/



		$this->ratables = $prefix."ratables"; //The name of the ratables table.
		$this->ratings  = $prefix."ratings";  //The name of the ratings table.

		//The position of the vote stat text.  Can be "before", "after", or 
		//false.
		$this->textPosition = "after";

		//If allowDuplicates is set to true, people can vote multiple times
		//for the same item. NOTE: This has to be set before the table is
		//created.  To change the value, you must first DROP all related
		//tables so they can be recreated. (Or you can be a dirty hack and
		//change $ip in doVote to rand(1,1000) or something.)
		$this->allowDuplicates = false;

		//The total number of stars in the scale (ie, 5).
		$this->stars = 5;

///////////////////////////////////////////////////////////////////////////////
//////////      END OF CONFIGURATION: Do Not Modify Below Lines      //////////
///////////////////////////////////////////////////////////////////////////////

	}

	function RabidRatings() {
		$this->configuration();
		$this->initializeDatabase();
	}

	/**
	 * RabidRatings::showStars
	 * Outputs the HTML code for a ratings box, including the current score (if
	 * any) of the item being rated.
	 * 
	 * @param string ratableKey - The unique idenifier for your rateable item.
	 *     ratableKey should be made up in whatever manner works best for the
	 *     site using it. 
	 */
	function showStars($ratableKey) {
		$rating = $this->loadRating($ratableKey);
		$ratingId = $rating['keyID'];
		$stars = $this->percentToStars($rating['rating']);
		$percent = round($rating['rating'], 0)."%";
		if ($rating['totalRatings'] == 0) { $unratedClass = " ratingUnrated"; }

		$textDesc = "<div id=\"rabidRating-$ratingId-description\" class=\"ratingText\">"
					.$this->getStarMessage($rating)."</div>";

		if ($this->textPosition == "before")     { $beforeText = $textDesc; }
		else if ($this->textPosition == "after") { $afterText  = $textDesc;  }

		/* Here's where you would mess with the code to modify the template. */
		echo "<div id=\"rabidRating-$ratingId-$stars"."_$this->stars\" "
			 ."class=\"rabidRating$unratedClass\">"
			 .$beforeText
			 ."<div class=\"wrapper\">"
			 ."<span class=\"ratingFill\" style=\"width:$percent;\">"
			 ."<span class=\"ratingStars\"></span>"
			 ."</span></div>".$afterText."</div>";
	}

	function getStarMessage($rating) {
		$stars = $this->percentToStars($rating['rating']);
		if ($rating[totalRatings] > 1) $s = "s";
		if ($rating[totalRatings] < 1) $s = "s";
		$this->imageword .= "s";
		return "$stars/$this->stars $this->imageword ($rating[totalRatings] $this->votestring$s)";
	}

	/**
	 * RabidRatings::doVote
	 *
	 * This is the function in charge of handling a vote and saving it to the
	 * database.
	 *
	 * NOTE: This method is meant to be called as part of an AJAX request.  As
	 * such, it unitlizes the die() function to display its errors.  THIS
	 * WOULD BE A VERY BAD FUNCTION TO CALL FROM WITHIN ANOTHER PAGE.
	 *
	 * @param integer id      - The id of key to register a rating for. 
	 * @param integer percent - The rating in percentages.
	 */
	function doVote($ratableId, $percent) {
		$ip = $_SERVER['REMOTE_ADDR'];


// G3 HACK - ONLY ALLOW REGISTERED USERS TO VOTE
		if(isset($_GET['regonly'])){
		    if($_GET['regonly'] == 1){
		      die("ERROR: Only registered users can vote");
		    }
		}

		//Make sure that the ratable ID is a number and not something crazy.
		if (is_numeric($ratableId)) {
			$id = $ratableId;
		} else {
			die("ERROR: Id invalid.");
		}

		//Make sure the percent is a number and under 100.
		if (is_numeric($percent) && $percent < 101) {
			$rating = $percent;
		} else {
			die("ERROR: Rating percent invalid.");
		}

		//Insert the data.

/**
 * PATCH THIS TO CHECK FOR USERID (IF REGONLY ON)
 * AND DIE FOR DUPLICATE
 *
 * JAMES
 *
 *
 */

if(isset($_GET['userid'])) $userid = $_GET['userid'];
if(isset($_GET['regonly'])) $regonly = $_GET['regonly'];

if($regonly){
  $SQL = "SELECT * from $this->ratings where 'userid' = $userid";
  if(mysql_query($SQL)){
    die("ERROR: Duplicate votes not allowed. (same userid)");
  }
} else {
  $SQL = "SELECT * from $this->ratings where 'ip_address' = $ip";
  if(mysql_query($SQL)){
    die("ERROR: Duplicate votes not allowed. (same ip)");
  }
}

		$SQL_INSERT_VOTE = "INSERT INTO $this->ratings(ratable_id, ip_address, ".
		    "rating, userid) VALUES ('$id', '$ip', '$rating','$userid');";

		//Die with an error if the insert fails (duplicate IP for a vote).
		if (!mysql_query($SQL_INSERT_VOTE)) {
			die("ERROR: Duplicate votes not allowed.");
		}

		$rating = $this->loadRating($id);
		echo $this->getStarMessage($rating);
	}

	/**
	 * RabidRatings::initializeDatabase
	 *
	 * Connects to the database, returning false on failure.
	 * NOTE: It's my intention here to be as unobtrusive as possible upon a
	 * failure, since nobody likes their entire page dying and throwing ugly
	 * errors just because a minor widget fails.
	 */
	 function initializeDatabase() {
		error_reporting(1);

/* GALLERY3 PATCH */

if(!isset($this->dbUser)){
  include($varpath."modules/ratings/db.settings.php");
}

/* END GALLERY3 PATCH */

		$this->dbConnection = mysql_connect($this->dbHost, $this->dbUser,
			$this->dbPassword);
		if (!mysql_select_db($this->dbDatabase)) {
			echo("RabidRatings couldn't connect to the database. Please make "
			."sure your configuration is correct.");
		} else {
			$this->ensureTables();
		}
	}

	/**
	 * RabidRatings::ensureTables
	 * Creates the necessary database tables, should they not  exist.
	 */
	function ensureTables() {

		if (!$this->allowDuplicates) {
			$unique = ", UNIQUE KEY `ratableKey` (`ratableKey`)";
		}
	
		$SQL_CREATE_RATABLES = "CREATE TABLE $this->ratables (
			`id` int(11) NOT NULL auto_increment,
			`ratableKey` varchar(50) NOT NULL,
			`created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
			PRIMARY KEY  (`id`) $unique
		) AUTO_INCREMENT=1 ; ";

		$SQL_CREATE_RATINGS = "CREATE TABLE $this->ratings (
			`id` int(11) NOT NULL auto_increment,
			`ratable_id` int(11) NOT NULL,
			`ip_address` varchar(50) NOT NULL,
			`rating` int(11) NOT NULL,
			`userid` int(9) NOT NULL,
			`timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
			PRIMARY KEY  (`id`),
			UNIQUE ( `ratable_id` , `userid`),
			CONSTRAINT rabid_ratings_fk FOREIGN KEY (`ratable_id`) REFERENCES
			$this->ratables(id)
		) AUTO_INCREMENT=1 ;";
		
		$result = mysql_query("SHOW TABLES;");
		while ($table = mysql_fetch_array($result)) {
			if ($table[0] == $this->ratables) { $ratablesFound = true; }
			if ($table[0] == $this->ratings)  { $ratingsFound = true; }
		}
		if ($ratablesFound != true) { mysql_query($SQL_CREATE_RATABLES); }
		if ($ratingsFound  != true) { mysql_query($SQL_CREATE_RATINGS);  }
	}

	function loadRating($ratableKey) {
		if (is_numeric($ratableKey)) $crit = "$this->ratables.id"; 
		else $crit = 'ratableKey';
		$WHERE = "WHERE $crit = '$ratableKey'"; 
		$SQL_GET_RATINGS = "SELECT $this->ratables.id AS keyID,
		AVG( $this->ratings.rating ) 
		AS rating, COUNT( $this->ratings.rating ) AS totalRatings
		FROM $this->ratables
		JOIN $this->ratings
		ON ( $this->ratables.id = $this->ratings.ratable_id ) $WHERE
		GROUP BY $this->ratables.id;";
		$result =  mysql_fetch_array(mysql_query($SQL_GET_RATINGS), MYSQL_ASSOC);
		if ($result == null) { $result = $this->createNewKey($ratableKey); }
		return $result;
	}

	function createNewKey($key) {
		if (is_numeric($key)) { return false; }
		$SQL_INSERT_KEY  = "INSERT INTO $this->ratables(ratableKey) "
		."VALUES ('$key');";
		$SQL_GET_NEW_KEY = "SELECT id AS keyID FROM $this->ratables WHERE "
		."ratableKey = '$key';";
		mysql_query($SQL_INSERT_KEY);
		$newKey = mysql_fetch_array(mysql_query($SQL_GET_NEW_KEY), MYSQL_ASSOC);
		$newKey['rating'] = 0;
		$newKey['totalRatings'] = 0;
		return $newKey;
	}

	function percentToStars($percent) {
		$modifier = 100 / $this->stars;
		return round($percent / $modifier, 1);
	}

}

/* The code below handles ratings sent if the $_POST variables are set. */
if (isset($_POST['vote']) && isset($_POST['id'])) {
	$r = new RabidRatings();
	$r->doVote($_POST['id'], $_POST['vote']);
};

?>
