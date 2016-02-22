<!-- ### OrResponseProfilePage ### 07/2008 ### Daniel Münch -->
<html>
<?php $OR = $_GET["OR"]; ?>
<head>
  <title>Response profile for responding unit <?php echo $OR; ?></title>

  <meta name="author" content="Daniel Münch" />
  <meta name="keywords" content="<?php echo $OR ?>, Drosophila,odor, odorant, response profile, olfaction" />
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
  <?php if ($OR == ""){echo "No OR specified...";}else {  ?>

    <!--## HEADER ##-->
    <!--## CHANGE RECEPTOR ##-->
    <div style="text-align:right;">
      <form action="receptor.php">
        <select name="OR" size="1" onchange="this.form.submit()">
          <?php
          if (file_exists("data/responding.units.csv")) {
            echo "<option selected value=\"\">change responding unit</option>";
            $file = fopen("data/responding.units.csv", "r");
            while(($DataORNames = fgetcsv($file, 1000, ",")) !== FALSE){
              if ($DataORNames[1] != "" & $DataORNames[1] !="Receptor"){
                $ORNames[] = $DataORNames[1];}}
                fclose($file);

                natcasesort($ORNames);
                foreach($ORNames as $ORNS)  echo "<option>",$ORNS,"</option>\n";
              }  else  {
                echo "<option selected>input file missing</option>\n";
              }
              ?>
            </select>
          </form>
        </div>
        <!--## END OF CHANGE RECEPTOR ##-->
        <!--## END OF HEADER ##-->

        <h1>Response profile for responding unit <span class="receptor"><?php echo $OR; ?></span></h1>

        <!--### GENERAL INFRMATION ###--->
        <h2>General Information</h2>
        <?php
        echo "<table class = \"info\">";
        if (file_exists("data/DoOR.mappings.csv")) {
          $row = 1;
          $mapping = fopen("data/DoOR.mappings.csv", "r");

          while(($Data = fgetcsv($mapping, 1000, ",")) !== FALSE) {
            $cols = count($Data);
            if ($Data[1] == $OR){
              echo "<tr><th>Name:</th><td>",$Data[1],"</td>";
              echo "<th>Co-receptor:</th><td>",$Data[5],"</td>";
              if($Data[18] != ""){echo "<th class=\"comment\">Comment:</th>";}
              echo"</tr>\n";

              echo "<tr><th>Receptor(s):</th><td>",$Data[13],"</td>";
              echo "<th>Expressed in:</th><td>";
              if($Data[15] == "TRUE"  & ($Data[16] == "FALSE" | $Data[16] == "NA")) {echo "adult" ;}
              if($Data[15] == "TRUE"  & $Data[16] == "TRUE")  {echo "larva and adult" ;}
              if(($Data[15] == "FALSE" | $Data[15] == "NA") & $Data[16] == "TRUE")  {echo "larva" ;}
              echo "</td>";

              if($Data[18] != ""){echo "<td class=\"comment\" valign=\"top\" align=\"left\" rowspan=\"6\">",$Data[18],"</td>\n";}
              echo "</tr>\n";

              echo "<tr><th>Neuron:</th><td>",$Data[3],"</td><th>Links:</th><td>\n";
              echo "<a class = \"extern\" href = \"http://flybase.org/cgi-bin/quicksearch.cgi?species=Dmel&field=SYM&db=fbgn&addata=all&context=",$OR,"\", target = \"_blank\">FlyBase</a>";
              if ($Data[21] != "NA") {echo ", <a href=\"http://www.virtualflybrain.org/site/stacks/index.htm?id=",$Data[21],"\", target = \"_blank\">VirtualFlyBrain</a> ";}
              echo "</td></tr>\n";
              echo "<tr><th>Sensillum:</th><td>",$Data[2],"</td></tr>\n";
              echo "<tr><th>Glomerulus:</th><td>",$Data[4],"</td></tr>\n";
              echo "<tr><th>Related:</th><td>";
              if ($Data[7]  != "" & $Data[7]  != "NA") { echo "<a href=\"receptor.php?OR=",$Data[7],"\">",$Data[7],"</a>";}
              if ($Data[8]  != "" & $Data[8]  != "NA") { echo ", <a href=\"receptor.php?OR=",$Data[8],"\">",$Data[8],"</a>";}
              if ($Data[9]  != "" & $Data[5]  != "NA") { echo ", <a href=\"receptor.php?OR=",$Data[9],"\">",$Data[9],"</a>";}
              if ($Data[10] != "" & $Data[10] != "NA") { echo ", <a href=\"receptor.php?OR=",$Data[10],"\">",$Data[10],"</a>";}
              if ($Data[11] != "" & $Data[11] != "NA") { echo ", <a href=\"receptor.php?OR=",$Data[11],"\">",$Data[11],"</a>";}
              if ($Data[12] != "" & $Data[12] != "NA") { echo ", <a href=\"receptor.php?OR=",$Data[12],"\">",$Data[12],"</a>";}

              echo "</td></tr>\n";

              $row++;

            }
          }

          fclose($mapping);
        } else {
          echo "<tr><th valign=\"top\"><i>error - data missing:</i></th>";
          echo "<td><i>error - data missing</i></td></tr>\n";
        }
        echo "</table>\n";
        ?>


        <h2>Available Datasets</h2>

        <?php
        if (file_exists("data/datasets/datasets_per_ru.csv")) {
          $row = 1;
          $studiesByORs = fopen("data/datasets/datasets_per_ru.csv", "r");
          while(($Data = fgetcsv($studiesByORs, 1000, ",")) !== FALSE) {
            $cols = count($Data);
            #$i = 2;
            if ($Data[1] == $OR) {
              for($i = 2; $i < $cols; $i ++){
                if($Data[$i] != "NA"){
                  if($i != 2) {
                    echo ", ";
                  }
                  echo "<a href=\"dataset.php?dataset=",$Data[$i],"\">",$Data[$i],"</a>";
                }}
                $row++;
              }
            }
            fclose($studiesByORs);
          } else {
            echo "error - data missing\n";
          }
          ?>



          <?php
          if (file_exists("data/excluded.data.csv")) {
            $row = 1;
            $excluded = fopen("data/excluded.data.csv", "r");
            while(($Data = fgetcsv($excluded, 1000, ",")) !== FALSE) {
              $cols = count($Data);
              #$i = 2;
              if ($Data[1] == $OR) {
                echo "<h3>Excluded Datasets</h3>";
                for($i = 2; $i < $cols; $i ++){
                  if($Data[$i] != "NA"){
                    if($i != 2) {
                      echo ", ";
                    }
                    echo "<a href=\"dataset.php?dataset=",$Data[$i],"\">",$Data[$i],"</a>";
                  }}
                  $row++;
                }
              }
              fclose($studiesByORs);
            } else {
              echo "TEST";
            }
            ?>


            <!--### END OF GENERAL INFRMATION ###--->
            <?php if (file_exists("data/responding.units/".$OR.".csv") == FALSE){echo "<p style=\"text-align:center;\">Sorry, couldn't find data for ",$OR,"...\n</p>";}else {  ?>

              <h2>Tuning Curve</h2>
              <div class = "center">
                <img style = "width:30%; max-width:300px;" src="data/responding.units/<?php echo $OR; ?>_tuningCurve.png">
              </div>

              <h2>Response Profile</h2>
              <div class = "center">
                <img  style = "width:100%; max-width:1000px;" src="data/responding.units/<?php echo $OR;?>_RP.png">
              </div>

              <!--### TABLE ###-->
              <h2>Response Table</h2>
              <table  class="door focus-highlight" id="datatable">
                <?php
                $row = 1;
                $OrFile = fopen("data/responding.units/".$OR.".csv", "r");
                while(($Data = fgetcsv($OrFile, 1000, ",")) !== FALSE) {
                  $cols = count($Data);

                  if ($row == 1) {
                    echo"<thead>\n";
                    echo"<tr>\n";
                    echo"<th>",$Data[2],"</th>\n";
                    echo"<th>",$Data[3],"</th>\n";
                    echo"<th>",$Data[5],"</th>\n";
                    echo"<th>",$Data[6],"</th>\n";
                    echo"</tr>\n\n";
                    echo"</thead>\n";
                    echo"<tbody>\n";
                    $row++;
                  } else {
                    echo"<tr>\n";
                    echo"<tr>\n";
                    echo"<td>",$Data[2],"</td>\n";
                    echo"<td><a href=\"odorant.php?odorant=",$Data[3],"\">",$Data[3],"</a></td>\n";
                    echo"<td>",$Data[5],"</td>\n";
                    echo "<td><span class=\"model response\">",$Data[6],"</span></td>\n";
                    echo"</tr>\n\n";
                    $row++;
                  }
                }
                echo"</tbody>\n";
                fclose($OrFile);
                ?>
              </table>

              <h2>Downloads</h2>
              <a href="data/responding.units/<?php echo$OR?>.csv">download</a> <?php echo$OR?>.csv<br>
              <a href="data/responding.units/<?php echo$OR?>_RP.png">download</a> <?php echo$OR?>_RP.png<br>
              <a href="data/responding.units/<?php echo$OR?>_tuningCurve.png">download</a> <?php echo$OR?>_tuningCurve.png
              <!--### END OF DOWNLOAD ###-->


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
