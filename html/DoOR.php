<html>
<head>
  <title>DoOR</title>
  <meta name="author" content="Daniel Münch" />
  <meta name="keywords" content="Drosophila,odor, odorant, response profile, olfaction" />
  <meta http-equiv="Content-Language" content="de" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link href="DoOR.css" type="text/css" rel="stylesheet">
  <!--jQuery Framework laden-->
  <script src="jquery-1.3.2.min.js" type="text/javascript" charset="utf-8"></script>

</head>
<body>

  <h1 class="FP_title">DoOR - Database of Odorant Responses</h1>

    <p>This site provides you with:
      <ul>
        <li>the full <i>Drosophila</i> odor receptor response profiles, as compiled using the DoOR package, and continously up to date.</li>
        <li>the current version of the data- and functions-package, so that you can use the whole data offline, and most importantly adapt the algorithm for your needs, generally in order to insert your own odor-response profiles for intraspecific or interspecific comparison</li>
        <li>previous versions of both data- and functions-package in order to secure backwards compatibility</li>
      </ul>
    </p>
    <p>
      PLEASE HELP US! The data in the package is only as good as the datasets that form its background. If you have a dataset that has not yet been included, or if you know of one, please let us know! Similary, if you find errors or mismatches, please let us know! We strive to get this resource as accurate as possible, in order to make it as useful as possible.<br>
      Every help is appreciated, Thanks a lot!
    </p>

    <!--<h3>Introduction</h3>-->
    <h2>Query</h2>
    <span id="query">
      <table>
        <tr>
          <td>responding unit:</td>
          <td>
            <form action="receptor.php">
              <p>
                <select name="OR" size = "1">
                  <?php
                  if (file_exists("data/responding.units.csv")) {
                    echo "<option selected value=\"\">select responding unit</option>";
                    $file = fopen("data/responding.units.csv", "r");
                    while(($DataORNames = fgetcsv($file, 1000, ",")) !== FALSE){
                      if ($DataORNames[1] != "" & $DataORNames[1] !="responding.units"){
                        $ORNames[] = $DataORNames[1];}}
                        fclose($file);

                        natcasesort($ORNames);
                        foreach($ORNames as $ORNS)  echo "<option>",$ORNS,"</option>\n";
                      }  else  {
                        echo "<option selected>input file missing</option>\n";
                      }
                      ?>
                    </select>
                    <input type="submit" value=" Go "></p>
                  </form>
                </td>
              </tr>
              <tr>
                <td>InChIKey:&nbsp; &nbsp; &nbsp;</td>
                <td>
                  <form action="odorant.php">
                    <p>
                      <select name="odorant" size="1">
                        <?php
                        if (file_exists("data/odorants.csv")) {
                          echo "<option selected value=\"\">select InChIKey</option>";
                          $odorantscsv = fopen("data/odorants.csv", "r");
                          while(($DataOdorants = fgetcsv($odorantscsv, 1000, ",")) !== FALSE){
                          if ($DataOdorants[3] !="CAS"){
                            $odorant[$DataOdorants[3]] = $DataOdorants[3];}}
                            fclose($odorantscsv);
                            natsort($odorant);
                            foreach($odorant as $odorant_key => $odorant_value)  echo "<option value=\"",$odorant_key,"\">",$odorant_value,"</option>\n";
                          }  else  {
                            echo "<option selected>input file missing</option>\n";
                        }
                        ?>
                            </select>
                          <input type="submit" value=" Go ">
                        </p>
                      </form>
                    </td>
                  </tr>
              <tr>
                <td>CAS number:</td>
                <td>
                  <form action="odorant.php">
                    <p>
                      <select name="odorant" size="1">
                        <?php
                        if (file_exists("data/odorants.csv")) {
                          echo "<option selected value=\"\">select CAS</option>";
                          $odorantscsv = fopen("data/odorants.csv", "r");
                          while(($DataOdorants = fgetcsv($odorantscsv, 1000, ",")) !== FALSE){
                            if ($DataOdorants[3] !="CAS"){
                              $odorant[$DataOdorants[3]] = $DataOdorants[5];}}
                              fclose($odorantscsv);
                              natsort($odorant);
                              foreach($odorant as $odorant_key => $odorant_value)  echo "<option value=\"",$odorant_key,"\">",$odorant_value,"</option>\n";
                            }  else  {
                              echo "<option selected>input file missing</option>\n";
                            }
                            ?>
                          </select>
                          <input type="submit" value=" Go "></p>
                        </form>
                      </td>
                    </tr>
                    <tr>
                      <td>odorant name:&nbsp; &nbsp; &nbsp;</td>
                      <td>
                        <form action="odorant.php">
                          <p>
                            <select name="odorant" size="1">
                              <?php
                              if (file_exists("data/odorants.csv")) {
                                echo "<option selected value=\"\">select odorant name</option>";
                                $odorantscsv = fopen("data/odorants.csv", "r");
                                while(($DataOdorants = fgetcsv($odorantscsv, 1000, ",")) !== FALSE){
                                  if ($DataOdorants[3] !="CAS"){
                                    $odorant[$DataOdorants[3]] = $DataOdorants[2];}}
                                    fclose($odorantscsv);
                                    natsort($odorant);
                                    foreach($odorant as $odorant_key => $odorant_value)  echo "<option value=\"",$odorant_key,"\">",$odorant_value,"</option>\n";
                                  }  else  {
                                    echo "<option selected>input file missing</option>\n";
                                  }
                                  ?>
                                </select>
                                <input type="submit" value=" Go "></p>
                              </form>
                            </td>
                          </tr>

                          <tr>
                            <td>included datasets:&nbsp; &nbsp; &nbsp;</td>
                            <td>
                              <form action="dataset.php">
                                <p>
                                  <select name="dataset" size="1">
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
                                    <input type="submit" value=" Go "></p>
                                  </form>
                                </td>
                              </tr>
                            </table>
                          </span>



                        <h2>Publications</h2>
                          <span id="links">
                            <a class="DoORFrontpageLink FPLpurple" target="_blank" href="http://biorxiv.org/content/early/2015/09/30/027920">DoOR 2.0 - Comprehensive Mapping of <i>Drosophila melanogaster</i> Odorant Responses.</a> 2015, bioRxiv<br>
                            <a class="DoORFrontpageLink FPLpurple" target="_blank" href="http://chemse.oxfordjournals.org/content/35/7/551">Integrating heterogeneous odor response data into a common response model: A DoOR to the complete olfactome.</a> 2010, Chemical Senses<br>
                            <a class="DoORFrontpageLink FPLgreen" target="_blank" href="http://www.chemosense.net/issues/11/ChemoSenseSept11.pdf">DoOR: The Database of Odorant Responses.</a> 2011, Chemosense<br>
                          </span>

                        <h2>Documentation</h2>
                          <span id="download">
                            <a class="DoORFrontpageLink FPLgreen" target="_blank" href="doc/DooR.functions_vignette.html">DoOR.functions</a><br>
                            <a class="DoORFrontpageLink FPLgreen" target="_blank" href="doc/DoOR_visualizations.html">DoOR visualizations</a><br>
                            <a class="DoORFrontpageLink FPLgreen" target="_blank" href="doc/DoOR_tools.html">DoOR tools</a><br>
                          </span>

                        <h2>Links</h2>
                          <span id="links">
                            <a class="DoORFrontpageLink FPLblue" target="_blank" href="https://github.com/Dahaniel/DoOR.functions">DoOR.functions @ GitHub</a><br>
                            <a class="DoORFrontpageLink FPLblue" target="_blank" href="https://github.com/Dahaniel/DoOR.data">DoOR.data @ GitHub</a><br>
                            <a class="DoORFrontpageLink FPLred" target="_blank" href="https://github.com/Dahaniel/DoOR.functions/issues">report a bug or request a feature</a><br>
                            <a class="DoORFrontpageLink FPLpurple" target="_blank" href="http://neuro.uni.kn/DoOR">DoOR 1.0</a>
                          </span>



                          <a href="https://github.com/Dahaniel/DoOR.functions"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/652c5b9acfaddf3a9c326fa6bde407b87f7be0f4/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6f72616e67655f6666373630302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_orange_ff7600.png"></a>

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
