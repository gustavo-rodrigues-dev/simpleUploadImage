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
    <link rel="stylesheet" href="./public/font-awesome/css/font-awesome.min.css">
  </head>

  <body>

    <div id="wrapper">

      <!-- Sidebar -->
      <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <a class="navbar-brand" href="index.html">Crop Tool</a>
        </div>
      </nav>

      <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12" style="text-align:center">
            <form name="cropUpload" method="POST" action="<?= $uploadAction ?>" enctype="multipart/form-data" class="form-horizontal well well-large">
              <div class="page-header">
                <h3>Selecione uma imagem</h3>
              </div>
                <div class="form-group">
                    <input type="file" class="filestyle" name="file" data-buttonText="Buscar" />
                </div>
                <button type="submit" class="btn btn-large btn-primary"><i class="fa fa-picture-o"></i> Abrir imagem </button>

            </form>
          </div>
        </div><!-- /.row -->

      </div><!-- /#page-wrapper -->

    </div><!-- /#wrapper -->

    <!-- JavaScript -->
    <script src="./public/js/vendor/jquery-1.10.2.js"></script>
    <script src="./public/js/vendor/bootstrap.js"></script>
    <script src="./public/js/vendor/bootstrap-filestyle.min.js"></script>

  </body>
</html>