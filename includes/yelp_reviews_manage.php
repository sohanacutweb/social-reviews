<?php
function yelp_reviews_manage(){
	
	//Yelp Review
	
	$apiKey = 'eROoFdcbF7MVjW6Tspn21rW1RIZeEcIntal5IKQSTBtAjNen6ur5fxNwX3zAWemu66PXXzd7YXvqa39Bd7PQSinxVfst7iQsU-Yb_vUyHnF9RTbaEYZNkYKPFtqoWnYx';
$businessName = "north-india-restaurant-san-francisco";
$authorization = "Authorization: Bearer ".$apiKey;
$authA = array(
				$authorization,
				'Accept: application/json',
				'Content-Type: application/json'
			);
$url = 'https://api.yelp.com/v3/businesses/'.$businessName.'/reviews';
$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_HTTPHEADER,$authA);            
	$result = curl_exec($ch);
	curl_close($ch);
	$resultArray = json_decode($result);
	
	//echo '<pre>';
	//print_r($resultArray);	

?>
<div class="wrap">
	<h2>Yelp Reviews</h2>
	<div id="poststuff">
		<div id="post-body">
			<div class="postbox">
				<div class="inside">
				<?php 
				if(!empty($resultArray)){
					$YelpReviewData = $resultArray->reviews;
					?>
					<table class="wp-list-table widefat fixed striped pages">
						<tr>
							<th>
								Author Name
							</th>
							<th>
								Profile
							</th>
							<th>
								Rating
							</th>
							<th>
								Text
							</th>
							<th>
								Time
							</th>
							<th>
								Action
							</th>
						</tr>
						<?php foreach($YelpReviewData as $ratData){?>
						<tr>
							<td>
							<?php echo $ratData->user->name;?>
							</td>
							<td>
							<img src="<?php echo $ratData->user->image_url;?>" width="100" height="100"/>
							</td>
							<td>
							<?php echo $ratData->rating;?>
							</td>
							<td>
							<?php echo $ratData->text;?>
							</td>
							<td>
							<?php echo $ratData->time_created;?>
							</td>
							<td>
								<a href="">Add In List</a>
							</td>
						</tr>
				<?php } ?>
					</table>
				<?php }else{ echo 'No any Reviews'; } ?>
			</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>