<?php 
foreach ($arts as $artkey => $art) {
	echo '<h3>'.$art.SP.count($albums[$artkey]).'</h3>';
	foreach ($albums[$artkey] as $albumkey => $album) {
		echo '<h4>'.$album['Album']['name'].$album['Album']['year'].'</h4>';
		if(empty($albums[$artkey])){
			echo '<p>Khong co bai hat</p>'; continue;
		}
		echo "<table width='100%'>
			<thead>
				<th>#</th>
				<th>Song</th>
				<th>Lyrics Available</th>
				<th>Writers</th>
				<th>Publish</th>
				<th>Link</th>
			</thead>";
			$i=0;
		foreach ($tracks[$album['Album']['id']] as $trackkey => $track) {
		?>
			<tr>
				<td><?php echo ++$i; ?></td>
				<td><?php echo $track['Lyrics']['name']; ?></td>
				<td><?php echo $track['Lyrics']['content']?'co':'chưa'; ?></td>
				<td><?php echo $track['Lyrics']['writer']; ?></td>
				<td><?php echo $track['Lyrics']['publisher']; ?></td>
				<td><a href="<?php echo ML.$track['Lyrics']['link'].DOT.PAGE_SUFFIX ?>" target="_blank">link</a></td>
			</tr>
<?php
		}
		echo '</table>';
	}
}
 ?>
