      <hr>

	      <footer>
	              <?php include(dirname(__FILE__).'/logos_footer.php');?>
	      </footer>
    </div>

    </div> 
  <!-- Le javascript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.4.5/bootstrap-editable/js/bootstrap-editable.min.js"></script>
  <script src="js/bootstrap/wysihtml5-0.3.0.js"></script>
  <script src="js/bootstrap/bootstrap.min.js"></script>
  <script src="js/bootbox.min.js"></script>
  <script src="js/bootstrap/bootstrap-wysihtml5.js"></script>
  <?php if (isset($show_tabs) && $show_tabs===true) {?>
  <script>
  $('#myTab a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  });
  $('.textarea').wysihtml5({
  "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
  "emphasis": true, //Italics, bold, etc. Default true
  "lists": false, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
  "html": true, //Button which allows you to edit the generated HTML. Default false
  "link": true, //Button to insert a link. Default true
  "image": true, //Button to insert an image. Default true,
  "color": false //Button to change color of font  
});
  </script>
<?php } ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-35476259-4', '54.246.81.79');
  ga('send', 'pageview');

</script>
</body>
</html>