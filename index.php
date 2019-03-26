<?php

require_once 'vendor/autoload.php';
require 'lib/application.php';
require 'lib/model/taskmodel.php';

$loader = new Twig_Loader_Filesystem('template');
$template = new Twig_Environment($loader, array(
    'cache' => 'cache',
));

$app = new lib\Application();

$app->init($template);
$app->orderBy = isset($_GET['order']) ? $_GET['order'] : 'id';

$app->addGet('/', function () use ($app) {

	$task = new TaskModel();	
	$res = $task->getByPage(1, 'id');
	$pages = $task->getPageList();
		
	echo $app->template->render('taskList.htm', [ 'current' => '1', 'tasks' => $res, 'pages' => $pages] );
});


$app->addGet('/admin/edit/{id}', function () use ($app) {

	$task = new TaskModel();
	
	$taskItem = $task->getById( $app->getVar('id') );

	if ($taskItem == null) $taskItem['username'] = false;	
	echo $app->template->render('admin.edit.htm', $taskItem );
}, true);

$app->addPost('/admin/update', function () use ($app) {

	$task = new TaskModel();
	if ($task->checkIn($_POST)) {
		$task->update($_POST);		
		$app->redirect('/admin/list/1');
	} else {
		echo 'error';
	}	
}, true);

$app->addGet('/inserterr', function () use ($app) {
	if (isset($_GET['error'])) {
		var_dump( $_GET['error'] );
	}
	echo $app->template->render('insert.htm');
});

$app->addPost('/insert', function () use ($app) {
	$task = new TaskModel();
	$error = $task->checkIn($_POST);
	$parm = implode('.', $error);

	if ($error === false) {
		$task->insert($_POST);
		$app->redirect('/');	
	} else {
		$app->redirect('/inserterr?error='. $parm);
	}
});


$app->addGet('/admin/list/{page}', function () use ($app) {

	$task = new TaskModel();

	$res = $task->getByPage((int)$app->getVar('page'), $app->orderBy);
	$pages = $task->getPageList();

	 echo $app->template->render('taskList.link.htm', [
	 		'current' => $app->getVar('page'),
	 		'tasks' => $res, 
	 		'order' => $app->orderBy, 
	 		'pages' => $pages] );
}, true);

$app->addGet('/admin/fin/', function () use ($app) {
	unset($_SESSION['isAuth']);
	echo 'Вы вышли из системы';
});


$app->addGet('/admin', function () use ($app) {
	echo $app->template->render('login.htm' );
});


$app->addPost('/login', function () use ($app) {

	if ($_POST['user'] =='123' && $_POST['psw'] == '123') {
		echo $app->template->render('login.ok.htm' );
		$_SESSION['isAuth'] = true;

	} else {
		echo $app->template->render('login.htm', 
			['msg' =>'не правильный пароль']);
	}
});


$app->addGet('/{id}/', function () use ($app) {

	$task = new TaskModel();	

	$res = $task->getByPage((int)$app->getVar('id'), $app->orderBy);
	$pages = $task->getPageList();


	 echo $app->template->render('taskList.htm', [
	 		'current' => $app->getVar('id'),
	 		'tasks' => $res, 
	 		'order' => $app->orderBy, 
	 		'pages' => $pages] );
});



$app->Run();

