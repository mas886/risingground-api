<?php

/*
 * Copyright (C) 2017 mas886/redrednose/arnau and judit09/tinez09/judit
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require './vendor/autoload.php';
include_once("./class/User.php");
include_once("./class/Message.php");
include_once("./class/Character.php");

$app = new \Slim\App;

//User system functions

$app->post('/user/logout', function (Request $request, Response $response, $args = []) {
    $token = $request->getParam('token');
    $user = new User();
    //Will return 1 if successfull 0 if fail
    $response->getBody()->write(json_encode(array('Message' => $user->logout($token))));
    return $response;
});

$app->post('/user/login', function (Request $request, Response $response, $args = []) {
    $username = $request->getParam('username');
    $password = $request->getParam('password');
    $user = new User();
    //Will return 0 if failed, the token if successfull
    $response->getBody()->write(json_encode(array('Message' => $user->login($username, $password))));
    return $response;
});

$app->post('/user/signup', function (Request $request, Response $response, $args = []) {
    $username = $request->getParam('username');
    $password = $request->getParam('password');
    $email = $request->getParam('email');
    $user = new User();
    //Will return 1 when successfull
    $response->getBody()->write(json_encode(array('Message' => $user->signUp($username, $password, $email))));
    return $response;
});

//Messaging system functions

$app->post('/message/send', function (Request $request, Response $response, $args = []) {
    $token = $request->getParam('token');
    $receiver = $request->getParam('receiver');
    $text = $request->getParam('text');
    $message = new Message;
    //Will return 1 when successfull
    $response->getBody()->write(json_encode(array('Message' => $message->sendMessage($token, $receiver, $text))));
    return $response;
});

//Character system functions

$app->post('/character/addcharacter', function (Request $request, Response $response, $args = []) {
    $token = $request->getParam('token');
    $characterName = $request->getParam('characterName');
    $character = new Character;
    //Will return 1 when successfull
    $response->getBody()->write(json_encode(array('Message' => $character->addCharacter($characterName, $token))));
    return $response;
});



$app->run();
