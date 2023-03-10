<?php
require_once dirname(__DIR__, 2) . "/Config/Autoload.php";
Config\Autoload::run();
$template = new Clases\TemplateSite();
$f = new Clases\PublicFunction();
$pedidos = new Clases\Pedidos();
if (isset($_GET['cod'])) {
    $cod = $f->antihack_mysqli($_GET['cod']);
    $pedidos->set("cod", $cod);
    $pedido = $pedidos->view();
    (!isset($pedido['detail'])) ? $f->headerMove(URL) : '';
    foreach ($pedido['detail'] as $detail) {
        if ($detail['tipo'] == "PR") {
            $array[] = ['product' => $detail['cod_producto'], 'amount' => $detail['cantidad']];
        }
    }
    $arrayJson = json_encode($array);
} else {
    $f->headerMove(URL);
}


?>
<style>
    .skip-loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100vh;
        background-color: white;
        z-index: 9999;
        font-family: 'Montserrat', sans-serif;
    }
</style>

<div class="skip-loader">
    <div style="display: flex;align-items: center;justify-content: center;top:0;left:0;width:100%;height:100vh;background:rgba(255,255,255,.5)">
        <div style="text-align: center;">
            <img style="margin-bottom: 20px;" src=" <?= LOGO ?>" width="300px" alt="">
            <div style="margin-bottom: 10px;"><?= $_SESSION["lang-txt"]["checkout"]["generando_pedido"] ?></div>
            <img style="filter: invert(48%) sepia(64%) saturate(5959%) hue-rotate(11deg) brightness(108%) contrast(100%)" src="<?= URL ?>/assets/images/loader-skip-checkout.svg" width="50px" alt="">
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.2.min.js" integrity="sha256-2krYZKh//PcchRtd+H+VyyQoZ/e3EcrkxhM8ycwASPA=" crossorigin="anonymous"></script>
<script>
    addCartByLink(<?= $arrayJson ?>, '<?= URL ?>');

    function addCartByLink(cart, url) {
        cart.forEach(async element => {
            var data = {
                product: element.product,
                amount: element.amount
            };
            addCart(data, url);
        });
        setTimeout(function() {
            window.location = url + '/carrito';
        }, 5000);
    }

    function addCart(data, url) {
        $.ajax({
            url: url + "/api/cart/add.php",
            type: "POST",
            data: data,
            success: function(data) {
            }
        })
    };
</script>