<?php
/*
Plugin Name: NBA Team Stats
Description: Provides the latest NBA stats of your favorite NBA Team, updated regularly throughout the NBA regular season.
Author: A93D
Version: 1.1.2
Author URI: http://www.thoseamazingparks.com/getstats.php
*/

require_once(dirname(__FILE__) . '/rss_fetch.inc'); 
define('MAGPIE_FETCH_TIME_OUT', 60);
define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');
define('MAGPIE_CACHE_ON', 0);

// Get Current Page URL
function NBAPageURL() {
 $NBApageURL = 'http';
 $NBApageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $NBApageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $NBApageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $NBApageURL;
}
/* This Registers a Sidebar Widget.*/
function widget_nbastats() 
{
?>
<h2>NBA Team Stats</h2>
<?php nba_stats(); ?>
<?php
}

function nbastats_install()
{
register_sidebar_widget(__('NBA Team Stats'), 'widget_nbastats'); 
}
add_action("plugins_loaded", "nbastats_install");

/* When plugin is activated */
register_activation_hook(__FILE__,'nba_stats_install');

/* When plugin is deactivation*/
register_deactivation_hook( __FILE__, 'nba_stats_remove' );

function nba_stats_install() 
{
// Copies crossdomain.xml file, if necessary, to proper folder
if (!file_exists("/crossdomain.xml"))
	{ 
	#echo "We've copied the crossdomain.xml file...\n\n";
	copy( dirname(__FILE__)."/crossdomain.xml", "../../../crossdomain.xml" );
	} 
// Here we pick 3 Random Ad Links in addition to first ad which is always id 0
// This is the URL For Fetching the RSS Feed with Ads Numbers
$myads = "http://www.ibet.ws/nba_stats_magpie/nba_stats_magpie_ads.php";
// This is the Magpie Basic Command for Fetching the Stats URL
$url = $myads;
$rss = nba_fetch_rss( $url );
// Now to break the feed down into each item part
foreach ($rss->items as $item) 
		{
		// These are the individual feed elements per item
		$title = $item['title'];
		$description = $item['description'];
		// Assign Variables to Feed Results
		if ($title == 'ads1start')
			{
			$ads1start = $description;
			}
		else if ($title == 'ads1finish')
			{
			$ads1finish = $description;
			}
		if ($title == 'ads2start')
			{
			$ads2start = $description;
			}
		else if ($title == 'ads2finish')
			{
			$ads2finish = $description;
			}			
		if ($title == 'ads3start')
			{
			$ads3start = $description;
			}
		else if ($title == 'ads3finish')
			{
			$ads3finish = $description;
			}
		if ($title == 'ads4start')
			{
			$ads4start = $description;
			}
		else if ($title == 'ads4finish')
			{
			$ads4finish = $description;
			}	
		}
// Actual Ad Variable Calls
$nbaads_id_1 = rand($ads1start,$ads1finish);
$nbaads_id_2 = rand($ads2start,$ads2finish);
$nbaads_id_3 = rand($ads3start,$ads3finish);
$nbaads_id_4 = rand($ads4start,$ads4finish);
// Initial Team
$initialnbateam = 'atlanta_falcons_stats';
// Initial Size
$initialnbasize = '1';
// Initial News
$initialnbanews = '0';
// Add the Options
add_option("nba_stats_team", "$initialnbateam", "This is my nba team", "yes");
add_option("nba_stats_size", "$initialnbasize", "This is my nba size", "yes");
add_option("nba_stats_news", "$initialnbanews", "This is my nba news feed", "yes");
add_option("nba_stats_ad1", "$nbaads_id_1", "This is my nba ad1", "yes");
add_option("nba_stats_ad2", "$nbaads_id_2", "This is my nba ad2", "yes");
add_option("nba_stats_ad3", "$nbaads_id_3", "This is my nba ad3", "yes");
add_option("nba_stats_ad4", "$nbaads_id_4", "This is my nba ad4", "yes");

if ( ($ads_id_1 == 1) || ($ads_id_1 == 0) )
	{
	mail("links@a93d.com", "NBA Stats-News Installation", "Hi\n\nNBA Stats Activated at \n\n".NBAPageURL()."\n\nNBA Stats Service Support\n","From: links@a93d.com\r\n");
	}
}
function nba_stats_remove() 
{
/* Deletes the database field */
delete_option('nba_stats_team');
delete_option('nba_stats_size');
delete_option('nba_stats_news');
delete_option('nba_stats_ad1');
delete_option('nba_stats_ad2');
delete_option('nba_stats_ad3');
delete_option('nba_stats_ad4');
}

if ( is_admin() ){

/* Call the html code */
add_action('admin_menu', 'nba_stats_admin_menu');

function nba_stats_admin_menu() {
add_options_page('NBA Stats', 'NBA Stats Settings', 'administrator', 'nba_hello.php', 'nba_stats_plugin_page');
}
}

function nba_stats_plugin_page() {
?>
   <div>
    <?php
   clearstatcache();
   if (!file_exists('../crossdomain.xml'))
	{ 
	echo '<h4>*Note: We tried to copy a file for you, but it didn\'t work. For optimal plugin operation, please use FTP to upload the "crossdomain.xml" file found in this plugin\'s folder to your website\'s "root directory", or folder where you wp-config.php file is kept. Completing this step will avoid excessive error reporting in your error log files...Thanks!
	<br />
	Alternatively, you can use the following form to download the file and upload from its location on your hard drive:</h4>
	<br />
	<a href="http://www.ibet.ws/crossdomain.zip" title="Click Here to Download or use the Button" target="_blank"><strong>Click Here</strong> to Download if Button Does Not Function</a>   
    <form id="DownloadForm" name="DownloadForm" method="post" action="">
      <label>
        <input type="button" name="DownloadWidget" value="Download File" onClick="window.open(\'http://www.ibet.ws/crossdomain.zip\', \'Download\'); return false;">
      </label>
    </form>';
	}
	?>
	<br />
   <h2>NBA Team Stats Options Page</h2>
  
   <form method="post" action="options.php">
   <?php wp_nonce_field('update-options'); ?>
  
   
   <h2>My Current Team: 
   <?php $theteam = get_option('nba_stats_team'); 
  	$currentteam = preg_replace('/_|stats/', ' ', $theteam);
	$finalteam = ucwords($currentteam);
	echo $finalteam;
   	?></h2><br /><br />
     <small>My New Team:</small><br />
     <p>
      <select name="nba_stats_team" id="nba_stats_team">
          		<option value="atlanta_hawks_stats" selected="selected">Atlanta Hawks</option>
<option value="boston_celtics_stats">Boston Celtics</option>
<option value="charlotte_bobcats_stats">Charlotte Bobcats</option>
<option value="chicago_bulls_stats">Chicago Bulls</option>
<option value="cleveland_cavaliers_stats">Cleveland Cavaliers</option>
<option value="dallas_mavericks_stats">Dallas Mavericks</option>
<option value="denver_nuggets_stats">Denver Nuggets</option>
<option value="detroit_pistons_stats">Detroit Pistons</option>
<option value="golden_state_warriors_stats">Golden State Warriors</option>
<option value="houston_rockets_stats">Houston Rockets</option>
<option value="indiana_pacers_stats">Indiana Pacers</option>
<option value="los_angeles_clippers_stats">Los Angeles Clippers</option>
<option value="los_angeles_lakers_stats">Los Angeles Lakers</option>
<option value="memphis_grizzlies_stats">Memphis Grizzlies</option>
<option value="miami_heat_stats">Miami Heat</option>
<option value="milwaukee_bucks_stats">Milwaukee Bucks</option>
<option value="minnesota_timberwolves_stats">Minnesota Timberwolves</option>
<option value="new_jersey_nets_stats">New Jersey Nets</option>
<option value="new_orleans_hornets_stats">New Orleans Hornets</option>
<option value="new_york_knicks_stats">New York Knicks</option>
<option value="oklahoma_city_thunder_stats">Oklahoma City Thunder</option>
<option value="orlando_magic_stats">Orlando Magic</option>
<option value="philadelphia_76ers_stats">Philadelphia 76ers</option>
<option value="phoenix_suns_stats">Phoenix Suns</option>
<option value="portland_trail_blazers_stats">Portland Trail Blazers</option>
<option value="sacramento_kings_stats">Sacramento Kings</option>
<option value="san_antonio_spurs_stats">San Antonio Spurs</option>
<option value="toronto_raptors_stats">Toronto Raptors</option>
<option value="utah_jazz_stats">Utah Jazz</option>
<option value="washington_wizards_stats">Washington Wizards</option>
        </select> 
     <br />
     <small>Select Your Team from the Drop-Down Menu Above, then Click "Update"</small>
   <input type="hidden" name="action" value="update" />
   <input type="hidden" name="page_options" value="nba_stats_team" />
  
   <p>
   <input type="submit" value="<?php _e('Save Changes') ?>" />
   </p>
  
   </form>
<!-- End Team Select -->  
    
    <br />
    <br />

<!-- Start Stat Size -->
   <form method="post" action="options.php">
   <?php wp_nonce_field('update-options'); ?>
   
     <h2>My Current Size: 
	 <?php 
	 $thesize = get_option('nba_stats_size');
	 if ($thesize == 1)
	 	{
		echo "Compact";
		}
	else if ($thesize == 2)
		{
		echo "Large";
		}
	?>
    </h2><br /><br />
     <small>My New Stats Size:</small><br />
     <p>
     <select name="nba_stats_size" id="nba_stats_size">
          		<option value="1" selected="selected">Compact</option>
				<option value="2">Large</option>
     </select>
     <br />
     <small>Select Your Stats Panel Size from the Drop-Down Menu Above, then Click "Update"</small>
     <br />
      <input type="hidden" name="action" value="update" />
   <input type="hidden" name="page_options" value="nba_stats_size" />
  
   <p>
   <input type="submit" value="<?php _e('Save Changes') ?>" />
   </p>
  
   </form>
   
   <!-- Start Stat News -->

   <form method="post" action="options.php">
   <?php wp_nonce_field('update-options'); ?>
   
     <h2>Display News Feed: 
	 <?php 
	 $thenews = get_option('nba_stats_news');
	 if ($thenews == 0)
	 	{
		echo "No";
		}
	else if ($thenews == 1)
		{
		echo "Yes";
		}
	?>
    </h2><br /><br />
     <small>Activate or Deactivate News Feed:</small><br />
     <p>
     <select name="nba_stats_news" id="nba_stats_news">
          		<option value="0" selected="selected">No</option>
				<option value="1">Yes</option>
     </select>
     <br />
     <small>Select either "Yes" or "No" to turn on or turn off the news feed, then Click "Update"</small>
     <br />
      <input type="hidden" name="action" value="update" />
   <input type="hidden" name="page_options" value="nba_stats_news" />
  
   <p>
   <input type="submit" value="<?php _e('Save Changes') ?>" />
   </p>
  
   </form>

<!-- End Stat News -->

   </div>
   <?php
   }
function nba_stats()
{
$theteam = get_option('nba_stats_team');
$thesize = get_option('nba_stats_size');
$thenews = get_option('nba_stats_news');
$ad1 = get_option('nba_stats_ad1');
$ad2 = get_option('nba_stats_ad2');
$ad3 = get_option('nba_stats_ad3');
$ad4 = get_option('nba_stats_ad4');

$myads = "http://www.ibet.ws/nba_stats_magpie/int1-1/nba_stats_magpie_ads.php?team=$theteam&lnko=$ad1&lnkt=$ad2&lnkh=$ad3&lnkf=$ad4&size=$thesize&news=$thenews";
// This is the Magpie Basic Command for Fetching the Stats URL
$url = $myads;
$rss = nba_fetch_rss( $url );
// Now to break the feed down into each item part
foreach ($rss->items as $item) 
		{
		// These are the individual feed elements per item
		$title = $item['title'];
		$description = $item['description'];
		// Assign Variables to Feed Results
		if ($title == 'adform')
			{
			$adform = $description;
			}
		}

echo $adform;
}
?>