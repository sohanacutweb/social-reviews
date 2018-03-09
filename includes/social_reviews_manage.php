<?php
function social_reviews_manage(){
	//Google Reviews
	$api_data = array();
	$query_args = array(
			'key'		=>	esc_html( 'AIzaSyAUy00JGj2tZT9SqRdbSQNNqcWN6Dju54A' ),
			'placeid'	=>	esc_html( 'ChIJQ0MizyGTDzkRsmpN7ZM96_I' )
		);
	$api_data['api_url'] = add_query_arg( $query_args, 'https://maps.googleapis.com/maps/api/place/details/json' );
	$request = wp_remote_get( $api_data['api_url'], $api_data['api_remote_args'] );
	$body = wp_remote_retrieve_body( $request );
	$api_response = json_decode( $body, true );
	//echo '<pre>';
	//print_r($api_response);
	$reviewsArray = $api_response['result']['reviews'];
	

?>
<div class="wrap">
	<h2>Google Reviews</h2>
	<div id="poststuff">
		<div id="post-body">
			<div class="postbox">
				<div class="inside">
				<?php 
				if(!empty($reviewsArray)){
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
						<?php foreach($reviewsArray as $ratData){?>
						<tr>
							<td>
							<?php echo $ratData['author_name'];?>
							</td>
							<td>
							<img src="<?php echo $ratData['profile_photo_url'];?>" />
							</td>
							<td>
							<?php echo $ratData['rating'];?>
							</td>
							<td>
							<?php echo $ratData['text'];?>
							</td>
							<td>
							<?php echo $ratData['relative_time_description'];?>
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