<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo Request::getStatusCodeByNumber(401);?></title>
</head>
<body>
<h1><?php echo Request::getStatusCodeByNumber(401);?></h1>
  <?php $message = ViewBag::get("message") ? ViewBag::get("message") : get_translation("no_permissions"); ?>
  <?php echo $message;?>
  </body>
</html>