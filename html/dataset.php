<!-- ### OrResponseProfilePage ### 07/2008 ### Daniel Münch -->
<html>
<?php $dataset = $_GET["dataset"]; ?>
<head>
 <title>Summary for dataset: <?php echo $dataset; ?></title>
 <meta name="author" content="Daniel Münch" />
 <meta name="keywords" content="<?php echo $dataset ?>, Drosophila,odor, odorant, response profile, olfaction" />
 <meta http-equiv="Content-Language" content="de" />
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

 <link href="DoOR.css" type="text/css" rel="stylesheet">
 <link href="tablesort.css" type="text/css" rel="stylesheet">

 <script type="text/javascript" src="jquery-1.3.2.min.js"></script>
 <script type="text/javascript" src="jquery.tablesorter.min.js"></script>
 <script type="text/javascript">
 $(document).ready(function(){
   $("#datatable").tablesorter({ sortList: [[0,0], [1,0]],
     widgets: ['zebra', 'columns', 'filter'],
     theme: 'blue'
   });
 });
 $(document).ready(function(){

   // Make table cell focusable
   // http://css-tricks.com/simple-css-row-column-highlighting/
   if ( $('.focus-highlight').length ) {
     $('.focus-highlight').find('td, th')
     .attr('tabindex', '1')
     // add touch device support
     .on('touchstart', function() {
       $(this).focus();
     });
   }

 });
 </script>
</head>

<body>
<?php if ($dataset == ""){echo "No study specified...";}else {  ?>
<?php if (file_exists("data/datasets/dataset_".$dataset.".csv") == FALSE){echo "Sorry, couldn't find data for ",$dataset,"...\n";}else {  //check if csv exists?>

  <!--## CHANGE DATASET ##-->
   <div style="text-align:right;">
      <form action="dataset.php">
          <select name="dataset" size="1" onchange="this.form.submit()">
           <?php
             if (file_exists("data/dataset.info.csv")) {
              echo "<option selected value=\"\">select dataset</option>";
              $file = fopen("data/dataset.info.csv", "r");
              while(($Data = fgetcsv($file, 1000, ",")) !== FALSE){
                if ($Data[1] !== "dataset"){
                 echo "<option value=\"",$Data[1],"\">",$Data[1],"</option>\n";
  	    }}
              fclose($file);
             }  else  {
                 echo "<option selected>input file missing</option>\n";
                }
            ?>
          </select>
      </form>
        </div>
  <!--## END OF CHANGE DATASET ##-->

<h1>Summary for dataset <span class = "dataset"><?php echo $dataset; ?></span></h1>




<!--### GENERAL INFRMATION ###--->
<h2>General Information</h2>
  <table class="info">
  <?php
  $row = 1;
  $file = fopen("data/dataset.info.csv", "r");

  while(($Data = fgetcsv($file, 1000, ",")) !== FALSE)
  {
	  $cols = count($Data);

	  if ($Data[1] == $dataset)
	  {
			  echo "<tr><th valign=\"top\" align=\"left\">dataset:</th><td>",$Data[1],"</td></tr>\n";
			  echo "<tr><th valign=\"top\" align=\"left\">DOI:</th><td><a href=\"",$Data[14],"\" target=\"_blank\">",$Data[15],"</a></td></tr>\n";
			  echo "<tr><th valign=\"top\" align=\"left\">other datasets in study:</th><td valign=\"top\" align=\"left\"><a href=\"dataset.php?dataset=",$Data[3],"\">",$Data[3],"</a> <a href=\"dataset.php?dataset=",$Data[4],"\">",$Data[4],"</a></td></tr>\n";
			  echo "<tr><th valign=\"top\" align=\"left\">technique:</th><td>",$Data[7],"</td></tr>\n";
			  echo "<tr><th valign=\"top\" align=\"left\">data type:</th><td>",$Data[8],"</td></tr>\n";
			  echo "<tr><th valign=\"top\" align=\"left\">controls:</th><td>",$Data[9],"</td></tr>\n";
			  echo "<tr><th valign=\"top\" align=\"left\">solvents:</th><td>",$Data[10],"</td></tr>\n";
			  // echo "<tr><th valign=\"top\" align=\"left\">concentration:</th><td>",$Data[11],"</td></tr>\n";
			  echo "<tr><th valign=\"top\" align=\"left\">SFR reported:</th><td>",$Data[5],"</td></tr>\n";
			  echo "<tr><th valign=\"top\" align=\"left\">SFR substracted:</th><td>",$Data[6],"</td></tr>\n";
			  if ($Data[13] !== "")
			  {
			  echo "<tr><th valign=\"top\" align=\"left\">comment:</th><td class = \"comment\">",$Data[13],"</td></tr>\n";
			  }
			  $row++;
	  }
  }
  fclose($file);
  ?>
  </table>
</div>
<!--## END OF GENERAL INFORMATION ###-->



<!--### DATASET ###--->
<h2>Data</h2>
  <table class="door focus-highlight" id = "datatable">
	  <?php
	  $row = 1;
	  $studyinfo2 = fopen("data/datasets/dataset_".$dataset.".csv", "r");
	  while(($Data = fgetcsv($studyinfo2, 1000, ",")) !== FALSE)
	  {
		  $cols = count($Data);

		  if ($row == 1)
		  { echo "<thead>\n";
			  echo "<tr>\n";
			  echo "<th>",$Data[2],"</a></th>\n";
			  echo "<th>",$Data[3],"</th>\n";
        echo "<th>",$Data[5],"</th>\n";

			  $i = 6;

			  while($i < $cols)
			  {
				  echo "<th><a class=\"intern cas\" title=\"link to receptor in DoOR\" href=\"receptor.php?OR=",$Data[$i],"\">",$Data[$i],"</a></td>";
				  $i++;
			  }

			  echo "</tr>\n";
        echo "</thead>\n";
        echo "<tbody>\n";
			  $row++;
		  }

		  else
		  {
			  echo "<tr>";
			  echo "<td>",$Data[2],"</td>\n";
			  echo "<td><a class=\"intern cas\" href=\"odorant.php?odorant=",$Data[3],"\" title=\"link to odorant in DoOR\">",$Data[3],"</a></td>\n";
			  echo "<td>",$Data[5],"</td>\n";
        $i = 6;

			  while($i < $cols)
			  {
				  if ($Data[$i] !== "NA")
				  {
					  echo "<td><span class=\"response\">",$Data[$i],"</span></td>";
				  }
				  else
				  {
					  echo "<td>&nbsp;</td>";
				  }
				  $i++;
			  }

			  echo "</tr>\n";
			  $row++;
			  $count++;
		  }
	  }
	  fclose($studyinfo2);
	  ?>
  </tbody>
  </table>
</div>
<!--## END OF DATASET ###-->

<?php }} ?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-4423942-3");
pageTracker._trackPageview();
} catch(err) {}</script>

<div class="corner-ribbon top-right sticky red">Preview</div>

</body>
</html>
