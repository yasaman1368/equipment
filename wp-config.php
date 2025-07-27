<?php

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'posnegarlocaldb');

/** Database username */
define('DB_USER', 'root');

/** Database password */
define('DB_PASSWORD', '');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'M<2cLK2# iTB|Bm%HSV.xJMCH>!dQC(t/u6V-@om8;,OzAFhFjsHO]ZEl4]CG/KT');
define('SECURE_AUTH_KEY',  '(<4p~[V&);y{+St6#7g@x_S4j/jN=PV!>kf7;3Ufs(M 7tfI:mF*CZNGnhT_dhiS');
define('LOGGED_IN_KEY',    '6z;C:zQ@Zn,)JNis/os@gHbmoTzp!ZF5NR~U2Yii^>._YTm)7!p4DNaC< tIo.#3');
define('NONCE_KEY',        '4?O}9BH/KnjRx>;?_J~O?su<h-{j)O8X)a,>p-J_F^Ga;&1=5Xa`[y.~eaj`dFcJ');
define('AUTH_SALT',        'DdF}C/bwNI)FPwfu5Jg>,r=(48H6k?1[Ae#cQygV;sJX&<DeC?Jb81&!4Ux6dSbJ');
define('SECURE_AUTH_SALT', '8GO7uh[m9Te%TnxEz30-muJ-,H>{3R:o%LR[uatpI~vjG1<Fd0C50I<{.=^Fz@J&');
define('LOGGED_IN_SALT',   '2 ]M=WcKtC+Ng+I]}0K-.8Sp^mTLMGGd(R6ivmzYi 3p_P3;4Db{.MO7uK!!gwh}');
define('NONCE_SALT',       'sbEY-zXJ;|9n^K$~}inzwGPieUqe/vD`/qJA;_t(nYy +N1hyr#Ehap@eTw#l#9[');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'PN_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', true);              // Turn on debugging
define('WP_DEBUG_LOG', true);          // Save errors to wp-content/debug.log
define('WP_DEBUG_DISPLAY', false);     // Hide errors from frontend
@ini_set('display_errors', 0);         // Prevent PHP from showing errors

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (! defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
