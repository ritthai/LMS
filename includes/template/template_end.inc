<?php
/**
	Given:	N/A
*/
?>

<?php //Error::disableLogging(); ?>
<?php	Error::showSeparator(); Error::setBgColour("#888");	?>

					</div>
					<div id="article_right">
<?php	if(User::IsAuthenticated() && $args['comment_id'])	include("$TEMPLATEROOT/sidebar_1.inc");
		else if($args['comment_id'])						include("$TEMPLATEROOT/sidebar_3.inc");
		else if(User::IsAuthenticated())					include("$TEMPLATEROOT/sidebar_1.inc");
		else												include("$TEMPLATEROOT/sidebar_3.inc");
?>
					</div>
					<div style="clear:both"></div>
				</article>
			</section>
		<footer></footer>
		<div id="footertext">
			<span style="float: left">ClassMate &copy; 2010</span>
			<span style="float:right">
				<a class="footerlink" href="/about">About</a> &middot;
				<a class="footerlink" href="/privacy">Privacy</a> &middot;
				<a class="footerlink" href="/terms">Terms</a> &middot;
				<a class="footerlink" href="/contact">Contact</a>
			</span>
		</div>
	</body>
</html>

<?php Error::enableLogging(); ?>

<?php /* cloudinizr cloud tag in the sidebar */ ?>
<script type="text/javascript">
	jQuery("#sidebar_tagcloud").html(jQuery("#sidebar_hidden_tags").html()).cloudinizr();
	jQuery("#sidebar_tagcloud span").hover(
		function() { jQuery(this).addClass('tagcloud_hover'); },
		function() { jQuery(this).removeClass('tagcloud_hover'); }
	);
	jQuery("#sidebar_tagcloud span").click(function() { window.location = "/search?terms="+jQuery(this).html(); });
</script>

