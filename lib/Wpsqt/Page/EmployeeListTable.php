<?php
// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Employee_List_Table extends WP_List_Table {

	protected $isFranchiseOwner = 'FALSE';
	
	public function prepare_items($isFranchisee) {
	
        $this->isFranchiseOwner = $isFranchisee ? 'TRUE' : 'FALSE';
        $this->page_url = $isFranchisee ? WPSQT_URL_FRANCHISEES : WPSQT_URL_EMPLOYEES;
	
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

	public function get_columns() {
        $columns = array(
			'cb'        => '<input type="checkbox" />',
            'id'         	=> 'ID',
            'id_user'       => 'User ID',
            'name' 			=> 'Name',
            'completion'	=> 'Completion',
            'location'		=> 'Location',
            'state'    		=> 'State'
        );

        return $columns;
    }    
        
	public function get_hidden_columns() {
		return array('id', 'id_user');
	}
	public function get_sortable_columns() {
		return array(
			'name' => array('name', false),
			'completion' => array('completion',false),
			'location' => array('location', false),
			'state' => array('state',true)
		);
	}
	function column_name($item) {
		$actions = array(
			'results' 	=> sprintf('<a href="'.WPSQT_URL_EMPLOYEES.'&subsection=results&id_user=%d">Results</a>',$item['id_user']),
			'edit'      => sprintf('<a href="'.$this->page_url.'&section=edit&id=%d">Edit</a>',$item['id']),
			'delete'    => sprintf('<a href="'.$this->page_url.'&section=edit&action=delete&id=%d">Remove</a>',$item['id'])
		);

		return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions) );
	}
	function column_location($item) {
		return sprintf(
			'<a href="%s&location=%s">%s</a>',$this->page_url,$item['location'],$item['location']
		);
	}

	function column_state($item) {
		$state = Wpsqt_System::getStateName($item['state']);
		return sprintf(
			'<a href="%s&state=%s">%s</a>',$this->page_url,$state,$state
		);
	}
	function column_completion($item) {
		return sprintf('<a href="'.WPSQT_URL_EMPLOYEES.'&subsection=results&id_user=%d">%s</a>',$item['id_user'],Wpsqt_System::colorCompletionRate($item['completion']));
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
            case 'id_user':
            case 'name':
            case 'completion' :
            case 'location':
            case 'state':
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
					Wpsqt_System::_log("bulk remove employee id=".$id);
					$wpdb->query($wpdb->prepare("DELETE FROM `".WPSQT_TABLE_EMPLOYEES."` WHERE id = %d", array($id)));
				}
			}
		}
		
		$search = "";
		if (isset($_POST['s']) && $_POST['s']) {
			$search = "AND (user.display_name LIKE '%".$_POST['s']."%' OR store.location LIKE '%".$_POST['s']."%')";
		}
		if (isset($_GET['location']) && $_GET['location']) {
			$search .= " AND store.location='".$_GET['location']."' ";
		}
		if (isset($_GET['state']) && $_GET['state']) {
			$search .= " AND store.state=".Wpsqt_System::getStateId($_GET['state'])." ";
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

		$sql = "SELECT emp.id AS id, user.id AS id_user, user.display_name AS name, store.location, store.state 
			FROM `".WPSQT_TABLE_EMPLOYEES."` emp
			INNER JOIN `".WP_TABLE_USERS."` user ON (emp.id_user = user.id)
			INNER JOIN `".WPSQT_TABLE_STORES."` store ON (emp.id_store = store.id)
			WHERE emp.franchisee = ".$this->isFranchiseOwner.
				$search."
			ORDER BY ".$orderby;
						
		$res = $wpdb->get_results( $sql,ARRAY_A);

		// add completion rates
		for($i=0;$i<count($res);$i++) {
			$res[$i]['completion'] = Wpsqt_System::getEmployeeCompletionRate($res[$i]['id_user']);
		}
		// and if required sort by completion here
		if (isset($_GET['orderby']) && $_GET['orderby'] == 'completion') {
			if ($_GET['order'] == "asc")
				usort($res,"cmp");
			else
				usort($res,"cmpR");
		}
		
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

// custom sort function for sort by completion rate
function cmp($a, $b) {
	return $a["completion"] > $b["completion"];	
}
// probably a better way of doing this.... but this way is exceptionally straightforward
function cmpR($a, $b) {
	return $a["completion"] < $b["completion"];	
}