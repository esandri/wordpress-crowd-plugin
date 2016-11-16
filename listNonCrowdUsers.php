<?php

require_once 'config.inc.php';
include('httpful.phar');


// List all users from gitlab
$i = 0;
for ($page = 1; $page <= 3; $page++ ) {
  $response = \Httpful\Request::get($gitlab_url . 'users?page='.$page.'&per_page=100&state=active')
      ->addHeader('PRIVATE-TOKEN', $gitlab_token)
      ->send();

  foreach ($response->body as $user) {
    if ($user->state == "active") {
      if (!checkCrowdUser($user->email)) {
        echo $user->username . "\n";
      }
      $i++;
    }
  }
}

echo $i . " Users checked";

// Check if user is active in crowd

function checkCrowdUser($email) {
  global $crowd_adress;
  global $crowd_app;
  global $crowd_app_password;

  $uri = $crowd_adress . 'search?entity-type=user&restriction=email='.$email;

  $response = \Httpful\Request::get($uri)
      ->authenticateWith($crowd_app, $crowd_app_password)
      ->send();

  return $response->body->user;
}

?>
