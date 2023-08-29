
	<?php
		/*
		Template Name: Customers Page
		*/
	?> 	

	<?php
		get_header();
	?> 

	<?php		
		if(page_access(true)){
			//if user have access show page
	?>

		<?php $customer_event = new WP_Query(array( 
			'posts_per_page' => 50, 
			'post_type' => 'wp_costumers' 
		)); ?>

	<section>			

		<div id="over_fl">	
			<table id="myTable">
				<tr id=headers>
					<th id = "leftSpace" onclick="sortTable(0)">Εταιρία</th>
					<th onclick="sortTable(1)">ΑΦΜ</th>
					<th onclick="sortTable(2)">Τηλέφωνο</th>
					<th onclick="sortTable(3)">Email</th>
					<th onclick="sortTable(4)">Διεύθυνση</th>
				</tr>
			
			<?php
				while($customer_event->have_posts ()) { 
					$customer_event->the_post(); ?>
					<tr>
						<td id="leftSpace"><?php the_field('company'); ?></td> 
						<td><?php the_field('afm'); ?></td>
						<td><?php the_field('phone'); ?></td> 
						<td><?php the_field('email'); ?></td> 
						<td><?php the_field('address'); ?></td>    
					</tr> 
				<?php }  ?>
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
			var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
			table = document.getElementById("myTable");
			switching = true;
			
			dir = "asc"; 
		
			while (switching) {				
				switching = false;
				rows = table.rows;
			
				for (i = 1; i < (rows.length - 1); i++) {				
				shouldSwitch = false;
				x = rows[i].getElementsByTagName("TD")[n];
				y = rows[i + 1].getElementsByTagName("TD")[n];
			
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
				if (shouldSwitch) {	
				rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
				switching = true;	
				switchcount ++;      
				} else {		
				if (switchcount == 0 && dir == "asc") {
					dir = "desc";
					switching = true;
				}
				}
			}
		}
		</script>      
	</body>
</html>