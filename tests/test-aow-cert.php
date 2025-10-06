<?php
/**
 * Basic PHPUnit tests for the AOW Certificate plugin.
 * Note: These tests require the WP PHPUnit environment to run (not runnable here).
 */

class AOW_Certificate_Test extends WP_UnitTestCase {
    public function test_cpt_registered() {
        $this->assertTrue( post_type_exists( 'aow_certificate' ), 'aow_certificate post type should be registered' );
    }

    public function test_duplicate_prevention() {
        // Create a certificate post with a known title
        $title = 'TEST-AOW-1234';
        $post_id = wp_insert_post( array( 'post_type' => 'aow_certificate', 'post_title' => $title, 'post_status' => 'publish' ) );
        $this->assertIsInt( $post_id );

        // Simulate calling the create handler with same title (should return WP_Error)
        $req = new WP_REST_Request( 'POST', '/aow-cert/v1/create' );
        $req->set_body_params( array( 'studentName' => 'X', 'courseTitle' => 'Y', 'certificateId' => $title ) );
        $resp = aow_rest_create_certificate( $req );
        $this->assertInstanceOf( 'WP_Error', $resp );

        // Cleanup
        wp_delete_post( $post_id, true );
    }

    public function test_enqueue_creates_job_record() {
        global $wpdb;
        $jobs_table = $wpdb->prefix . 'aow_jobs';
        // Table should exist (created on plugin activation)
        $this->assertNotEmpty( $wpdb->get_var( "SHOW TABLES LIKE '$jobs_table'" ) );

        // Call enqueue handler directly
        $req = new WP_REST_Request( 'POST', '/aow-cert/v1/enqueue' );
        $req->set_body_params( array( 'cert_ids' => array('TEST-AOW-ENQ'), 'recipients' => array('dev@example.com'), 'format' => 'pdf' ) );
        $resp = aow_rest_enqueue_export_and_email( $req );
        $data = rest_get_server()->response_to_data( $resp );
        $this->assertArrayHasKey( 'job_id', $data );
        $job_id = intval( $data['job_id'] );
        $this->assertGreaterThan( 0, $job_id );

        // Cleanup job record
        $wpdb->delete( $jobs_table, array('id' => $job_id), array('%d') );
    }
}
