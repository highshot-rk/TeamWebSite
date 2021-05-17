<?php
namespace Elementor;

function timeline_awesome_general_elementor_init(){
	Plugin::instance()->elements_manager->add_category(
		'timeline_awesome-general-category',
		[
			'title'  => 'Timeline Awesome',
			'icon' => 'font'
		],
		1
	);
}
add_action('elementor/init','Elementor\timeline_awesome_general_elementor_init');
