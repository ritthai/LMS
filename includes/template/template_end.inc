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
<?php /* cloudinizr cloud tag in the sidebar */ ?>
		<script type="text/javascript">
			function setup_tagcloud($id) {
				jQuery("#"+$id).html(jQuery("#"+$id+"_hidden").html()).cloudinizr();
				jQuery("#"+$id+" span").hover(
					function() { jQuery(this).addClass('tagcloud_hover'); },
					function() { jQuery(this).removeClass('tagcloud_hover'); }
				);
				jQuery("#"+$id+" span").click(function() { window.location = "/search?terms="+jQuery(this).html(); });
			}
			setup_tagcloud("sidebar_recommendation_tagcloud");
			setup_tagcloud("sidebar_similarities_tagcloud");
		</script>

	</body>
</html>

<?php Error::enableLogging(); ?>

