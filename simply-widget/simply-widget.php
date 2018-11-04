<?php
/*
Plugin Name:Simply Widget
Plugin URI: http://www.websitecreator.pl
Description: Display taxonomies, comments , authors
Author: Pawel Kalisz
Version: 1.0
Author URI: http://www.websitecreator.pl wpisów:
*/
class Simply_Widget extends WP_Widget{
    
    function __construct(){

        $widget_options = array(
            'classname' => 'simply-widget',
            'description' => 'entries, tags'
        );
        
        parent::__construct('simply-widget', 'Simply Widget, entries, etc...', $widget_options);
		
		 add_action('wp_print_styles', array($this, 'registerStyles'));
		 add_action('wp_enqueue_scripts', array($this, 'registerScripts'));
		
    }
    function registerStyles(){
             wp_enqueue_style(
                     'styles-widget',
                     plugins_url('/styles/styles.css', __FILE__)
                    );
         }
		 
		 function registerScripts(){
         
   
              wp_deregister_script( 'jquery' );
				wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js');
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script('my-jqyery', plugins_url('/js/scripts.js', __FILE__), array('jquery') );
						 
         }
		 
	function widget($args, $instance){
        
        extract($args);
        $title = apply_filters( 'widget_title', $instance[ 'title' ] );
     
		 echo $before_widget;
		 
        if ( $title ) {
            echo $before_title . $title . $after_title;
        }
        //posts
        $entries_count = (!empty($instance['entries_count'])) ? (int)$instance['entries_count'] : 5;
        $entry_type = (!empty($instance['entry_type'])) ? $instance['entry_type'] : 'post';
        
        //comments
		 $comments_count = (!empty($instance['comments_count'])) ? (int)$instance['comments_count'] : 3;
		 //wp tag clouds
		  $taxonomy = (!empty($instance['taxonomy'])) ? $instance['taxonomy'] : 'category';
		  //checkbox
		  $avatar = $instance[ 'avatar' ] ? 'true' : 'false'; 
		  $author = $instance[ 'author' ] ? 'true' : 'false'; 
		  $data   = $instance[ 'data' ] ? 'true' : 'false'; 
		  $comment_content = $instance[ 'comment_content' ] ? 'true' : 'false'; 
		 //comments
		 function fetchRecentComments($limit = 3) {

	 global $wpdb;
        
		$limit = (int)$limit;
		
        $res = $wpdb->get_results("
            SELECT C.*, P.post_title
                FROM {$wpdb->comments} C
                    LEFT JOIN {$wpdb->posts} P ON C.comment_post_ID = P.ID
                WHERE comment_approved = 1
                ORDER BY comment_date_gmt DESC
                LIMIT {$limit}
        ");
                
        return $res;
}
		 $recent_comments = fetchRecentComments($comments_count);
	///end comments	
	
      if( 'post' == $instance[ 'entry_type' ] )
        echo '<h3 class="post" >Posty</h3>';
	
	     elseif( 'page' == $instance[ 'entry_type' ] )
		echo'<h3 class="pages">Strony</h3>';
        //posts
        $loop = new WP_Query(array(
                    'post_type' => $entry_type,
                    'posts_per_page' => $entries_count
                ));
        
        if(!$loop->have_posts()){
            echo '<p>No posts</p>';
        }else{
            
            echo '<ul>';
            while($loop->have_posts()){
                $loop->the_post();?><li><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></li><?php
            }
            echo '</ul>';
            
        }?>
		<p class="comment">Comments:</p>
		<?php //comments
		foreach($recent_comments as $comment){
            $date = new \DateTime($comment->comment_date_gmt);?>
                <section id="fadeincom" >
                    <header>
                       <small><?php if( 'on' == $instance[ 'author' ] ) echo 'Author: '.$comment->comment_author;
					   if( 'on' == $instance[ 'data' ] ) echo 'day: '.$date->format('d.m.Y'); ?></small>
                        <?php echo $comment->post_title; ?>
                    </header>
                    <?php
					 if( 'on' == $instance[ 'avatar' ] ) echo get_avatar($comment->user_id, 69); ?>
                    <blockquote>
                        <?php if( 'on' == $instance[ 'comment_content' ] ) echo $comment->comment_content; ?>
                    </blockquote>
                </section>
			
			<?php } ?>
	
		<p class="taxonomy">Taxonomy :</p>
		<section id="fadein">
				<div class="tag-cloud">
        
			  <?php wp_tag_cloud(array(
					'taxonomy' => $taxonomy,
					'smallest' => 11,
					'largest' => 16.5,
					'unit' => 'px'
				)); ?>
				
				</div>
		</section>
        <?php echo $after_widget;  
   }
    
	//update
	function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    
    // The update for the variable of the checkbox
	$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
    $instance['entries_count'] = $new_instance[ 'entries_count' ];
	$instance['entry_type'] =  $new_instance[ 'entry_type' ];
    $instance[ 'avatar' ] = $new_instance[ 'avatar' ];
	$instance[ 'author' ] = $new_instance[ 'author' ];
	$instance[ 'data' ] = $new_instance[ 'data' ];
	$instance['comments_count'] = $new_instance['comments_count'];
	$instance[ 'comment_content' ] = $new_instance[ 'comment_content' ];
	$instance[ 'taxonomy' ] = $new_instance[ 'taxonomy' ];
    return $instance;
}
    
    function form($instance){//BACK END
    $defaults = array( 'title' => 'Simply Widget', 'entries, etc...',
					   'avatar' => 'off',
					   'author' => 'off',
					   'data' => 'off',
					   'comments_count'=> 3,
					   'comment_content' => 'off',
					   'entries_count' => 3,
					   'entry_type' => 'post',
					   'taxonomy'=> 'category'
					   );
    $instance = wp_parse_args( ( array ) $instance, $defaults );  ?>
         
       
        <label id="<?php echo $this->get_field_id( 'title' ); ?>"  for="<?php echo $this->get_field_id( 'title' ); ?>">Title</label>
        <input id="<?php echo $this->get_field_id( 'title' ); ?>" class = "widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance[ 'title' ] ); ?>" />
    
        <br/>
       
       <label for="<?php echo $this->get_field_id('entry_type') ?>">
            CPT type:
            <select 
                name="<?php echo $this->get_field_name('entry_type') ?>"
                id="<?php echo $this->get_field_id('entry_type') ?>" class = "widefat">
                <?php
                
                    $entries_types_list = get_post_types(NULL, 'object');
                    $exclude = array('attachment', 'revision', 'nav_menu_item');
                    
                    $curr_type = $instance['entry_type'];
                    
                    foreach($entries_types_list as $type){
                        $name = $type->name;
                        if(!in_array($name, $exclude)){
                            $label = $type->labels->name;
                            if($curr_type == $name){
                                echo '<option selected="selected" value="'.$name.'">'.$label.'</selected>';
                            }else{
                                echo '<option value="'.$name.'">'.$label.'</selected>';
                            }
                        }
                    }
                ?>
            </select>
        </label>
        <br/>
        <label for="<?php echo $this->get_field_id('entries_count') ?>">
            Posts count:
            <select 
                name="<?php echo $this->get_field_name('entries_count') ?>"
                id="<?php echo $this->get_field_id('entries_count') ?>" class = "widefat">
                <?php
                    $opts = array(5, 10, 15);
                    $curr = (int)esc_attr($instance['entries_count']);
                    foreach($opts as $val){
                        if($curr == $val){
                            echo '<option selected="selected" value="'.$val.'">'.$val.'</selected>';
                        }else{
                            echo '<option value="'.$val.'">'.$val.'</selected>';
                        }
                    }
                ?>
            </select>
        </label>
		</br>
        <label for="<?php echo $this->get_field_id('comments_count') ?>">
            Comments count:
            <select 
                name="<?php echo $this->get_field_name('comments_count') ?>"
                id="<?php echo $this->get_field_id('comments_count') ?>" class = "widefat"
                >
                <?php
                    $opts = array(1,2, 3);
                    $curr = (int)esc_attr($instance['comments_count']);
                    foreach($opts as $val){
                        if($curr == $val){
                            echo '<option selected="selected" value="'.$val.'">'.$val.'</selected>';
                        }else{
                            echo '<option value="'.$val.'">'.$val.'</selected>';
                        }
                    }
                ?>
            </select>
        </label>
		</br>
        <label for="<?php echo $this->get_field_id('taxonomy') ?>">
            Taxonomy:
            <select 
                name="<?php echo $this->get_field_name('taxonomy') ?>"
                id="<?php echo $this->get_field_id('taxonomy') ?>" class = "widefat">
                <?php
                
                    $taxonomies_list = get_taxonomies(NULL, 'object');
                    $exclude = array('', 'type');
                    
                    $curr_taxonomy = $instance['taxonomy'];
                    
                    foreach($taxonomies_list as $taxonomy){
                        $name = $taxonomy->name;
                        if(!in_array($name, $exclude)){
                            $label = $taxonomy->labels->name;
                            if($curr_taxonomy == $name){
                                echo '<option selected="selected" value="'.$name.'">'.$label.'</selected>';
                            }else{
                                echo '<option value="'.$name.'">'.$label.'</selected>';
                            }
                        }
                    }
                ?>
            </select>
        </label>
		
		 <!-- The checkbox -->
    <p>
        <input  type="checkbox" <?php checked( $instance[ 'avatar' ], 'on' ); ?>  id="<?php echo $this->get_field_id( 'avatar' ); ?>" class = "widefat" name="<?php echo $this->get_field_name( 'avatar' ); ?>" /> 
        <label for="<?php echo $this->get_field_id( 'avatar' ); ?>">Show avatar</label>
    </p>
	 <p>
        <input  type="checkbox" <?php checked( $instance[ 'author' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'author' ); ?>" class = "widefat" name="<?php echo $this->get_field_name( 'author' ); ?>" /> 
        <label for="<?php echo $this->get_field_id( 'author' ); ?>">Show author</label>
    </p>	
	 <p>
        <input  type="checkbox" <?php checked( $instance[ 'data' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'data' ); ?>" class = "widefat" name="<?php echo $this->get_field_name( 'data' ); ?>" /> 
        <label for="<?php echo $this->get_field_id( 'data' ); ?>">Show data</label>
    </p>	
	 <p>
        <input  type="checkbox" <?php checked( $instance[ 'comment_content' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'comment_content' ); ?>" class = "widefat" name="<?php echo $this->get_field_name( 'comment_content' ); ?>" /> 
        <label for="<?php echo $this->get_field_id( 'comment_content' ); ?>">Show comment content</label>
    </p>	
		
	<?php 	
    }
    
}

function simple_widget_init(){
    register_widget('Simply_Widget');
}

add_action('widgets_init', 'simple_widget_init');
?>