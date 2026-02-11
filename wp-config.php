<?php

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

//Using environment variables for memory limits
$wp_memory_limit = (getenv('WP_MEMORY_LIMIT') && preg_match("/^[0-9]+M$/", getenv('WP_MEMORY_LIMIT'))) ? getenv('WP_MEMORY_LIMIT') : '128M';
$wp_max_memory_limit = (getenv('WP_MAX_MEMORY_LIMIT') && preg_match("/^[0-9]+M$/", getenv('WP_MAX_MEMORY_LIMIT'))) ? getenv('WP_MAX_MEMORY_LIMIT') : '256M';

/** General WordPress memory limit for PHP scripts*/
define('WP_MEMORY_LIMIT', $wp_memory_limit );

/** WordPress memory limit for Admin panel scripts */
define('WP_MAX_MEMORY_LIMIT', $wp_max_memory_limit );


//Using environment variables for DB connection information

// ** Database settings - You can get this info from your web host ** //
$connectstr_dbhost = getenv('DATABASE_HOST');
error_log(print_r($connectstr_dbhost, TRUE));
$connectstr_dbname = getenv('DATABASE_NAME');
error_log(print_r($connectstr_dbname, TRUE));
$connectstr_dbusername = getenv('DATABASE_USERNAME');
error_log(print_r($connectstr_dbusername, TRUE));
$connectstr_dbpassword = getenv('DATABASE_PASSWORD');
error_log(print_r($connectstr_dbpassword, TRUE));

// Using managed identity to fetch MySQL access token
if (strtolower(getenv('ENABLE_MYSQL_MANAGED_IDENTITY')) === 'true') {
	try {
		require_once(ABSPATH . 'class_entra_database_token_utility.php');
		if (strtolower(getenv('CACHE_MYSQL_ACCESS_TOKEN')) !== 'true') {
			$connectstr_dbpassword = EntraID_Database_Token_Utilities::getAccessToken();
		} else {
			$connectstr_dbpassword = EntraID_Database_Token_Utilities::getOrUpdateAccessTokenFromCache();
		}
error_log(print_r($connectstr_dbpassword, TRUE));

	} catch (Exception $e) {
		// An empty string displays a 502 HTTP error page rather than a database connection error page. So, using a dummy string instead.
		$connectstr_dbpassword = '<dummy-value>';
		error_log($e->getMessage());
	}
}

/** The name of the database for WordPress */
define('DB_NAME', $connectstr_dbname);

/** MySQL database username */
define('DB_USER', $connectstr_dbusername);

/** MySQL database password */
define('DB_PASSWORD',$connectstr_dbpassword);

/** MySQL hostname */
define('DB_HOST', $connectstr_dbhost);

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/** Enabling support for connecting external MYSQL over SSL*/
$mysql_sslconnect = (getenv('DB_SSL_CONNECTION')) ? getenv('DB_SSL_CONNECTION') : 'true';
if (strtolower($mysql_sslconnect) != 'false' && !is_numeric(strpos($connectstr_dbhost, "127.0.0.1")) && !is_numeric(strpos(strtolower($connectstr_dbhost), "localhost"))) {
	define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);
}


/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '!_m}>U{[$,.fo6~hjA0o,DOPY@x<Y05jKc`1hgM 2-N8YC&&%~oCqqIsvha#=G+]' );
define( 'SECURE_AUTH_KEY',  'A>) /[[`Vkh^FUa%VsM46hTP[IUQv3`utRx^WVI;bb0_^nK}G@#b}wege>GIm-NS' );
define( 'LOGGED_IN_KEY',    'Qbau&a!>(Wm2lK5F^6OxV&y[Z>YD2rkBRbtX*ONfUN~/<2h?Y,M4hg|z4Q^,B,i]' );
define( 'NONCE_KEY',        'C=9ZT0sPYl/H4Ikl5x~j =F/NcitX}j.mE6:e%!EUDq}:--]JI^#m@AgNp!n`%VE' );
define( 'AUTH_SALT',        '/MubG4F?f3bpj94l=PZr6$laQ&]tJqOa-n0W;Bz!&]A|r:Nbe{dZn@A*Ak(BGW+' );
define( 'SECURE_AUTH_SALT', 'v1+OE)1)3%rS6BnvtQ!uEJF/)YZs9+[2-`rTRW-H9C3|o=ZnmR6.MsV}GMonDTR^' );
define( 'LOGGED_IN_SALT',   'y8 &({Fh(9F0EeAJ:559T&jY;Etx.4w}@I/fw0[4Id_aN3i,S)o0n`UGU`lo>-=>' );
define( 'NONCE_SALT',       'WGS2E#{d%G}LcD8@c^ (J[Y0%|V3i^cPS%hCe:Ne[oB,p:Rk@8%yWd+Ij<Y70Mc1' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy blogging. */
/**https://developer.wordpress.org/reference/functions/is_ssl/ */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
	$_SERVER['HTTPS'] = 'on';

$http_protocol='http://';
if (!preg_match("/^localhost(:[0-9])*/", $_SERVER['HTTP_HOST']) && !preg_match("/^127\.0\.0\.1(:[0-9])*/", $_SERVER['HTTP_HOST'])) {
	$http_protocol='https://';
}

//Relative URLs for swapping across app service deployment slots
//
// .orig
define('WP_HOME', $http_protocol . $_SERVER['HTTP_HOST']);
define('WP_SITEURL', $http_protocol . $_SERVER['HTTP_HOST']);
// .changed
//define('WP_HOME', $http_protocol . 'student.MichaelKentBurns.com');
//define('WP_SITEURL', $http_protocol . 'student.MichaelKentBurns.com');
//
define('WP_CONTENT_URL', '/wp-content');
define('DOMAIN_CURRENT_SITE', $_SERVER['HTTP_HOST']);

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
