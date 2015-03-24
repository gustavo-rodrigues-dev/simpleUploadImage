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
    <link rel="stylesheet" href="../public/css/jquery.Jcrop.min.css">

    <style>
        .jcrop-holder{
            margin: 0 auto;
        }
    </style>
</head>

<body>

<div id="wrapper clearfix">

    <!-- Sidebar -->
    <nav class="navbar navbar-inverse navbar-fixed-top clearfix" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="index.html">Crop Tool</a>
        </div>
    </nav>

    <div id="page-wrapper clearfix">

        <div class="row">
            <div class="col-lg-12 clearfix" style="text-align:center">
                    <img src="<?=$destination?>" class="center-block" id="cropbox" />
                <!-- This is the form that our event handler fills -->
                <form action="<?=$uploadAction?>" method="post" onsubmit="return checkCoords();">
                    <input type="hidden" id="x" name="x" />
                    <input type="hidden" id="y" name="y" />
                    <input type="hidden" id="w" name="w" />
                    <input type="hidden" id="h" name="h" />
                    <br/>
                    <button type="submit" class="btn btn-large btn-primary"><i class="fa fa-picture-o"></i> Salvar imagem </button>
                </form>
            </div>
        </div><!-- /.row -->

    </div><!-- /#page-wrapper -->

</div><!-- /#wrapper -->

<!-- JavaScript -->
<script src="./public/js/vendor/jquery-1.10.2.js"></script>
<script src="./public/js/vendor/bootstrap.js"></script>
<script src="./public/js/vendor/bootstrap-filestyle.min.js"></script>
<script src="./public/js/vendor/jquery.Jcrop.min.js"></script>
<script type="text/javascript">

    $(function(){

        $('#cropbox').Jcrop({
            aspectRatio: 0,
            onSelect: updateCoords
        });

    });

    function updateCoords(c)
    {
        $('#x').val(c.x);
        $('#y').val(c.y);
        $('#w').val(c.w);
        $('#h').val(c.h);
    };

    function checkCoords()
    {
        if (parseInt($('#w').val())) return true;
        alert('Por favor, selecione a area para crop.');
        return false;
    };

</script>
<script>

</script>

</body>
</html>