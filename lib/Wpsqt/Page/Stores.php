<?php

	/**
	 * Base page for User/Franchise/Store Management
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */

class Wpsqt_Page_Stores extends Wpsqt_Page {

	public function process(){

		$customTable = new Store_List_Table();
		$customTable->prepare_items(false);
		$this->_pageVars['customtable'] = $customTable;
				
		$this->_pageView = "admin/store/index.php";
			
	}

}

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Store_List_Table extends WP_List_Table {
	
	public function prepare_items() {
	
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

	public function get_columns() {
        $columns = array(
			'cb'        		=> '<input type="checkbox" />',
            'id'         		=> 'ID',
            'location'			=> 'Location',
            'state'    			=> 'State',
            'completionRate'	=> 'Completion Rate'
        );

        return $columns;
    }    
        
	public function get_hidden_columns() {
		return array('id');
	}
	public function get_sortable_columns() {
		return array(
			'location' => array('location', false),
			'state' => array('state',true),
			'completionRate' => array('completionRate',false)
		);
	}

	function column_location($item) {
		$actions = array(
			'edit'      			=> sprintf('<a href="'.WPSQT_URL_STORES.'&section=edit&id='.$item['id'].'">Edit</a>'),
			'Add Franchise Owner'	=> sprintf('<a href="'.WPSQT_URL_FRANCHISEES.'&section=addnew&id_store='.$item['id'].'">Add Franchisee</a>'),
			'Add Employee' 			=> sprintf('<a href="'.WPSQT_URL_EMPLOYEES.'&section=addnew&id_store='.$item['id'].'">Add User</a>'),
			'delete'    			=> sprintf('<a href="'.WPSQT_URL_STORES.'&section=edit&action=delete&id='.$item['id'].'">Remove</a>')
		);

		return sprintf(
			'<a href="%s&location=%s">%s</a> %s',WPSQT_URL_EMPLOYEES,$item['location'],$item['location'], $this->row_actions($actions) 
		);
		
	}
	function column_state($item) {
		$state = Wpsqt_System::getStateName($item['state']);
		return sprintf(
			'<a href="%s&state=%s">%s</a>',WPSQT_URL_STORES,$state,$state
		);
	}
	function column_completionRate($item) {
		return sprintf(
			'<a href="%s&location=%s">%s</a>',WPSQT_URL_EMPLOYEES,$item['location'],Wpsqt_System::colorCompletionRate($item['completionRate'])
		);
	}
	function get_bulk_actions() {
		$actions = array(
			'delete'    => 'Remove'
		);
		return $actions;
	}
	function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />', $item['id']
        );    
    }
	public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'id':
            case 'location':
            case 'state':
            case 'completionRate' :
                return $item[ $column_name ];

            default:
                return print_r( $item, true ) ;
        }
    }

    
    private function table_data() {
    	global $wpdb;

		
		// process bulk action - delete
		if ($this->current_action()) {			
		
			if ($this->current_action() == "delete") {
				foreach ($_POST['id'] AS $id) {
					Wpsqt_System::_log("bulk remove store id=".$id);
					$wpdb->query($wpdb->prepare("DELETE FROM `".WPSQT_TABLE_STORES."` WHERE id = %d", array($id)));
				}
			}
		}
		
		$search = "";
		if (isset($_POST['s']) && $_POST['s']) {
			$search .= "AND location LIKE '%".$_POST['s']."%'";
		}
		if (isset($_GET['location']) && $_GET['location']) {
			$search .= " AND location='".$_GET['location']."' ";
		}
		if (isset($_GET['state']) && $_GET['state']) {
			$search .= " AND state=".Wpsqt_System::getStateId($_GET['state'])." ";
		}


		$orderby = "";
		if (isset($_GET['orderby']) && $_GET['orderby'] != 'completion') {
			$orderby = $_GET['orderby']." ".$_GET['order'];
			
			// manually add location to end of state search, or things look weird
			if ($_GET['orderby'] == 'state')
				$orderby .= ", location ".$_GET['order'];
		
		} else {
			$orderby ="state, location";
		}

		$sql = "SELECT id, location, state, completionRate 
			FROM `".WPSQT_TABLE_STORES."`
			WHERE 1=1
			".$search."
			ORDER BY ".$orderby;
		
		//Wpsqt_System::_log($sql);
						
		$res = $wpdb->get_results( $sql,ARRAY_A);
		
		$perPage = 20;
        $currentPage = $this->get_pagenum();
        $totalItems = count($res);

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $res = array_slice($res,(($currentPage-1)*$perPage),$perPage);

		
		return $res;	
	}
}