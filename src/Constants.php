<?PHP

session_start();

date_default_timezone_set('America/New_York');

// Set up debug mode
define("DEBUG_MODE", true);

// Site root
define("SITE_ROOT", "http://localhost/mike");

define("SITE_NAME", "Rankings");

// error_reporting(0);

define("DB_HOST","localhost");
define("DB_NAME","mike");
define("DB_USER","root");
define("DB_PASSWORD","");

// define("MAX_REPLIES_PER_PAGE", 3);

// The Player Group
define("PLAYER_GROUP", 1);

// The admin group
define("ADMIN_GROUP", 2);

// Admins
const canCreateChallenges = array(ADMIN_GROUP);
const canViewChallengeInfo = array(ADMIN_GROUP);
const canUpdateChallengeInfo = array(ADMIN_GROUP);
const canDeleteChallenge = array(ADMIN_GROUP);
const canViewChallengePassword = array(ADMIN_GROUP);
const canKickAnyone = array(ADMIN_GROUP);
const canMakeAnyoneLeader = array(ADMIN_GROUP);

// Admins and Players
const canJoinTeam = array(PLAYER_GROUP, ADMIN_GROUP);
const canCreateTeam = array(PLAYER_GROUP, ADMIN_GROUP);
const canLeaveTeam = array(PLAYER_GROUP, ADMIN_GROUP);
const canKick = array(PLAYER_GROUP, ADMIN_GROUP);
const canMakeLeader = array(PLAYER_GROUP, ADMIN_GROUP);
const canSubmitPasswords = array(PLAYER_GROUP, ADMIN_GROUP);

define("SIGNUP_ENABLED", true);
define("SIGNIN_ENABLED", true);
define("JOIN_TEAM_ENABLED", true);
define("CREATE_TEAM_ENABLED", true);
define("TEAM_KICKING", true);
define("LEAVE_TEAM_ENABLED", true);
define("CAN_MAKE_LEADER_ENABLED", true);
define("CAN_SUBMIT_PASSWORDS", true);
define("VIEW_CHALLENGES_ENABLED", true);

// Which event are we currently on
define("CURRENT_EVENT", 1);