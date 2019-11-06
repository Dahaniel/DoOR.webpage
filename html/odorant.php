<!-- ### OrResponseProfilePage ### 07/2008 ### Daniel Münch -->
<html>
<?php $odorant = $_GET["odorant"]; ?>
<?php if (file_exists("data/odorants.csv") == FALSE){echo "Error, odorants.csv is missing";} else {  ?>
	<!--### getOdorantName from odor.csv ###-->
	<?php
	$row = 1;
	$odorcsv = fopen("data/odorants.csv", "r");
	while(($DataOdor = fgetcsv($odorcsv, 1000, ",")) !== FALSE)
	{
		$cols = count($odorant);
		if ($DataOdor[3] == $odorant){
			$odorname = $DataOdor[2];
			$CID = $DataOdor[4];
			$CAS = $DataOdor[5];
			$InChI = $DataOdor[6];
			$SMILES = $DataOdor[7];
			$InChIKey = $DataOdor[3];
		}
	}
	fclose($odorcsv);
	?>

	<head>
		<title>Responses elicited by <?php echo $odorname; ?> (<?php echo $odorant; ?>)</title>

		<meta name="author" content="Daniel Münch" />
		<meta name="keywords" content="<?php echo $OR ?>, Drosophila,odor, odorant, response profile, olfaction" />
		<meta http-equiv="Content-Language" content="de" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

		<link href="DoOR.css" type="text/css" rel="stylesheet">
		<link href="tablesort.css" type="text/css" rel="stylesheet">

		<script src="http://cactus.nci.nih.gov/chemical/structure/<?php echo $odorant;?>/twirl_cached/twirlymol" type="text/javascript"></script>
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
		<?php if ($odorant == ""){echo "No odorant specified...";}else {  ?>
			<?php if (file_exists("data/odorants/".$odorant.".csv") == FALSE){echo "Sorry, couldn't find data for ",$odorant,"...</br></br>\n
			This might happen when a data set is excluded during the merging process due to insufficient overlap 
			with other studies or insufficient quality of the fit.</br></br>\n
			All excluded studies are listed in <a href=\"https://github.com/ropensci/DoOR.data/blob/master/data/door_excluded_data.csv\"><span style=\"font-family: monospace;\">door_excluded_data</span></a></ br>\n
			To list all available RAW responses for the odorant you are looking for type (in R): </ br>
			<span style=\"font-family: monospace;\">na.omit(get_responses('",$odorant,"'))</span>
			";}else {  ?>

				<!--## HEADER ##-->
				<!--## CHANGE ODORANT ##-->
				<div style="text-align:right;margin:0;padding:0;">
					<form action="odorant.php" style="display:inline">
						<select name="odorant" size="1" onchange="this.form.submit()">
							<?php
							if (file_exists("data/odorants.csv")) {
								echo "<option selected value=\"\">change InChIKey</option>";
								$odorantscsv = fopen("data/odorants.csv", "r");
								while(($DataOdorants = fgetcsv($odorantscsv, 1000, ",")) !== FALSE){
									if ($DataOdorants[3] !="InChIKey"){
										$odorants[$DataOdorants[3]] = $DataOdorants[3];}}
										fclose($odorantscsv);
										natsort($odorants);
										foreach($odorants as $odorant_key => $odorant_value)  echo "<option value=\"",$odorant_key,"\">",$odorant_value,"</option>\n";
									}  else  {
										echo "<option selected>input file missing</option>\n";
									}
									?>
								</select>
							</form>
							<form action="odorant.php" style="display:inline">
								<select name="odorant" size="1" onchange="this.form.submit()">
									<?php
									if (file_exists("data/odorants.csv")) {
										echo "<option selected value=\"\">change odorant</option>";
										$odorantscsv = fopen("data/odorants.csv", "r");
										while(($DataOdorants = fgetcsv($odorantscsv, 1000, ",")) !== FALSE){
											if ($DataOdorants[2] !="Name"){
												$odorants[$DataOdorants[3]] = $DataOdorants[2];}}
												fclose($odorantscsv);
												natsort($odorants);
												foreach($odorants as $odorant_key => $odorant_value)  echo "<option value=\"",$odorant_key,"\">",$odorant_value,"</option>\n";
											}  else  {
												echo "<option selected>input file missing</option>\n";
											}
											?>
										</select>
									</form>
									<form action="odorant.php" style="display:inline">
										<select name="odorant" size="1" onchange="this.form.submit()">
											<?php
											if (file_exists("data/odorants.csv")) {
												echo "<option selected value=\"\">change CAS</option>";
												$odorantscsv = fopen("data/odorants.csv", "r");
												while(($DataOdorants = fgetcsv($odorantscsv, 1000, ",")) !== FALSE){
													if ($DataOdorants[5] !="CAS"){
														$odorants[$DataOdorants[3]] = $DataOdorants[5];}}
														fclose($odorantscsv);
														asort($odorants);
														foreach($odorants as $odorant_key => $odorant_value)  echo "<option value=\"",$odorant_key,"\">",$odorant_value,"</option>\n";
													}  else  {
														echo "<option selected>input file missing</option>\n";
													}
													?>
												</select>
											</form>
										</div>
										<!--## END OF HEADER ##-->

										<h1>Responses elicited by <span class="odorant"><?php echo $odorname; ?></span></h1>

										<h2>Odorant Information</a></h2>

										<table class = "info">
											<tr>
												<th>Name:</th>
												<td><?php echo $odorname; ?></td>
												<th>Links:</th>
											</tr>
											<tr>
												<th>InChI:</th>
												<td><?php echo $InChI; ?></td>
												<td><?php echo "<a class = \"extern\" href=\"http://pubchem.ncbi.nlm.nih.gov/summary/summary.cgi?cid=",$CID,"\">PubChem</a>" ;?></td>
											</tr>
											<tr>
												<th>InChIKey:</th>
												<td><?php echo "<span class = \"inchikey\">",$InChIKey,"</span>"; ?></td>
												<td><?php echo "<a class = \"extern\" href=\"http://pubchem.ncbi.nlm.nih.gov/summary/summary.cgi?cid=",$CID,"\">ChemSpider</a>" ;?></td>
											</tr>
											<tr>
												<th>SMILES:</th>
												<td><?php echo $SMILES; ?></td>
												<td><?php echo "<a class = \"extern\" href=\"https://www.ebi.ac.uk/chebi/advancedSearchFT.do?searchString=",$InChIKey		,"\">ChEBI	</a>" ;?></td>
											</tr>
											<tr>
												<th>CAS:</th>
												<td><?php echo $CAS; ?></td>
											</tr>
											<tr>
												<th>CID:</th>
												<td><?php echo $CID; ?></td>
											</tr>
										</table>


										<!--### DATASETS INFRMATION ###--->

										<h2>Available Datasets</h2>
										<?php
										if (file_exists("data/datasets/datasets_per_odorant.csv")) {
											$row = 1;
											$studiesByOdors = fopen("data/datasets/datasets_per_odorant.csv", "r");
											while(($Data = fgetcsv($studiesByOdors, 1000, ",")) !== FALSE) 	{
												$cols = count($Data);

												#$i = 2;
												if ($Data[1] == $odorant){
													$ii = 1;
													for($i=2; $i<$cols; $i++){
														if($Data[$i] != "NA"){
															if($ii != 1) {
																echo ", ";
															}
															echo "<a href=\"dataset.php?dataset=",$Data[$i],"\"><span class = \"dataset\">",$Data[$i],"</span></a>";
															$ii++;
														}}
														$row++;
													}
												}
												fclose($studiesByOdors);
											} else {
												echo "error - data missing\n";
											}

											?>


											<!--### END OF DATASETS INFRMATION ###--->

											<!--### ALPlot ###-->
											<h2>Activation Pattern</h2>
											<div class="ALImage"><img style = "width:100%; max-width:1200px;" src="data/odorants/<?php echo $odorant;?>.png"></div>
											<div class="center">
												<img style = "width:30%; max-width:300px;" src="data/odorants/<?php echo $odorant;?>_tuningCurve.png">
												<div id="twirlymol" width=300 height=250></div>
											</div>

										<!--### END OF ALPlot ###-->

										<!--### TABLE ###-->
										<h2>Response Table</h2>
										<?php
										$row = 1;
										$odorantFile = fopen("data/odorants/".$odorant.".csv", "r");

										echo "<table class=\"door focus-highlight\" id=\"datatable\">\n";

										while(($Data = fgetcsv($odorantFile, 1000, ",")) !== FALSE)
										{
											$cols = count($Data);

											if ($row == 1){
												echo"<thead>\n<tr>\n";
												$row++;
												echo "<th>",$Data[1], "</th>\n";
												echo "<th>",$Data[3], "</th>\n";
												echo "</tr>\n</thead>\n\n";
												echo "<tbody>\n";
											}

											else {
												echo"<tr>\n";
												$row++;
												echo"<td><a href=\"receptor.php?OR=",$Data[1],"\">",$Data[1],"</a></td>\n";
												if ($Data[3] !== "NA")
												{
													echo "<td><span class=\"model response\">",$Data[3],"</span></td>\n";
												}
												else
												{
													echo "<td>&nbsp;</td>\n";
												}
												echo"</tr>\n\n";
											}
										}
										echo "</tbody>\n";
										echo "</table>\n";

										fclose($odorantFile);
										?>
										<!--### END OF TABLE ###-->

										<!--### DOWNLOAD ###-->
										<h2>Download</h2>
										<a href="data/odorants/<?php echo$odorant;?>.csv">download</a> <?php echo$odorant?>.csv<br>
										<a href="data/odorants/<?php echo$odorant;?>.png">download</a> <?php echo$odorant?>.png<br>
										<a href="data/odorants/<?php echo$odorant;?>_tuningCurve.png">download</a> <?php echo$odorant?>_tuningCurve.png<br>

										<!--### END OF DOWNLOAD ###-->
										<?php }}
									}?>

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
