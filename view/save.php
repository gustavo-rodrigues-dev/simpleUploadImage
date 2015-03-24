<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Crop Tool</title>

    <!-- Bootstrap core CSS -->
    <link href="./public/css/bootstrap.css" rel="stylesheet">

    <!-- Add custom CSS here -->
    <link href="./public/css/sb-admin.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/font-awesome/css/font-awesome.min.css">
</head>

<body>

<div id="wrapper">

    <!-- Sidebar -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="#">Crop Tool</a>
        </div>
    </nav>

    <div id="page-wrapper">

        <div class="page-header">
            <h3>Salvo com Sucesso</h3>
        </div>

    </div><!-- /#page-wrapper -->

</div><!-- /#wrapper -->

<!-- JavaScript -->
<script src="./public/js/vendor/jquery-1.10.2.js"></script>
<script src="./public/js/vendor/bootstrap.js"></script>
<script>
    window.opener.Crop.setCallbackPopup("<?= $file?>")
    open(location, '_self').close();
</script>

</body>
</html>