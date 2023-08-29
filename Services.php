		
	<?php
		/*
		Template Name: Services Page
		*/			
	?>	
	
	<?php
	get_header();
	?> 

	<?php		
	if(page_access(true)){
			//if user have access show page
			?>

		<?php $services_event = new WP_Query(array( 
			'posts_per_page' => 50, 
			'post_type' => 'wp_services' )); 
		?>

		<?php $customer_event = new WP_Query(array( 
			'posts_per_page' => 50, 
			'post_type' => 'wp_costumers' )); 
		?>
		
		<?php 
			date_default_timezone_set('Europe/Athens');	
			$today = new DateTime('24-02-2022 00:00:00');
			$user = wp_get_current_user();	
			$user_email = $user->user_email;
			// echo $user_email;	
		?>

		<section>
				
			<div id="over_fl">
				<table id="myTable">
					<tr id="headers">
						<th id = "leftSpace" onclick="sortTable(0)">Υπηρεσία</th>
						<th onclick="sortTable(1)">Ιστοσελίδα</th>				
						<th onclick="sortTable(2)">Πλατφόρμα</th>
						<th onclick="sortTable(3)">Πακέτο</th>
						<th onclick="sortTable(4)">Developer</th>
						<th onclick="sortTable(5)">Ποσό affiliate</th>
						<th onclick="sortTable(6)">Ημερ/νία έναρξης</th>
						<th onclick="sortTable(7)">Ημερ/νία λήξης</th>
						<th onclick="sortTable(8)">Τύπος</th>
						<th onclick="sortTable(9)">Κόστος</th>
						<th onclick="sortTable(10)">Τιμολόγιο</th>
					</tr>
				
					<?php
					$services_counter = 0;
					while($services_event->have_posts() ) : 
						$services_event->the_post(); 
											
						$expiration_date = get_field('expiration_date');
						$temp_string = $expiration_date.' 00:00:00';
						$expdate = new DateTime($temp_string);
						$diff = $today->diff($expdate)->format("%r %a");
						
						//class name to change color with css
						$classname = ‘’;
						if($diff<=0){
						$classname = "expired";
						}
						if($diff==5 || $diff==3){ //an apomenoun 5 h 3 meres gia thn lhjh
							$classname = "soon";
						}
						if($diff > 0 && $diff < 3 || $diff > 3 && $diff < 5 ||  $diff > 5){
							$classname = "normal";
						}				
						$affiliate = get_field('affiliate');

						//if user is admin show table data
						if ( in_array( 'developer', (array) $user->roles ) && $user_email != $affiliate ) :
							//do nothing
						else :
						$services_counter++;
						?>	
						
						<?php 
						$affiliate_cost=get_field('affiliate_cost');
						$affiliate_cost_total += $affiliate_cost;
						 
						$service_cost=get_field('cost');
						$total_cost += $service_cost;
						$katharo_cost= $total_cost - $affiliate_cost_total;				
						?>
						
						<tr class="<?php echo $classname;?>"> 
							<td id="leftSpace"><?php the_field('service_name'); ?></td>								
							<td><?php the_field('websites'); ?></td> 
							<td><?php the_field('server'); ?></td> 
												
							<td><?php $packet_post = get_field('packet');
									if( $packet_post ): ?>
									<?php echo esc_html( $packet_post->post_title ); ?>
									<?php endif; ?></td>

							<td><?php the_field('developer'); ?></td>
							
							<td><?php the_field('affiliate_cost'); if($affiliate_cost != ''){echo'€';}else{echo'-';}?></td> 

							<td><?php the_field('start_date'); ?></td>
							<td><?php the_field('expiration_date'); ?></td>   
							<td><?php the_field('annual_monthly'); ?></td>
							<td><?php the_field('cost');echo'€'; ?></td> 
											
							<td><?php 
							if( have_rows('timologia') ):
								$latest_timologio_date = '';
								// Loop through rows
								while( have_rows('timologia') ) : the_row();				
									//get sub field value
									$sub_pdf = get_sub_field('pdf_file');
									$sub_date = get_sub_field('creation_date');
									$todate = strtotime($sub_date);
								
									//pick the pdf with the latest date
									if($latest_timologio_date == ''){
										$latest_timologio_date = $todate;									
									}else{
										if($todate > $latest_timologio_date){
											$latest_timologio_date = $todate;	
											$title=$sub_pdf['title'];
											$url=$sub_pdf['url'];									
										}else{
											//do_nothing
										}
									}	
							?>	
							<?php
								// End loop.
								endwhile;						
							?>				 
								<a href="<?php echo esc_attr($url); ?>" target="_blank" title="<?php echo esc_attr($title);?>"> <img id="tim_img" src="https://wordpress.designerd.gr/wp-content/uploads/2022/02/docum.svg"> </a>										
							<?php
							// No value. 
							else :
								// Do nothing...
							endif;					
							?>
							</td>
						</tr>

						<?php
						endif;	
						?>
						
					<?php 
					endwhile; 
					if($services_counter == 0): ?>
						<tr>
							<td colspan="11">Δεν βρέθηκαν διαθέσιμες υπηρεσίες</td>
						</tr>
						
					<?php else : //show cost total?>
					<tr>
						<td></td><td></td><td></td><td></td><td></td>
						<td><?php echo'Συνολικό Ποσό Affiliate:&nbsp;'. $affiliate_cost_total.'€';?></td>
						<td></td><td></td><td></td>
						<td><?php printf("Συνολικό Κόστος:&nbsp;". $total_cost."€\n Καθαρό Κόστος:&nbsp;".$katharo_cost."€");?></td>
						<td></td>
					</tr>

					<?php
						endif;	
					?>
				</table>
				
			</div>
			
		</section>
	
	

	<?php
	}else{
		if( is_user_logged_in() ){	?>
			<p>Forbidden Access</p>	
		<?php 
		}else{ ?>
			<p style="padding-left:40px">Συνδέσου για να δεις τις υπηρεσίες:</p>	
		<?php	echo the_content(); // user login plugin	
		} 			
	}					
	?> 
	<?php	
	get_footer();
	?>

	<script>
	function sortTable(n) {
		var table, rows, switching, i, x, y, shouldSwitch, dir, num_dir, switchcount = 0;
		table = document.getElementById("myTable");
		switching = true;

		var datesplit1,datesplit2;
		dir = "asc"; 
		num_dir = "increasing";
		
		while (switching) {				
			switching = false;
			rows = table.rows;
			//headers = document.getElementsByTagName('TH');
			
			for (i = 1; i < (rows.length -1 ); i++) {				
				shouldSwitch = false;
				x = rows[i].getElementsByTagName("TD")[n];
				//console.log(x);
				y = rows[i + 1].getElementsByTagName("TD")[n];
				//console.log(y);	
				if (n==6 || n==7) {
					//split date
					datesplit1 = x.innerHTML.split("-");
					//console.log(datesplit1);
					datesplit2 = y.innerHTML.split("-");
					//console.log(datesplit2);
				
					if(num_dir == "increasing"){
						// Check if the two rows should switch place
						if(Number(datesplit1[2]) > Number(datesplit2[2])){
							//year is smaller
							shouldSwitch = true;
							break;
						}
						else if(Number(datesplit1[2]) == Number(datesplit2[2]) ){
							//year is the same
							if(Number(datesplit1[1]) > Number(datesplit2[1])){
								//month is smaller
								shouldSwitch = true;
								break;
							}else if(Number(datesplit1[1]) == Number(datesplit2[1])){
								//month is the same
								if(Number(datesplit1[0]) > Number(datesplit2[0])){
									//day is smaller
									shouldSwitch = true;
									break;
								}else{
									//do nothing
								}
							}else{
								//do nothing
							}
						}else{
							//do nothing
						}
					}else if(num_dir == "declining"){
						// Check if the two rows should switch place
						if(Number(datesplit1[2]) < Number(datesplit2[2])){
							//year is smaller
							shouldSwitch = true;
							break;
						}
						else if(Number(datesplit1[2]) == Number(datesplit2[2]) ){
							//year is the same
							if(Number(datesplit1[1]) < Number(datesplit2[1])){
								//month is smaller
								shouldSwitch = true;
								break;
							}else if(Number(datesplit1[1]) == Number(datesplit2[1])){
								//month is the same
								if(Number(datesplit1[0]) < Number(datesplit2[0])){
									//day is smaller
									shouldSwitch = true;
									break;
								}else{
									//do nothing
								}
							}else{
								//do nothing
							}
						}else{
							//do nothing
						}
					}
				}else{
					if (dir == "asc") {
						if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {					
						shouldSwitch= true;
						break;
						}
					} else if (dir == "desc") {
						if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {		
						shouldSwitch = true;
						break;
						}		
					}
				}
			}
			//If a switch has been marked, make the switch
			if (shouldSwitch) {	
			rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
			switching = true;	
			switchcount ++;      
			} else {		
				if (switchcount == 0 && dir == "asc") {
					dir = "desc";
					switching = true;
				}
				if (switchcount == 0 && num_dir == "increasing") {
					num_dir = "declining";
					switching = true;
				}
			}
		}
	}	
	sortTable(7);
	</script>  

		

