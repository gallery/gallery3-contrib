<?php defined('SYSPATH') OR die('No direct access allowed. This file is automatically ran by index.php.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Kohana Installation</title>
		<style type="text/css">
			body {
				width: 42em;
				margin: 0 auto;
				font-family: sans-serif;
				font-size: 90%;
			}

			#tests table {
				border-collapse: collapse;
				width: 100%;
			}

			#tests table th, #tests table td {
				padding: 0.2em 0.4em;
				text-align: left;
				vertical-align: top;
			}

			#tests table th {
				width: 12em;
				font-weight: normal;
				font-size: 1.2em;
			}

			#tests table tr:nth-child(odd) {
				background:	#eee;
			}

			#tests table td.pass {
				color: #191;
			}

			#tests table td.fail {
				color: #911;
			}

			#tests #results {
				color: #fff;
			}

			#tests #results p {
				padding: 0.8em 0.4em;
			}

			#tests #results p.pass {
				background: #191;
			}

			#tests #results p.fail {
				background: #911;
			}
		</style>
	</head>
	<body>
		<h1>Environment Tests</h1>
		<p>
			The following tests have been run to determine if Kohana will work in your environment. If any of the tests have failed, consult the <a href="http://docs.kohanaphp.com/installation">documentation</a>
			for more information on how to correct the problem.
		</p>
		<div id="tests">
			<?php $failed = FALSE?>
			<table cellspacing="0">
				<tr>
					<th>PHP Version</th>
					<?php if (version_compare(PHP_VERSION, '5.2', '>=')): ?>
					<td class="pass">
						<?php echo PHP_VERSION?>
					</td>
					<?php else : $failed = TRUE?>
					<td class="fail">
						Kohana requires PHP 5.2 or newer, this version is <?php echo PHP_VERSION?>.
					</td>
					<?php endif?>
				</tr>
				<tr>
					<th>System Directory</th>
					<?php if (is_dir(SYSPATH)): ?>
					<td class="pass">
						<?php echo SYSPATH?>
					</td>
					<?php else : $failed = TRUE?>
					<td class="fail">
						The configured
						<code>
							system
						</code>
						directory does not exist or does not contain required files.
					</td>
					<?php endif?>
				</tr>
				<tr>
					<th>Application Directory</th>
					<?php if (is_dir(APPPATH) AND is_file(APPPATH.'config/config'.EXT) AND is_file(APPPATH.'Bootstrap'.EXT)): ?>
					<td class="pass">
						<?php echo APPPATH?>
					</td>
					<?php else : $failed = TRUE?>
					<td class="fail">
						The configured
						<code>
							application
						</code>
						directory does not exist or does not contain required files.
					</td>
					<?php endif?>
				</tr>
				<tr>
					<th>Modules Directory</th>
					<?php if (is_dir(MODPATH)): ?>
					<td class="pass">
						<?php echo MODPATH?>
					</td>
					<?php else : $failed = TRUE?>
					<td class="fail">
						The configured
						<code>
							modules
						</code>
						directory does not exist or does not contain required files.
					</td>
					<?php endif?>
				</tr>
				<tr>
					<th>Logs Directory</th>
					<?php if (is_dir(APPPATH.'logs') AND is_writable(APPPATH.'logs')): ?>
					<td class="pass">
						Pass
					</td>
					<?php else : $failed = TRUE?>
					<td class="fail">
						The default
						<code>
							logs
						</code>
						directory does not exist or is not writable. Depending on your log driver and config settings, this may not be a problem.
					</td>
					<?php endif?>
				</tr>
				<tr>
					<th>Cache Directory</th>
					<?php if (is_dir(APPPATH.'cache') AND is_writable(APPPATH.'cache')): ?>
					<td class="pass">
						Pass
					</td>
					<?php else : $failed = TRUE?>
					<td class="fail">
						The default
						<code>
							cache
						</code>
						directory does not exist or is not writable. Depending on your cache driver and config settings, this may not be a problem.
					</td>
					<?php endif?>
				</tr>
				<tr>
					<th>PCRE UTF-8</th>
					<?php if ( ! function_exists('preg_match')): $failed = TRUE?>
					<td class="fail">
						<a href="http://php.net/pcre">PCRE</a>
						support is missing.
					</td>
					<?php elseif ( ! @preg_match('/^.$/u', 'ñ')): $failed = TRUE?>
					<td class="fail">
						<a href="http://php.net/pcre">PCRE</a>
						has not been compiled with UTF-8 support.
					</td>
					<?php elseif ( ! @preg_match('/^\pL$/u', 'ñ')): $failed = TRUE?>
					<td class="fail">
						<a href="http://php.net/pcre">PCRE</a>
						has not been compiled with Unicode property support.
					</td>
					<?php else : ?>
					<td class="pass">
						Pass
					</td>
					<?php endif?>
				</tr>
				<tr>
					<th>Reflection Enabled</th>
					<?php if (class_exists('ReflectionClass')): ?>
					<td class="pass">
						Pass
					</td>
					<?php else : $failed = TRUE?>
					<td class="fail">
						PHP <a href="http://www.php.net/reflection">reflection</a>
						is either not loaded or not compiled in.
					</td>
					<?php endif?>
				</tr>
				<tr>
					<th>Filters Enabled</th>
					<?php if (function_exists('filter_list')): ?>
					<td class="pass">
						Pass
					</td>
					<?php else : $failed = TRUE?>
					<td class="fail">
						The <a href="http://www.php.net/filter">filter</a>
						extension is either not loaded or not compiled in.
					</td>
					<?php endif?>
				</tr>
				<tr>
					<th>Iconv Extension Loaded</th>
					<?php if (extension_loaded('iconv')): ?>
					<td class="pass">
						Pass
					</td>
					<?php else : $failed = TRUE?>
					<td class="fail">
						The <a href="http://php.net/iconv">iconv</a>
						extension is not loaded.
					</td>
					<?php endif?>
				</tr>
				<tr>
					<th>SPL Enabled</th>
					<?php if (function_exists('spl_autoload_register')): ?>
					<td class="pass">
						Pass
					</td>
					<?php else : $failed = TRUE?>
					<td class="fail">
						<a href="http://php.net/spl">SPL</a>
						is not enabled.
					</td>
					<?php endif?>
				</tr>
				<tr>
					<th>Multibyte String Enabled</th>
					<?php if (extension_loaded('mbstring')): ?>
					<td class="pass">Pass</td>
					<?php else: $failed = TRUE; ?>
					<td class="fail">The <a href="http://php.net/mbstring">mbstring</a>
						extension is not loaded.</td>
					<?php endif ?>
				</tr>
				<?php if (extension_loaded('mbstring')): ?>
				<tr>
					<th>Mbstring Not Overloaded</th>
					<?php if (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING): $failed = TRUE?>
					<td class="fail">
						The <a href="http://php.net/mbstring">mbstring</a>
						extension is overloading PHP's native string functions.
					</td>
					<?php else : ?>
					<td class="pass">
						Pass
					</td>
					<?php endif?>
				</tr>
				<?php endif?>
				<tr>
					<th>XML support</th>
					<?php if ( ! function_exists('utf8_encode')): $failed = TRUE?>
					<td class="fail">
						PHP is compiled without <a href="http://php.net/xml">XML</a>
						support, thus lacking support for
						<code>
							utf8_encode()
						</code>/
						<code>
							utf8_decode()
						</code>.
					</td>
					<?php else : ?>
					<td class="pass">
						Pass
					</td>
					<?php endif?>
				</tr>
					<th>Timezone</th>
					<?php try { new DateTimeZone(ini_get('date.timezone')); ?>
					<td class="pass">Pass</td>
					<?php } catch (Exception $e) { $failed = TRUE ?>
					<td class="fail">
						The current timezone, <code>'<?php echo ini_get('date.timezone') ?>'</code>, is not valid.
						You must configure it in <code>php.ini</code> or <code>config/locale.php</code>.
					</td>
					<?php } ?>
				</tr>
				<tr>
					<th>URI Determination</th>
					<?php if (isset($_SERVER['SCRIPT_NAME']) AND (isset($_SERVER['PATH_INFO']) OR isset($_SERVER['ORIG_PATH_INFO']) OR isset($_SERVER['PHP_SELF']))): ?>
					<td class="pass">
						Pass
					</td>
					<?php else : $failed = TRUE?>
					<td class="fail">
						At least one of <code>$_SERVER['PATH_INFO']</code>, <code>$_SERVER['ORIG_PATH_INFO']</code>, or <code>$_SERVER['PHP_SELF']</code> must be available.
					</td>
					<?php endif?>
				</tr>
			</table>
			<div id="results">
				<?php if ($failed === TRUE): ?>
				<p class="fail">
					Kohana may not work correctly with your environment.
				</p>
				<?php else : ?>
				<p class="pass">
					Your environment passed all requirements. Remove or rename the
					<code>
						install<?php echo EXT?>
					</code>
					file now.
				</p>
				<?php endif?>
			</div>
		</div>
	</body>
</html>
