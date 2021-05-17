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
define( 'DB_NAME', 'sagar_db' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         '}zE!+K<^ [hb7Sn@$(gu8W#x4+x9Nb2l(-3B(Q895LXY}2-/7%~x6&6+4. d<ulJ' );
define( 'SECURE_AUTH_KEY',  '!vPhp/1gb;Ul_u%C7/(UG+K4w.74d1BD#@$+aW}cTdkEBmuyO=3o0@@?=dMfc}rg' );
define( 'LOGGED_IN_KEY',    '<.yBg8lEvWyPrk<|`S@uRS(saYjx,7On>nrr6X+AM|c{GM3F*4*$<+4^WjG@ &&>' );
define( 'NONCE_KEY',        'Pb1>bc21bl}yA-1$q4s@#]B!w[nsp[U=]W[WW?*HL6Y.Zg_4R[~;XI46/x)knoj2' );
define( 'AUTH_SALT',        'tmPpep!TUU(fxsxEyYC{ArLrJWnP-P!l}]M-/%STdOvS*=K2:&M>+R;:w_S^n/fq' );
define( 'SECURE_AUTH_SALT', 'n`AZ8@,~#k;,3PJQc$Q&s[:Djtb,mG<m*}jd/-,`iY{Ss21utqFnq{cz0/-y9:mv' );
define( 'LOGGED_IN_SALT',   'M*1kf%c/&vi+PrNy,lqC[H@1nv&/^.SZq?;7<!a,$L&+Dq~awC{6W2gp,s`m>iPv' );
define( 'NONCE_SALT',       'LYj,@170JArE?_V8YO![zg5~-g+eyI(zu^4M8#~Fw+w{2DjvEL]x@If*mV#2$ vs' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'sagar_';

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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
