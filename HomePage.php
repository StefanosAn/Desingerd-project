<?php
    /*
    Template Name: Home Page
    */			
?>	

<?php
    get_header();
?> 
<article>
    <?php		
        if(page_access(true)){
            //if user have access show page
            
            ?> 
        <p style="text-align: center">Καλώς ήρθατε στο σύστημα διαχείρισης υπηρεσιών της Designerd, μέσα από το οποίο μπορείτε να δείτε όλες τις διαθέσιμες υπηρεσίες των πελατών σας.</p>

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
</article>
<?php	
    get_footer();
?>