<?php
/*
** Base navigation for application
*/

/* ROOT Node */
$_NAVIGATION = new NavigationNode('ROOT');

/* Home Node */
$Home = new NavigationNode('Home','/home/index/');
$Home->AddChild(new NavigationNode('MotD','/home/motd/'));
$Home->AddChild(new NavigationNode('Wall','/wall/index/'));

/* Media Node */
$Media = new NavigationNode('Media','/media/index/');
$Media->AddChild(new NavigationNode('Pictures','/media/pictures/'));

$Music = new NavigationNode('Music','/media/music/');
$Music->AddChild(new NavigationNode('Jukebox','/media/jukebox/'));

$Media->AddChild($Music);
$Media->AddChild(new NavigationNode('Flash','/media/flash/'));
$Media->AddChild(new NavigationNode('Files','/media/files/'));
$Media->AddChild(new NavigationNode('Video','/media/video/'));

/* User and Admin */
$User = null;
if (Auth::UserAuthenticated()) {
	$User = new NavigationNode('Profile','/user/profile/');
	$User->AddChild(new NavigationNode('Logout','/user/logout/'));
} else {
	$User = new NavigationNode('Login','javascript:getBlock("LoginForm",renderFlyout);');
	$User->AddChild(new NavigationNode('Register','/user/register/'));
}
if (Auth::UserAuthorized(4)) {
	$User->AddChild(new NavigationNode('Admin Panel','/admin/index/'));
}

// Assign
$_NAVIGATION->AddChild($Home);
$_NAVIGATION->AddChild($Media);
$_NAVIGATION->AddChild($User);
$_NAVIGATION->AddChild(new NavigationNode('Test'));
// Clean-Up
unset($Home,$Music,$Media,$User);