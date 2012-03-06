<?php
/**
 * Adds Presta4wp_Widget widget.
 */

require_once('PSWebServiceLibrary.php');

class KropesPrestaProductsWidget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'kropes_presta_products_widget', // Base ID
			'Kropes Prestashop Products', // Name
			array( 'description' => __( 'Vypíše produkty z kategorie HOME', 'presta4wp' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		// BODY
	try
	{
	  $options = get_option('Presta4wp_options');
	  $ws = new PrestaShopWebservice($options["url"], $options["key"], false);
	  $xml = $ws->get(array('resource' => 'products', 'display'=>'[id,id_default_image,price,condition,link_rewrite,name,description]', 'filter[id_category_default]'=>"[1]", 'filter[active]'=>"[1]" ));
	  // Here in $xml, a SimpleXMLElement object you can parse

	  echo "<ul>";
	  foreach ($xml->products->product as $attName => $r){
		$name = $r->name->xpath("language[@id=6]");
		$name = (string)$name[0];
		$description = $r->description->xpath("language[@id=6]");
		$description = (string)$description[0];
		$link_rewrite = $r->link_rewrite->xpath("language[@id=6]");
		$link_rewrite = (string)$link_rewrite[0];

		$id = (string) $r->id;
		$id_default_image = (string) $r->id_default_image;
		$price = (string) $r->price;
		$condition = (string) $r->condition;

		
		$prod = array("name"=>$name,"description"=>$description,"id"=>$id,"price"=>$price,"condition"=>$condition,"link_rewrite"=>$link_rewrite);

                echo "<li><a href='$options[url]/$id-$link_rewrite.html'>$name</a>";
		if($price) echo " <span class='presta4wp price'>$price Kč</span>";
		echo "</li>";
		#<img src='$options[url]/$id-$id_default_image/$link_rewrite.jpg'><
	  }
	  echo "</ul>";

	}
	catch (PrestaShopWebserviceException $ex)
	{
		echo 'Error : '.$ex->getMessage();
	}

		// BODY
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
		}
		else {
			$title = __( 'New title', 'presta4wp' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<?php 
	}

} // class Foo_Widget
