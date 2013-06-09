<?php defined("SYSPATH") or die("No direct script access.") ?>  
<div id="g-admin-code-block">
  <h2><?= t("Movie overly page administration") ?></h2>
	<table  border="1" bordercolor="#000000" 
			style="background-color:#FFFFFF" width="550px" 
			cellpadding="1" cellspacing="1">
	<tr>
	    <td>Icon:</td>
		<td><img src="<?= url::file("modules/movie_overlay/images/1.png") ?>" /></td>
		<td><img src="<?= url::file("modules/movie_overlay/images/2.png") ?>" /></td>
		<td><img src="<?= url::file("modules/movie_overlay/images/3.png") ?>" /></td>
		<td><img src="<?= url::file("modules/movie_overlay/images/4.png") ?>" /></td>
		<td><img src="<?= url::file("modules/movie_overlay/images/5.png") ?>" /></td>
		<td><img src="<?= url::file("modules/movie_overlay/images/6.png") ?>" /></td>
		<td><img src="<?= url::file("modules/movie_overlay/images/7.png") ?>" /></td>
		<td><img src="<?= url::file("modules/movie_overlay/images/8.png") ?>" /></td>
		<td><img src="<?= url::file("modules/movie_overlay/images/9.png") ?>" /></td>
		<td><img src="<?= url::file("modules/movie_overlay/images/10.png") ?>" /></td>
	</tr>
	<tr>
		<td>Number:</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td>
		<td>6</td><td>7</td><td>8</td><td>9</td><td>10</td>
	</tr>
	</table>
  <?= $form ?>
</div>
