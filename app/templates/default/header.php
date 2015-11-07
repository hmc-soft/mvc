<?php

use HMC\Config;
use HMC\Hooks;

?><!DOCTYPE html>
<html>
  <head>
    <title><?php echo $data['title']; ?> | <?php echo Config::SITE_TITLE(); ?></title>
    <?php
      Hooks::run('meta');
      Hooks::run('css');
      //use Assets::css or combine_css to inject stylesheets
    ?>
  </head>
  <body>
