<?php
try {
	if (strtolower(PHP_OS) != 'linux') {
		throw new Exception("Sorry, but script only works on Linux\n");
	}
	if (php_sapi_name() != 'cli') {
		throw new Exception("Please, call script from terminal");
	}
} catch (Exception $e) {
	die($e->getMessage());
}

// Your hosts file
define('HOSTS_FILE', '/etc/hosts');

// Directory where locates apache2 config files
define('CONF_FILES_DIR', '/etc/apache2/sites-available');

// Directory where locates your domains
define('DOMAINS_DIR', '/var/www');

//HTML template file
define('HTML_TEMPLATE_FILE', 'html_template.html');

//Config file template
define('CONF_TEMPLATE_FILE', 'conf_template.txt');

//Enter your domain. Don't use cyrillic!
$domain = readline("Enter your domain: ");

try {
	if (file_exists(DOMAINS_DIR . "/$domain"))
		throw new Exception("Directory " . DOMAINS_DIR . "/$domain already created \n");
} catch (Exception $e) {
	die($e->getMessage());
}

//Making domain directory
system('sudo mkdir ' . DOMAINS_DIR . "/$domain");
system('sudo mkdir ' . DOMAINS_DIR . "/$domain/public_html");
system('sudo touch ' . DOMAINS_DIR . "/$domain/public_html/index.php");
system('sudo chmod -R 777 ' . DOMAINS_DIR);

//Making config file
system('sudo touch ' . CONF_FILES_DIR . "/$domain.conf");
$confTemplate = file_get_contents(realpath(CONF_TEMPLATE_FILE));
$confTemplate = str_replace('{domain}', $domain, $confTemplate);
$confFile = fopen(CONF_FILES_DIR . "/$domain.conf", 'w');
fwrite($confFile, $confTemplate);
fclose($confFile);

//Adding config file and restarts apache
system("sudo a2ensite $domain.conf");
system('sudo systemctl restart apache2');

//Adding to hosts file domain
$hostsFile = fopen(HOSTS_FILE, 'a');
fwrite($hostsFile, "\n127.0.0.1 $domain");
fclose($hostsFile);

//Adding index.php
$htmlTemplate = file_get_contents(realpath(HTML_TEMPLATE_FILE));
$htmlTemplate = str_replace('{domain}', $domain, $htmlTemplate);
$htmlFile = fopen(DOMAINS_DIR . "/$domain/public_html/index.php", 'w');
fwrite($htmlFile, $htmlTemplate);
fclose($htmlFile);

echo "\nDomain $domain has been successfull created!\n";
