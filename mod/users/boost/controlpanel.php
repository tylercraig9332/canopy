<?php
/**
 
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 */

$tabs[] = array('id' => 'my_page',
                'title' => 'My Page',
		'link'  => 'index.php?module=users&amp;action=user',
);

$link[] = array('label'       => 'User Administration',
		'restricted'  => TRUE,
		'url'         => 'index.php?module=users&amp;action=admin',
		'description' => 'Lets you create and edit users and groups.',
		'image'       => 'users.png',
		'tab'         => 'admin'
		);
