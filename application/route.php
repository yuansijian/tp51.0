<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//return [
//    '__pattern__' => [
//        'name' => '\w+',
//    ],
//    '[hello]'     => [
//        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
//];

//引入路由
use think\Route;

Route::rule('board/:id', 'index/index/board');
Route::rule('newTopic/:name', 'index/index/newTopic');
Route::rule('/', 'index/index/index');
Route::rule('insertDatabase/:boardName', 'index/index/insertDatabase', 'post');
Route::rule('content/:name/:topic', 'index/index/content');
Route::rule('reply/:name/:topic', 'index/index/reply');
Route::rule('checkUser', 'index/login/checkUser', 'post');
Route::rule('signUp/', 'index/login/signUp');
Route::rule('insertDatabase/', 'index/login/insertDatabase');
Route::rule('login/', 'index/login/index');
Route::rule('myAccount', 'index/user/index');
Route::rule('userUpdate', 'index/user/update');
Route::rule('userChangePassword', 'index/user/changePassword');
Route::rule('updatePassword', 'index/user/updatePassword');
Route::rule('forgetPassword', 'index/login/forgetPassword');
Route::rule('replyInsert', 'index/index/replyInsert');
Route::rule('replyRedict/:name/:topic/:ran', 'index/index/replyRedict');
Route::rule('replyUpdate', 'index/index/replyUpdate');
Route::rule('index', 'admin/index/index');
Route::rule('management', 'admin/index/management');
Route::rule('boards', 'admin/index/boards');
Route::rule('managerUpdatePassword', 'index/user/managerUpdatePassword', 'post');
Route::rule('deleteUser/:id', 'admin/index/deleteUser');
Route::rule('add', 'admin/index/add');
Route::rule('addManager', 'index/user/addManager');
Route::rule('searchUser', 'admin/index/searchUser');
Route::rule('addBoard', 'admin/index/addBoard');
Route::rule('insertBoard', 'admin/index/insertBoard');
Route::rule('searchBoard', 'admin/index/searchBoard');
Route::rule('deleteBoard/:id', 'admin/index/deleteBoard');
Route::rule('manageBoard/:id', 'admin/index/manageBoard');
Route::rule('updateBoard/:id', 'admin/index/updateBoard');