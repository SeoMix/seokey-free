<?php
/**
 * Audit Task class content loader : save data for each task
 *
 * @Loaded on 'init' & role editor
 *
 * @see     audit.php
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

// Save data for each task
class SeoKey_Audit_Launch_task_save_result {

    // TODO Comment
    public $audit_data = [];

    // TODO Comment
    public function run( $tasks_status  ) {
        foreach ( $tasks_status as $key => $data ) {
            $status = $data['priority'];
            $id = (int)$key;
            $datas = ( ! empty( $data['datas'] ) ) ? serialize( $data['datas'] ) : '';
            // TODO fix for terms + autors + other types
	        switch ( $data['item_type_global'] ) {
		        case 'global':
			        $name = $data['name'];
			        $item_type  = esc_html__( 'Entire website', 'seo-key' );
					break;
		        case 'post':
			        $name       = get_the_title( $id );
			        $item_type  = get_post_type( $id );
			        break;
		        case 'attachment':
			        $name       =  esc_html__( 'Medias without ALT','seo-key' );
			        $item_type  = 'attachment';
			        break;
                case 'author':
                    $name       =  esc_html( get_the_author_meta( 'display_name', $id ) );
                    $item_type  = 'author';
                    break;
	        }
            $sub_priority = ( !empty ( $data['sub_priority'] ) ) ? sanitize_title( $data['sub_priority'] ) : '';
			$this->audit_data[] = [
                'item_id'           => $id,
                'item_name'         => $name,
                'item_type'         => $item_type,
                'item_type_global'  => esc_html( $data['item_type_global'] ),
                'audit_type'        => esc_html( $data['audit_type'] ),
                'task'              => esc_html( $data['task'] ),
                'priority'          => substr( $status, 0, 1),
                'sub_priority'      => $sub_priority,
                'datas'             => $datas,
            ];
        }
        unset($tasks_status);
        global $wpdb;
        $table_name = esc_sql( $wpdb->base_prefix . 'seokey_audit' );
        $format = ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];
        foreach ( $this->audit_data as $issue ) {
            $wpdb->insert( $table_name, $issue, $format );
        }
        // End save
        return '';
    }
}
