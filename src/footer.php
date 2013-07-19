      <hr>

	      <footer>
	              <?php include('logos_footer.php');?></a>
	      </footer>
    </div>

    </div> 
  <!-- Le javascript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.4.5/bootstrap-editable/js/bootstrap-editable.min.js"></script>
  <script src="js/bootstrap/bootstrap.min.js"></script>
  <script src="js/bootbox.min.js"></script>
  <?php if (isset($show_tabs) && $show_tabs===true) {?>
  <script>
  $('#myTab a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  });
  </script>
<?php } ?>
</body>
</html>