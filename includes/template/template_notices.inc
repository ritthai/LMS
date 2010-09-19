<?php
/**
	Given:	N/A
*/
?>
</head>
    <body>

<?php   /*****  ARE WE LOGGED IN?   *****/  ?>
<div id="logged_in" style="display: none"><?php echo User::GetAuthenticatedID() ? 'y' : 'n'; ?></div>

<?php   /*****  HIDDEN FANCYBOX LOGIN FORM *****/ ?>
<div style="display:none">
<?php   include("$ROOT/user/views/loginajax.pview.php"); ?>
</div>

<?php	if(User::IsAuthenticated() && ($id=User::GetAuthenticatedID()) && ($name=User::GetAttrib($id,'name'))) { ?>
			<div id="login"><div class="holder">
				Welcome Back, <a class="headerlink" href="/user/"><?php echo $name; ?></a>
				| <a class="headerlink" href="/user/logout">Not <?php echo $name; ?>?</a>
			</div></div>
<?php	} else { ?>
			<div id="login"><div class="holder">
				<a class="headerlink loginlink" id="top_login_link" href="#login_form">Login</a>
				| No account? Click <a class="headerlink" href="/user/create">here</a> to Register.
				| <a class="headerlink" href="/user/forgot_password">Forgot your password?</a>
			</div> </div>
<?php } ?>

<div class="holder">
	<header><div id="logo"><a class="headerlink" href="/"><img src="/images/navigation/logo.png" /></a></div>
<?php	/*****	SEARCH	*****/ ?>
<?php	if((isset($args['university']) && $u = $args['university']) || (isset($_SESSION['university']) && $u = $_SESSION['university'])) { ?>
			<div id="search">
				<div style="position: relative; margin-right: 5px; padding-top: 5px">
					<form action="/search" method="get" class="search_form">
						<input type="text" class="search_form" id="search_terms" name="terms" <?php setDefaultText('Search for a course'); ?> /><br/>
						<input type="hidden" id="search_id" name="id" /><br/>
						<input type="hidden" id="university" name="university" value="<?php echo $u['id']; ?>" /><br/>
					</form>
				</div>
			</div>
<?php	} ?>
			<div style="clear: both"></div>
	</header>
<?php	/*****	BREADCRUMB	*****/	?>
	<nav>
		<h5>
				<!--<a class="breadcrumblink" href="/">ClassMate</a>-->
<?php	if((isset($args['country']) && $c = $args['country']) || (isset($_SESSION['country']) && $c = $_SESSION['country'])) { ?>
			>	<a class="breadcrumblink" href="/areas?<?php echo "country=$c[id]"; ?>">
					<?php echo $c['name']; ?></a>
<?php	} ?>
<?php	if((isset($args['area']) && $a = $args['area']) || (isset($_SESSION['area']) && $a = $_SESSION['area'])) { ?>
			>	<a class="breadcrumblink" href="/universities?<?php echo "area=$a[id]"; ?>">
					<?php echo $a['name']; ?></a>
<?php	} ?>
<?php	if((isset($args['university']) && $u = $args['university']) || (isset($_SESSION['university']) && $u = $_SESSION['university'])) { ?>
			>	<a class="breadcrumblink" href="/subjects?<?php echo "university=$u[id]"; ?>">
					<?php echo $u['name']; ?></a>
<?php	} ?>
<?php	if((isset($args['code']) && $o = $args['code']) || (isset($_SESSION['code']) && $o = $_SESSION['code'])) { ?>
			>	<a class="breadcrumblink" href="/subject?<?php echo "code=$o&university=$u[id]"; ?>">
					<?php echo $o; ?></a>
<?php	} ?>
			&nbsp;
		</h5>
		<h1><?php echo $args['pagetitle']; ?></h1>
	</nav>


<?php   if($errors=Error::get()) {  ?>
    <div style="border: 1px solid #F00; background-color: #fff5f5;">
        <ul>
        <?php foreach($errors as $error) {  ?>
            <li><?php echo Error::format_error($error); ?></li>
        <?php } ?>
        </ul>
    </div>
<?php   }   ?>

	<div id="boxtop"></div>
		<section>
			<article>
				<div id="article_left">

<?php Error::enableLogging(); ?>
