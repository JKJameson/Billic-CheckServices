<?php
class CheckServices {
	public $settings = array(
		'admin_menu_category' => 'General',
		'admin_menu_name' => 'Check Services',
		'admin_menu_icon' => '<i class="icon-tasks"></i>',
		'description' => 'Check service status from a comma seperated list of usernames.',
	);
	function admin_area() {
		global $billic, $db;
		
		$billic->set_title('Admin/Services');
		echo '<h1><i class="icon-tasks"></i> '.$this->settings['admin_menu_name'].'</h1><p>'.$this->settings['description'].'</p>';

		if (!empty($_POST['usernames'])) {
			$usernames = explode(',', $_POST['usernames']);
			if (count($usernames)==1) {
				$usernames = explode(PHP_EOL, str_replace("\r", '', $_POST['usernames']));
			}
			if (count($usernames)==1) {
				$billic->errors[] = 'Usernames not seperated with a comma or new line';	
			}
			if (empty($errors)) {
				$clause = implode(',', array_fill(0, count($usernames), '?'));
				$array = array();
				$array[] = 'SELECT `id`,`username`,`domainstatus` FROM `services` WHERE `username` IN ('.$clause.') ORDER BY `nextduedate` DESC';
				foreach($usernames as $username) {
					$array[] = $username;	
				}
				$services = call_user_func_array(array($db, 'q'), $array);
				
				echo '<table class="table table-striped table-hover">';
				foreach($usernames as $username) {
					$found = false;
					foreach($services as $service) {
						if ($service['username']==$username) {
							$found = true;
							break;
						}
					}
					echo '<tr>';
					if ($found) {
						switch($service['domainstatus']) {
							case 'Active':
								$label = 'success';
								break;
							case 'Suspended':
								$label = 'warning';
								break;
							default:
								$label = 'danger';
								break;
						}
						echo '<td><a href="/Admin/Services/ID/'.$service['id'].'/">'.$username.'</a></td><td><span class="label label-'.$label.'">'.$service['domainstatus'].'</span></td>';
					} else {
						echo '<td>'.$username.'</td><td><span class="label label-danger">Not Found</span></td>';
					}
					echo '</tr>';
				}
				echo '</table>';
			}
		}
		
		$billic->show_errors();
		
		echo '<form method="POST"><div align="center"><textarea name="usernames" class="form-control">'.htmlentities($_POST['usernames']).'</textarea><br><input type="submit" value="Check &raquo;" class="btn btn-success"></div></form>';
		
	}
}
