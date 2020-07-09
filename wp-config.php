<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'postcodding' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '5E59a9fc9d33' );

/** MySQL hostname */
define( 'DB_HOST', '8.210.164.181' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'p&c_wl}J507E@:h3cu|YBNB=C=UOs<s H9c#9ljAtW&b)G4PaKy=tOy1XA(r32&W' );
define( 'SECURE_AUTH_KEY',  'IH^TKY?7pWwIJ/pv(4Jk(!K.u,:.X]ys<#,+9M&gG&+xa#c8#5Plsy7/B1Z]VOzP' );
define( 'LOGGED_IN_KEY',    '2c_#4TNTfD%;liB;!HALw@/%TwRcZQlPwmVY&iu-FD9xX=o(dz?Pc{WK!0PfO#&=' );
define( 'NONCE_KEY',        'F-eqNHh_*/&h1C}ANrtW#~hh&|s7V>J(~<2P!<e0VH+QWAuOOtgW>1g@bYYMA*|x' );
define( 'AUTH_SALT',        ',%{p2exLoz:}6V!QWIrS*xEjn,^CPKXba-^pzU=nC%Wukq<fs9e} VM3^PQLP.EU' );
define( 'SECURE_AUTH_SALT', '68/c[M OHPR*6U.$5%9J58$!J~.)9WRs=Sg4JS`+pZcB5BIx4RP;sZ@,3GkN)g+R' );
define( 'LOGGED_IN_SALT',   'z9F}n!+3pK#awn@)^CQk_N::R=Mk*vcI0NjY1w*n=pNVm*X=*^)X6)AhoV,sT)|v' );
define( 'NONCE_SALT',       '%N#N<QQr,tXk]^[xs4*uD+:=5lgSSTTUkYdHeb]OBt,#NQCy^M*sK2S07/F&e$65' );

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
define('WPLANG', 'zh_CN');
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

