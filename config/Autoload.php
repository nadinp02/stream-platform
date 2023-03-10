<?php

namespace Config;

use Clases\Idiomas;
use Clases\PerfilesEcommerce;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';
Dotenv::createImmutable(dirname(__DIR__, "1"))->load();

class Autoload
{
    public static function  run()
    {
        #Variables Globales
        $arrContextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
        $page_customization = json_decode(file_get_contents(dirname(__DIR__, 1)  . '/files/customization/page.json', false, stream_context_create($arrContextOptions)), true);
        define('SALT', hash("sha256", $_ENV["SALT"]));
        define('URL', $_ENV["PROTOCOL"] . "://" . $_SERVER['HTTP_HOST'] . $_ENV["PROJECT"]);
        define('URL_ADMIN', $_ENV["PROTOCOL"] . "://" . $_SERVER['HTTP_HOST'] . $_ENV["PROJECT"] . "/admin");
        define('TITULO_ADMIN', $_ENV["TITLE_ADMIN"]);
        define('CANONICAL', $_ENV["PROTOCOL"] . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        define('LOGO', URL . $page_customization["logo"]);
        define('FAVICON', URL . $page_customization["favicon"]);
        define('TITULO', $_ENV["TITLE"]);

        #Autoload
        spl_autoload_register(
            function ($clase) {
                $ruta = str_replace("\\", "/", $clase) . ".php";
                $ruta = str_replace("Clases", "clases", $ruta);
                $pos = strpos($ruta, "clases");
                if ($pos !== false) {
                    include_once dirname(__DIR__) . "/" . $ruta;
                }
            }
        );
        self::settings();
    }

    public static function settings()
    {
        #Se configura la zona horaria en Argentina
        setlocale(LC_ALL, $_ENV["LOCALE"]);
        date_default_timezone_set($_ENV["TIMEZONE"]);
        session_start();

        #Se mantiene siempre la sesión iniciada
        if ($_ENV["DEBUG"] == "1") {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }

        #Se define el perfil Ecommerce de la pagina
        if (!isset($_SESSION["admin"])) {
            $minorista = (isset($_SESSION["usuarios"]["minorista"]) && $_SESSION["usuarios"]["invitado"] == '0') ? $_SESSION["usuarios"]["minorista"] : 2;
            $_SESSION["perfil-ecommerce"] = PerfilesEcommerce::list(['activo' => 1, 'minorista' => $minorista], '', '', true);
        }


        #Se define el idioma de la pagina 
        if (isset($_SESSION["usuarios"]["idioma"])) {
            $_SESSION["lang"] = $_SESSION["usuarios"]["idioma"];
        } else {
            $_SESSION["lang"] = isset($_SESSION["lang"]) ? $_SESSION["lang"]  : Idiomas::viewDefault()['data']['cod'];
        }
        $_SESSION["defaultLang"] = isset($_SESSION["defaultLang"]) ? $_SESSION["defaultLang"]  : Idiomas::viewDefault()['data']['cod'];
        $_SESSION["lang-txt"] = json_decode(file_get_contents(dirname(__DIR__) . '/lang/' . $_SESSION["lang"] . '.json'), true);
        $_SESSION["cod_pedido"] = isset($_SESSION["cod_pedido"]) ? $_SESSION["cod_pedido"] : '';
        !isset($_SESSION['token']) ? $_SESSION['token'] = md5(uniqid(rand(), TRUE)) : null;


        #Activar si quiero restructurar los links de la base de datos
        // PublicFunction::changeRootContents("https://26.177.116.7/", "https://server.com/");

        \Clases\Backup::cleanFolderBackup(dirname(__DIR__, "1"));
    }
}
