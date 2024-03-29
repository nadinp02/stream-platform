<?php

namespace Clases;

class Favoritos extends Conexion
{

    //Atributos
    public $id;
    public $usuario;
    public $producto;
    public $idioma;

    public function set($atributo, $valor)
    {
        if ($valor != '') {
            $valor = "'" . $valor . "'";
        } else {
            $valor = "NULL";
        }
        $this->$atributo = $valor;
    }

    public function get($atributo)
    {
        return $this->$atributo;
    }

    public function add($user, $product, $idioma)
    {
        $sql = "INSERT INTO `favoritos`(`usuario`, `producto`,`idioma`) 
                VALUES ('$user','$product','$idioma')";
        $query = parent::sql($sql);
        return !empty($query) ? true : false;
    }

    public static function view($user, $product, $idioma)
    {
        $sql = "SELECT * FROM `favoritos` WHERE `usuario` = '$user' AND `producto` = '$product' AND `idioma` = '$idioma'";
        $favoritos = parent::sqlReturn($sql);
        if ($favoritos) {
            $row = mysqli_fetch_assoc($favoritos);
            $row_ = ["data" => $row];
        } else {
            $row_ = false;
        }
        return $row_;
    }


    public function delete($id)
    {
        $sql = "DELETE FROM `favoritos` WHERE `id` = $id";
        $query = parent::sql($sql);

        return !empty($query) ? true : false;
    }


    /**
     *
     * Traer más de un array 
     *
     * @param    array  $filter array con todos los códigos que quiero traer
     * @param    bool  $admin si se entra desde el admin
     * @param    bool  $category si desea traer esa informacion extra sobre el contenido
     * @param    bool  $subcategory si desea traer esa informacion extra sobre el contenido
     * @param    bool  $images si desea traer esa informacion extra sobre el contenido
     * @return   array retorna un array con todos los array internos de productos
     *
     */


    public static function list($filter = [], $category = false, $subcategory = false, $images = false, $order = '', $limit = '')
    {
        $array = array();
        $filterSql = (count($filter)) ? "WHERE " . implode(" AND ", $filter) : '';

        if ($category) {
            $arrayAttr[] = "`categorias`.`titulo` as 'categoria_titulo',`categorias`.`area` as 'categoria_area', `categorias`.`descripcion` as 'categoria_descripcion'";
            $arrayLeft[] = "LEFT JOIN `categorias` ON `categorias`.`cod` = `productos`.`categoria`";
        }
        if ($subcategory) {
            $arrayAttr[] = "`subcategorias`.`titulo`as 'subcategoria_titulo'";
            $arrayLeft[] = "LEFT JOIN `subcategorias` ON `subcategorias`.`cod` = `productos`.`subcategoria`";
        }
        if ($images) {
            $arrayAttr[] = "GROUP_CONCAT(`imagenes`.`id` ORDER BY `imagenes`.`orden` SEPARATOR ',') AS 'imagenes_id',
            GROUP_CONCAT(`imagenes`.`orden` ORDER BY `imagenes`.`orden` SEPARATOR ',') AS 'imagenes_orden',
            GROUP_CONCAT(`imagenes`.`ruta` ORDER BY `imagenes`.`orden` SEPARATOR ',') AS 'imagenes_rutas'";
            $arrayLeft[] = "LEFT JOIN `imagenes` ON `imagenes`.`cod` = `productos`.`cod`";
        }
        $attr = isset($arrayAttr) ? " , " . implode(" , ", $arrayAttr) . " " : '';
        $left = isset($arrayLeft) ? implode(" ", $arrayLeft) : '';


        if ($order != '') {
            $orderSql = $order;
        } else {
            $orderSql = "favoritos.id DESC";
        }

        $limitSql = ($limit != '') ? "LIMIT " . $limit : "";

        $sql = "SELECT `productos`.* $attr FROM `favoritos` LEFT JOIN `productos` ON `favoritos`.`producto` = `productos`.`cod` $left $filterSql GROUP BY `favoritos`.`id` ORDER BY $orderSql $limitSql";
        $favoritos = parent::sqlReturn($sql);
        if ($favoritos) {
            while ($row = mysqli_fetch_assoc($favoritos)) {
                if ($row['id'] != null) {
                    $productFecha = $row["fecha"];
                    $productFecha = date("Y-m-d", strtotime($productFecha . "+ 7 days"));
                    $fecha = ($productFecha >= date("Y-m-d")) ? $_SESSION['lang-txt']['productos']['nuevo'] : '';
                    $row = Productos::checkPriceByUser($row);
                    $link = URL . '/producto/' . PublicFunction::normalizar_link($row['titulo']) . '/' . $row['cod'];
                    $imagesArray = ($images) ? self::createArrayImages($row) : '';
                    if ($images) unset($row["imagenes_id"], $row["imagenes_orden"], $row["imagenes_rutas"]);
                    $array[] = ["data" => $row, "nuevo" => $fecha, "images" => $imagesArray, "link" => $link, "favorite" => ['data' => true]];
                }
            }
            return $array;
        }
    }

    public static function createArrayImages($row)
    {
        $row['imagenes_id'] = explode(",", $row['imagenes_id']);
        $row['imagenes_orden'] = explode(",", $row['imagenes_orden']);
        $row['imagenes_rutas'] = explode(",", $row['imagenes_rutas']);
        $imagesLength = count($row['imagenes_id']) - 1;
        $images = Imagenes::checkForProduct($row['cod_producto'], "_");
        for ($i = 0; $i <= $imagesLength; $i++) {
            if ($row["imagenes_id"][$i]) {
                $images[] = ["id" => $row["imagenes_id"][$i], "orden" => $row["imagenes_orden"][$i], "url" => URL . "/" . $row["imagenes_rutas"][$i]];
            }
        }
        $images = (count($images)) ? $images : [["url" => URL . "/assets/archivos/sin_imagen.jpg"]];
        return $images;
    }
}
