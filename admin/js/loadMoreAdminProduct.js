var start = 0;
var limit = 40;
var url_admin = $("#grid-products").attr("data-url");
var idioma = $("#grid-products").attr("data-idioma");
const url = $("#grid-products").attr("data-url");
var nameColumn = localStorage.getItem("key") ? localStorage.getItem("key") : "";
var shcolumn;
var position = 0;
var ls = localStorage;
var page = ls.getItem("page") ? ls.getItem("page") : 1;
var baseUrl = window.location.href; // You can also use document.URL
var lastVariable = baseUrl.substring(baseUrl.lastIndexOf("/") + 1);
var lastProduct =
  lastVariable && lastVariable != "productos"
    ? ls.setItem("product", lastVariable)
    : "";
var urlFilter = ls.getItem("lastFilter") ? ls.getItem("lastFilter") : "";

$(document).ready(() => {
  if (localStorage.getItem("key") == null) {
    localStorage.setItem(
      "key",
      "peso,precio_descuento,precio_mayorista,subcategoria,keywords,envio_gratis"
    );
  }
  $.fn.serializeObject = function serializeObject() {
    var o = {};
    var a = "";
    var a = this.serializeArray();
    $.each(a, function () {
      if (o[this.name] !== undefined) {
        if (!o[this.name].push) {
          o[this.name] = [o[this.name]];
        }
        o[this.name].push(this.value || "");
      } else {
        o[this.name] = this.value || "";
      }
    });
    return o;
  };

  if (baseUrl.search("modificar-masivo") > 0) {
    getDataMassive();
  } else {
    getData();
  }
});

function loadMore() {
  disableLoadMore();
  start += limit;
  getData("add");
}

function disableLoadMore() {
  $("#grid-products-btn").hide();
}

function enableLoadMore() {
  $("#grid-products-btn").show();
}

function reset() {
  $("#grid-products").html("");
}
function resetPage() {
  ls.setItem("page", 1);
  getData();
}

function toggleColumn(name = "") {
  var nameColumn = localStorage.getItem("key")
    ? localStorage.getItem("key")
    : "";
  if (name != "" || name != undefined || name != null) {
    if (nameColumn.indexOf("," + name) == -1) {
      newNameColumn = nameColumn + "," + name;
      localStorage.setItem("key", newNameColumn);
      var shcolumn = "." + name;
      $(shcolumn).toggle();
    } else {
      $("." + name).toggle();
      newNameColumn = nameColumn.replace("," + name, "");
      localStorage.setItem("key", newNameColumn);
    }
  }
}

function showColumnLoadMore() {
  $(".checkbox-menu-products").attr("checked", true);
  if (localStorage.getItem("key") != null) {
    key = localStorage.getItem("key").split(",");
    key.forEach((name) => {
      if (name) {
        $("#lb-" + name).attr("checked", false);
        $("." + name).hide();
      }
    });
  }
}

function seguroEliminar(url) {
  swal({
    title: "¿ESTÁS SEGURO DE ELIMINAR ESTE REGISTRO?",
    text: "No podrás recuperar este registro, una vez borrado.",
    icon: "warning",
    buttons: ["Cancelar", true],
    dangerMode: true,
  }).then((willDelete) => {
    if (willDelete) {
      swal("¡FUE ELIMINADO EXITOSAMENTE!", {
        icon: "success",
      });
      if (url) {
        window.location.href = url;
      } else {
        window.location.reload();
      }
    } else {
      swal("¡LA ACCIÓN FUE CANCELADA!");
    }
  });
}

function serializeFormFilterToQuery(form) {
  objectArray = $("#" + form).serializeObject();
  const str = Object.keys(objectArray)
    .map((key) => {
      keyValue = key.replace("'", "").replace("[]", "").replace("'", "");
      if (objectArray[key]) {
        return `${keyValue}=${encodeURIComponent(objectArray[key]).trim()}`;
      }
    })
    .join("&");
  if (str[0] == "&") str.replace(str[0], "");
  return str;
}

function makeUrlFinal(str) {
  newUrl = url_admin + "/index.php?op=productos&accion=ver&idioma=es";
  window.history.replaceState("", "", newUrl + "&" + str);
  ls.setItem("lastFilter", newUrl + "&" + str);
}

function getData(type, cod_ = "") {
  console.log("entro")
  const list = type != "add" ? true : false;
  disableLoadMore();
  var eliminar = $("#permisos").attr("data-eliminar") == 1 ? "true" : "false";
  var editar = $("#permisos").attr("data-editar") == 1 ? "true" : "false";
  order = $("#order").val();
  if (isNaN(parseInt(ls.getItem("page")))) $("#page").val(1);
  if (type == "add") $("#page").val(parseInt($("#page").val()) + 1);
  page = parseInt($("#page").val());
  start = type == "add" ? 40 * page - 40 : 0;
  limit = type == "add" ? 40 : page * 40;
  ls.setItem("page", page);
  const serializeForm = serializeFormFilterToQuery("filter-form");
  makeUrlFinal(serializeForm);
  filter = $("#filter-form").serialize();

  $.ajax({
    url: `${url_admin}/api/productos/list.php?start=${start}&limit=${limit}&order=${order}&idioma=${idioma}`,
    type: "POST",
    data: filter,
    success: async (data) => {
      var data = JSON.parse(data);
      if (data || !list) {
        list ? reset() : enableLoadMore();
        data.product.length ? enableLoadMore() : "";
        $("#error-msg").html("");
        data.product.forEach((elementProduct) => {
          var cod = elementProduct["data"]["cod"];
          var idioma = elementProduct["data"]["idioma"];
          var cod_product = elementProduct["data"]["cod_producto"];
          var mostrar_web =
            elementProduct["data"]["mostrar_web"] == 1 ? "checked" : "";
          var envio_gratis =
            elementProduct["data"]["envio_gratis"] == 1 ? "checked" : "";
          var destacado =
            elementProduct["data"]["destacado"] == 1 ? "checked" : "";
          var precio =
            elementProduct["data"]["precio"] == null
              ? " "
              : elementProduct["data"]["precio"];
          var precio_descuento =
            elementProduct["data"]["precio_descuento"] == null
              ? " "
              : elementProduct["data"]["precio_descuento"];
          var precio_mayorista =
            elementProduct["data"]["precio_mayorista"] == null
              ? " "
              : elementProduct["data"]["precio_mayorista"];
          var keywords =
            elementProduct["data"]["keywords"] == null
              ? " "
              : elementProduct["data"]["keywords"];
          var stock =
            elementProduct["data"]["stock"] == null
              ? " "
              : elementProduct["data"]["stock"];
          var peso =
            elementProduct["data"]["peso"] == null
              ? " "
              : elementProduct["data"]["peso"];
          if (data.category != "") {
            var catData = listOptionCat(
              data.category,
              elementProduct["data"]["categoria"]
            );
            var subcatData = listOptionSubcat(
              data.category,
              elementProduct["data"]["subcategoria"],
              elementProduct["data"]["categoria"]
            );
          }
          var btnEliminar = "";
          var btnEditar = "";
          if (eliminar == "true") {
            btnEliminar = `<a data-toggle="tooltip" data-placement="left" title="Eliminar" class="btn btn-danger " onclick="seguroEliminar('${url_admin}/index.php?op=productos&accion=ver&borrar=${elementProduct["data"]["cod"]}&idioma=${elementProduct["data"]["idioma"]}')"  >
                            <div class="fonticon-wrap"><i class="bx bx-trash fs-20"></i></div>
                           </a>`;
          }
          if (editar == "true") {
            btnEditar = `<a data-toggle="tooltip" data-placement="left" title="Modificar" class="btn btn-default" href="${url_admin}/index.php?op=productos&accion=modificar&cod=${elementProduct["data"]["cod"]}&idioma=${elementProduct["data"]["idioma"]}">
                          <div class="fonticon-wrap"><i class="bx bx-edit fs-20"></i></div>
                        </a>`;
          }
          btnCopiar = `<button onclick="copyLink('${cod}')" class="btn btn-warning" data-toggle="tooltip" data-placement="left" title="Copiar url del producto"><i class="fa fa-link" aria-hidden="true"></i></button>`;

          var productData = `<tr id='${cod}'>
                              <td class="titulo"><input class="borderInputBottom invoice-customer" onchange='editProduct("${idioma}","titulo-${cod}","${url_admin}","${editar}")' id='titulo-${cod}' name='titulo' value='${elementProduct["data"]["titulo"]}' /></td>
                              <td class="precio" >$<input class="borderInputBottom invoice-amount" onchange='editProduct("${idioma}","precio-${cod}","${url_admin}","${editar}")' id='precio-${cod}' name='precio' value='${precio}' /></td>
                              <td class="precio_descuento" >$<input class="borderInputBottom" onchange='editProduct("${idioma}","precio_descuento-${cod}","${url_admin}","${editar}")' id='precio_descuento-${cod}' name='precio_descuento' value='${precio_descuento}' /></td>
                              <td class="precio_mayorista" >$<input class="borderInputBottom" onchange='editProduct("${idioma}","precio_mayorista-${cod}","${url_admin}","${editar}")' id='precio_mayorista-${cod}' name='precio_mayorista' value='${precio_mayorista}' /></td>
                              <td class="categoria" >
                                <select class="form-control borderInputBottom fs-12 invoice-item-select" onchange='editProduct("${idioma}","categoria-${cod}","${url_admin}","${editar}")' id='categoria-${cod}' name='categoria' value='#categoria option:selected'>
                                  <option value="">-- categorías --</option>
                                  ${catData}
                                </select>
                              </td>
                              <td class="subcategoria" >
                                <select class="form-control borderInputBottom fs-12 invoice-item-select select2" onchange='editProduct("${idioma}","subcategoria-${cod}","${url_admin}","${editar}")' id='subcategoria-${cod}' name='subcategoria' value='#subcategoria option:selected'>
                                  ${subcatData}
                                </select>
                              </td>
                              <td class="keywords"><input class=" borderInputBottom" onchange='editProduct("${idioma}","keywords-${cod}","${url_admin}","${editar}")' id='keywords-${cod}' name='keywords' value='${keywords}' /></td>
                              <td class="stock" ><input class=" borderInputBottom" onchange='editProduct("${idioma}","stock-${cod}","${url_admin}","${editar}")' id='stock-${cod}' name='stock' value='${stock}' /></td>
                              <td class="peso" ><input class="borderInputBottom" onchange='editProduct("${idioma}","peso-${cod}","${url_admin}","${editar}")' id='peso-${cod}' name='peso' value='${peso}' />g</td>
                              <td class="envio_gratis"  class="text-center"><input type="checkbox" class=" borderInputBottom" onchange='changeStatus("envio_gratis-${cod}","${url_admin}","${editar}")' id='envio_gratis-${cod}' name='envio_gratis' ${envio_gratis} /></td>
                              <td class="destacado"  class="text-center"><input type="checkbox" class=" borderInputBottom" onchange='changeStatus("destacado-${cod}","${url_admin}","${editar}")' id='destacado-${cod}' name='destacado' ${destacado} /></td>
                              <td class="mostrar_web"  class="text-center"><input type="checkbox" class=" borderInputBottom" onchange='changeStatus("mostrar_web-${cod}","${url_admin}","${editar}")' id='mostrar_web-${cod}' name='mostrar_web' ${mostrar_web} /></td>
                              <td class="text-right ">
                              <input type="text" style="position:absolute;left:-5000px;top:-5000px;" id="link-${cod}" value="${elementProduct["link"]}">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                  ${btnCopiar}
                                  ${btnEditar}
                                  ${btnEliminar}
                                </div>
                              </td>
                            </tr>`;
          $("#grid-products").append(productData);
        });
        await showColumnLoadMore();
      } else {
        $("#grid-products").html("");
        $("#error-msg").html(
          "<h4 class='mt-10'>NO SE ENCONTRARON PRODUCTOS CON LAS CARACTERISTICAS BUSCADAS</h4>"
        );
      }
    },
  });
}

function getDataMassive() {
  console.log("homa");
  filter = $("#filter-form").serialize();
  $.ajax({
    url: `${url_admin}/api/productos/list.php?start=0&limit=5000&order=${order}&idioma=${idioma}`,
    type: "POST",
    data: filter,
    success: async (data) => {
      reset();
      var data = JSON.parse(data);
      console.log(data);
      var cods = "";
      $("#cods_productos").val("");
      $("#totalProductos").html(data.product.length);
      data.product.forEach((elementProduct) => {
        cods += "'" + elementProduct["data"]["cod"] + "',";
        console.log(elementProduct["data"]["envio_gratis"]);
        var productData = `<tr>
          <td>${elementProduct["data"]["titulo"]}</td>
          <td>$${elementProduct["data"]["precio"]}</td>
          <td>$${elementProduct["data"]["precio_descuento"]}</td>
          <td>${elementProduct["data"]["stock"]}</td>
          <td>${elementProduct["data"]["peso"]} kg</td>
          <td><i class="fa fa-${(elementProduct["data"]["envio_gratis"] != "0") ? 'check' : 'window-close'}"></i></td>
          <td><i class="fa fa-${(elementProduct["data"]["destacado"] != "0") ? 'check' : 'window-close'}"></i></td>
          <td><i class="fa fa-${(elementProduct["data"]["mostrar_web"] != "0") ? 'check' : 'window-close'}"></i></td>
          </tr>`;
        $("#grid-products").append(productData);
      });
      $("#cods_productos").val(cods.substring(0, cods.length - 1));
    },
  });
}

function listOptionCat(category, productCod) {
  var catData = "";
  category.forEach((elementCategory) => {
    catData +=
      productCod == elementCategory["data"]["cod"]
        ? `<option value='${
            elementCategory["data"]["cod"]
          }' selected >${elementCategory["data"][
            "titulo"
          ].toUpperCase()}</option>`
        : `<option value='${elementCategory["data"]["cod"]}'>${elementCategory[
            "data"
          ]["titulo"].toUpperCase()}</option>`;
  });
  return catData;
}

function listOptionSubcat(category, productCodSubCat, codCat) {
  var subcatData = "";
  category.forEach((elementCategory) => {
    if (elementCategory["subcategories"] != "") {
      elementCategory["subcategories"].forEach((elementSubcategory) => {
        if (elementSubcategory["data"]["categoria"] == codCat) {
          subcatData +=
            productCodSubCat == elementSubcategory["data"]["cod"]
              ? `<option value='${
                  elementSubcategory["data"]["cod"]
                }' selected >${elementSubcategory["data"][
                  "titulo"
                ].toUpperCase()}</option>`
              : `<option value='${
                  elementSubcategory["data"]["cod"]
                }'>${elementSubcategory["data"][
                  "titulo"
                ].toUpperCase()}</option>`;
        }
      });
    }
  });
  return subcatData;
}

function changeSelect(cat, sub = "", tercat = "") {
  $("#page").val(1);
  if (cat && !sub) {
    let subcat_list = $("#" + cat + "SubCat");
    $("#cat-" + cat).removeClass("check"); //remover clase de .check de la categoria clickeada
    $(".check").prop("checked", false);
    $("#cat-" + cat).addClass("check");
    $(".ulProductsDropdown").hide(); //hide oculta todas las clases ulProductsDropdown
    $("#cat-" + cat).prop("checked") ? subcat_list.show() : subcat_list.hide();
  }
  if (cat && sub) {
    let subcat_list = $("#" + sub + "TerCat");
    $("#sub-" + cat + "-" + sub).removeClass("check"); //remover clase de .check de la categoria clickeada
    $("#" + sub + "SubCat .ulProductsDropdown").hide(); //hide oculta todas las clases ulProductsDropdown
    $(".tercercategorias .check").prop("checked", false);
    $("#sub-" + cat + "-" + sub).prop("checked")
      ? subcat_list.show()
      : subcat_list.hide();
    $("#sub-" + cat + "-" + sub).addClass("check");
  }
}

function copyLink(id) {
  var copyText = document.getElementById("link-" + id);
  copyText.select();
  document.execCommand("copy");
  successMessage("Link copiado: " + copyText.value);
}
