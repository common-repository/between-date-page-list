<?php
/**
 * Plugin Name: Between Date Page List
 * Description: For generate list of pages based on staring and ending dates. 
 * Version: 1.0.0
 * Author: bhaveshkhadodara
 * Author Email: bhaveshkhadodara999@gmail.com
 * License: GPL2
 */

add_action( 'admin_init', 'bdpl_between_date_page_list_css' );

function bdpl_between_date_page_list_css() {
    wp_enqueue_style( 'between-date-page-list', plugins_url( 'between-date-page-list/css/between-date-page-list.css' ));
}

add_action('admin_init','bdpl_between_date_page_list_js');

function bdpl_between_date_page_list_js() {
    wp_enqueue_script( 'between_date_page_list_js', plugins_url( '/js/between-date-page-list.js', __FILE__ ));
    wp_localize_script( 'between_date_page_list_js', 'my_ajax_object',
    array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

add_action('admin_init','bdpl_between_date_page_list_two_js');

function bdpl_between_date_page_list_two_js() {
    wp_enqueue_script( 'between_date_page_list_two_js', plugins_url( '/js/jquery.blockUI.js', __FILE__ ));
 }

add_action('admin_menu', 'bdpl_my_plugin_menu');

function bdpl_my_plugin_menu() {
	add_menu_page('Between Date Page List', 'Between Date Page List', 'administrator', 'between-date-page-list', 'bdpl_my_between_date_page_list', 'dashicons-admin-generic');
}

function bdpl_my_between_date_page_list() {
  echo  '<div class="dwpl-main blockMe">';
  echo  '<h2>Date Wise Page List</h2>';
  echo  '<a href="javascript:void(0)" class="csv-icon"></a>';
  echo  '<div class="frm-date">';
  echo  '<span>From Date</span>';
  echo  '<input type="date" name="frm-date">';
  echo  '</div>';
  echo  '<div class="to-date">';
  echo  '<span>To Date</span>';
  echo  '<input type="date" name="to-date">';
  echo  '</div>';
  echo  '<div class="sbmt-date">';
  echo  '<span>Geneate List</span>';
  echo  '</div>';
  echo  '<div class="result-block dwpl-output">';
  echo  '</div>';
}

function bdpl_between_date_page_list_ajax_fun(){
	global $wpdb, $post;
	$data_html .= '';
	$data_html .= '<table>';
		$data_html .= '<thead>
							<tr>
								<th>No</th>
								<th>Page Name</th>
								<th>Page Url</th>
								<th>Publish Date</th>
							</tr>
					   </thead>
					   <tbody>
					   		<tr style="display:none;">
					   			<td>No</td>
					   			<td>Page Name</td>
					   			<td>Page Url</td>
					   			<td>Publish Date</td>
					   		</tr>';

							if(!empty($_POST['frm_date']) && !empty($_POST['to_date'])){
								
								$frm_date = sanitize_text_field( $_POST['frm_date'] );
								$to_date = sanitize_text_field( $_POST['to_date'] );
								$frm_date_ex = explode("-",$frm_date);
								$frm_year = $frm_date_ex[0];
								$frm_month = $frm_date_ex[1];
								$frm_day = $frm_date_ex[2];
								$to_date_ex = explode("-",$to_date);
								$to_year = $to_date_ex[0];
								$to_month = $to_date_ex[1];
								$to_day = $to_date_ex[2];
								$frm_date_validator = checkdate($frm_month,$frm_day,$frm_year);
								$to_date_validator = checkdate($to_month,$to_day,$to_year);
								
								if($frm_date_validator == true && $to_date_validator == true){
									$args = array(
							            'post_type' => 'page',
							            'post_status' => 'publish',
									    'date_query' => array(
									        array(
									            'after'     => array(
																	'year'  => $frm_year,
																	'month' => $frm_month,
																	'day'   => $frm_day,
																),
									            'before'    => array(
																	'year'  => $to_year,
																	'month' => $to_month,
																	'day'   => $to_day,
																),
									            'inclusive' => true,
									        ),
									    ),
									);
								
								    $dwpl_cnt = 1;
									$query = new WP_Query( $args );
									if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
								    	$data_html .= "<tr>
								    					<td>".$dwpl_cnt."</td>";
								    	$data_html .= "<td>".get_the_title()."</td>";
								    	$data_html .= "<td>".get_permalink()."</td>";
								    	$data_html .= "<td>".get_the_date( 'Y-m-d' )."</td>
								    				  </tr>";
								    	$dwpl_cnt++;
									endwhile; endif;
									wp_reset_postdata();
								}
							}
			$data_html .= "</tbody>
				   </table>";	
			$res['date_html'] = $data_html;
	echo json_encode($res);
    exit();
}
add_action( 'wp_ajax_bdpl_between_date_page_list_ajax_fun', 'bdpl_between_date_page_list_ajax_fun' );
add_action( 'wp_ajax_nopriv_bdpl_between_date_page_list_ajax_fun', 'bdpl_between_date_page_list_ajax_fun');