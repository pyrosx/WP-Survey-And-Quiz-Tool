<?php
// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Employee_List_Table extends WP_List_Table {

	protected $isFranchiseOwner = 'FALSE';
	protected $isInactive;
		
	public function prepare_items($isFranchisee, $isInactive = false) {
	
        $this->isFranchiseOwner = $isFranchisee ? 'TRUE' : 'FALSE';
        $this->page_url = $isFranchisee ? WPSQT_URL_FRANCHISEES : WPSQT_URL_EMPLOYEES;
	
		if ($isInactive) { 
			$this->isInactive = true;
			$this->page_url = WPSQT_URL_EMPLOYEES."&inactive=true";
		}
	
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
            'email'			=> 'Email',
            'completion'	=> 'Completion'
        );
        
        if (!$this->isInactive) {		
			$columns['location'] = 'Location';
			$columns['state'] = 'State';
        }

        return $columns;
    }    
        
	public function get_hidden_columns() {
		return array('id', 'id_user');
	}
	public function get_sortable_columns() {
		return array(
			'name' => array('name', false),
			'email' => array('email',false),
			'completion' => array('completion',false),
			'location' => array('location', false),
			'state' => array('state',true)
		);
	}
	function column_name($item) {
		$actions = array(
			'results' 	=> sprintf('<a href="'.WPSQT_URL_EMPLOYEES.'&subsection=results&id_user=%d">Results</a>',$item['id_user']),
			'edit'      => sprintf('<a href="'.admin_url('user-edit.php').'?user_id=%d">Edit Profile</a>',$item['id_user']),		
			'remind' 	=> sprintf('<a href="'.$this->page_url.'&subsection=remind&id_user=%d">Send Reminder</a>',$item['id_user']),	
			'reinvite' 	=> sprintf('<a href="'.$this->page_url.'&subsection=reinvite&id_user=%d">Reset and reinvite</a>',$item['id_user']),
			'certificate' => sprintf('<a href="%s?display_name=%s&completed_date=%d">Certificate</a>',plugins_url('cert/pdf.php',WPSQT_FILE),$item['name'],Wpsqt_System::getEmployeeCompletedDate($item['id_user'])),
			'add'		=> sprintf('<a href="'.$this->page_url.'&subsection=addnew&id_user=%d">Add to other store</a>',$item['id_user']),
			'remove'    => sprintf('<a href="'.$this->page_url.'&section=edit&action=remove&id=%d">Remove from store</a>',$item['id']),
			'delete'	=> sprintf('<a href="'.$this->page_url.'&section=edit&action=delete&id_user=%d">Delete</a>',$item['id_user'])
		);	
		
		// remove reminder link if completion is at 100%
		if ($item['completion']==100) 
			unset($actions['remind']);
		else 
			unset($actions['certificate']);
		
		// Fix a few things for Inactive page
		if ($this->isInactive) {
			unset($actions['remove']);
			$actions['add'] = str_replace("other ", "", $actions['add']);
		}
		return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions) );
	}
	function column_location($item) {
		return sprintf(
			'<a href="%s&location=%s">%s</a>',WPSQT_URL_EMPLOYEES,$item['location'],$item['location']
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
			'remove'    => 'Remove from store'
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
            case 'email' :
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

		
		// process bulk action - remove
		if ($this->current_action()) {			
		
			if ($this->current_action() == "remove") {
				foreach ($_POST['id'] AS $id) {
					Wpsqt_System::_log("bulk remove employee id=".$id);
					$wpdb->query($wpdb->prepare("DELETE FROM `".WPSQT_TABLE_EMPLOYEES."` WHERE id = %d", array($id)));
				}
				add_action( 'admin_notices', 'my_admin_notice' );
			}
		}
		

		
		$search = "";
		if (isset($_POST['s']) && $_POST['s']) {
			$search = " AND (user.display_name LIKE '%".$_POST['s']."%' OR user.user_email LIKE '%".$_POST['s']."%' OR store.location LIKE '%".$_POST['s']."%')";
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

		if (!$this->isInactive) {
			$sql = "SELECT emp.id AS id, user.id AS id_user, user.display_name AS name, user.user_email AS email, store.location, store.state 
				FROM `".WPSQT_TABLE_EMPLOYEES."` emp
				INNER JOIN `".WP_TABLE_USERS."` user ON (emp.id_user = user.id)
				INNER JOIN `".WPSQT_TABLE_STORES."` store ON (emp.id_store = store.id)
				WHERE emp.franchisee = ".$this->isFranchiseOwner.
					$search."
				ORDER BY ".$orderby;
		} else {
			$sql = "SELECT emp.id AS id, user.id AS id_user, user.display_name AS name, user.user_email AS email
				FROM `".WP_TABLE_USERS."` user
				LEFT OUTER JOIN `".WPSQT_TABLE_EMPLOYEES."` emp ON (emp.id_user = user.id)
				WHERE emp.id_user is NULL ".
					$search."
				ORDER BY user.display_name";
		}
// 		Wpsqt_System::_log($sql);
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
