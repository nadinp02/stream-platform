<?php


namespace Clases;


class Checkout
{
    private $con;
    private $config;
    private $f;

    public function __construct()
    {
        $this->con = new Conexion();
        $this->config = new Config();
        $this->f = new PublicFunction();
    }

    public function initial(string $type, string $user)
    {
        //status EMPTY.OPEN,CLOSED
        //type USER,GUEST,NEWER
        //stage-X -> status OPEN,CLOSED
        //stage-1 -> subtype HOME,SPECIAL,API
        $cod = $_SESSION['cod_pedido'];
        $_SESSION['stages'] = array(
            "status" => 'OPEN',
            "type" => $type,
            "user_cod" => $user,
            "cod" => $cod,
            "stage-1" => '',
            "stage-2" => '',
            "stage-3" => ''
        );
    }

    public function user(string $user, string $type)
    {
        if (!empty($_SESSION['stages'])) {
            $_SESSION['stages']['user_cod'] = $user;
            $_SESSION['stages']['type'] = $type;
            return true;
        } else {
            return false;
        }
    }



    public function progress()
    {
        $stages = $_SESSION['stages'];
        $response = array(
            "stage-1" => empty($stages['stage-1']) ? false : true,
            "stage-2" => empty($stages['stage-2']) ? false : true,
            "stage-3" => empty($stages['stage-3']) ? false : true,
            "finished" => $stages['status'] == 'OPEN' ? false : true
        );
        return $response;
    }

    public function destroy()
    {
        if (isset($_SESSION['stages'])) unset($_SESSION['stages']);
    }

    public function close()
    {
        if (isset($_SESSION['stages'])) $_SESSION['stages']['status'] = 'CLOSED';
    }


    public function checkSkip()
    {
        $minorista = (isset($_SESSION["usuarios"]["minorista"]) && $_SESSION["usuarios"]["invitado"] == '0') ? $_SESSION["usuarios"]["minorista"] : 2;
        $checkout_activo = PerfilesEcommerce::list(['activo' => 1, 'minorista' => $minorista], '', '', true);
        $link = ($checkout_activo['data']['saltar_checkout'] == 'skip' && CANONICAL != URL . "/checkout/skip-checkout" && isset($_SESSION["usuarios"]["minorista"])) ? "checkout/skip-checkout" : "checkout/shipping";
        return $link;
    }

    public function hidePrices()
    {
        $type = "mayorista";
        $ocultar = "";
        if (isset($_SESSION['usuarios']['minorista']) && $_SESSION['usuarios']['minorista'] == 1) {
            $type = "minorista";
        }
        $checkPrecio = $this->config->viewCheckout($type, $_SESSION['lang']);
        if ($checkPrecio['data']['mostrar_precio'] == 1) {
            $ocultar = "hidden";
        }
        return $ocultar;
    }

    public function  checkTypeCheckout($op)
    {
        $close = false;
        $minorista = (isset($_SESSION["usuarios"]["minorista"]) && $_SESSION["usuarios"]["invitado"] == '0') ? $_SESSION["usuarios"]["minorista"] : 2;
        $perfiles_ecommerce = new PerfilesEcommerce();
        $checkout_activo = $perfiles_ecommerce->list(['activo' => 1, 'minorista' => $minorista], '', '', true);
        $_SESSION["perfil-ecommerce"] = $checkout_activo;
        if ($checkout_activo['data']['saltar_checkout'] == 'skip' && CANONICAL != URL . "/checkout/skip-checkout" && isset($_SESSION["usuarios"]["minorista"])) $this->f->headerMove(URL . "/checkout/skip-checkout");
        if ($checkout_activo['data']['saltar_checkout'] != 'nothing') {
            if ($checkout_activo['data']['saltar_checkout'] == $op) {
                echo "la opcion concuerda, finalizar pedido y ir a detail";
                $close = true;
                $this->close();
            } elseif ($checkout_activo['data']['saltar_checkout'] == 'all') {
                echo "esta bloqueado el metodo de compra";
                $close = true;
                $this->close();
            }
        }
        return $close;
    }
}
