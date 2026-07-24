<?php
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.8.2
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2019 Fuel Development Team
 * @link       https://fuelphp.com
 */

// Bootstrap the framework - THIS LINE NEEDS TO BE FIRST!
require COREPATH.'bootstrap.php';

// Add framework overload classes here
\Autoloader::add_classes(array(
	// Example: 'View' => APPPATH.'classes/myview.php',
));

// Register the autoloader
\Autoloader::register();

/**
 * Your environment.  Can be set to any of the following:
 *
 * Fuel::DEVELOPMENT
 * Fuel::TEST
 * Fuel::STAGING
 * Fuel::PRODUCTION
 */
Fuel::$env = Arr::get($_SERVER, 'FUEL_ENV', Arr::get($_ENV, 'FUEL_ENV', getenv('FUEL_ENV') ?: Fuel::DEVELOPMENT));

// PHP 8.x の Deprecated / Notice 警告を FuelPHP のエラーハンドラに渡さない
// （FuelPHP 1.8.2 は古いため、PHP 8.2 では非推奨警告が大量に出る。それを抑制する）
set_error_handler(function ($severity, $message, $file, $line) {
	// Deprecated と Notice は無視（それ以外は通常どおり FuelPHP に処理させる）
	if ($severity === E_DEPRECATED || $severity === E_USER_DEPRECATED || $severity === E_NOTICE) {
		return true;
	}
	return \Fuel\Core\Error::error_handler($severity, $message, $file, $line);
});

// Initialize the framework with the config file.
\Fuel::init('config.php');