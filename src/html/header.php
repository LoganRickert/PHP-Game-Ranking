<?PHP
include './autoloader.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title><?PHP echo $html->getName() ?></title>
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<link href='https://fonts.googleapis.com/css?family=Source+Serif+Pro' rel='stylesheet' type='text/css'>
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<link rel="StyleSheet" href="css/reset.css" type="text/css">
	<link rel="StyleSheet" href="css/style.css" type="text/css">
	<link rel="stylesheet" media="(max-width: 1200px)" href="css/tablet.css" />
	<link rel="stylesheet" media="(max-width: 1250px)" href="css/phone.css" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>
<body>
	<div id="wrapper">
		<header>
			<?PHP echo Constants::$siteName; ?>
		</header>
		<nav>
			Nav
		</nav>
	<!--
    <section id="content">
      <article>
        Article
      </article>
    </section>
    <aside>
      Sidebar
    </aside>
	-->