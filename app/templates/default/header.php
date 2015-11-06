<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $data['title']; ?> | <?php echo Core\Config::SITE_TITLE(); ?></title>
    <?php
      \Helpers\Hooks::run('meta');
      \Helpers\Hooks::run('css');
      //use \Helpers\Assets::css or combine_css to inject stylesheets
    ?>
  </head>
  <body>
