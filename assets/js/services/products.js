var url = $("body").attr("data-url");
var ls = localStorage;
var page = ls.getItem("page") ? ls.getItem("page") : 1;
var baseUrl = window.location.href; // You can also use document.URL
var lastVariable = baseUrl.substring(baseUrl.lastIndexOf("/") + 1);
var lastProduct =
  lastVariable && lastVariable != "productos"
    ? ls.setItem("product", lastVariable)
    : "";

function loadMore() {
  getData("add");
}

function showLoadMore(type = true) {
  let display = type ? "block" : "none";
  document.getElementById("grid-products-btn").style.display = display;
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

function resetPage() {
  ls.setItem("page", 1);
  getData();
}

function initPage() {
  if (window.location.href.search("/productos/promocion") != -1) {
    $("#en_promocion").prop("checked", true);
  }

  $("input:checkbox:checked").each(function () {
    $(`#${$(this).val()}SubCat`).show();
    $(`#${$(this).val()}TerCat`).show();
  });

  var a = new StickySidebar("#sideCart", {
    topSpacing: 200,
  });

  getData();
}

function changeSelect(cat, sub = "", tercat = "") {
  checkCat = $("#cat-" + cat).prop("checked");
  $("#page").val(1);
  if (cat && !sub) {
    let subcat_list = $("#" + cat + "SubCat");
    $("#cat-" + cat).removeClass("check"); //remover clase de .check de la categoria clickeada
    $('[id^="cat-"]').prop("checked", false);
    $('[id^="sub-"]').prop("checked", false);
    $('[id^="ter-"]').prop("checked", false);
    if (checkCat) $("#cat-" + cat).addClass("check");
    if (checkCat) $("#cat-" + cat).prop("checked", true);
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
  newUrl = $("body").attr("data-url") + "/productos";
  window.history.replaceState("", "", newUrl + "?" + str);
}

function getData(
  type = "",
  div = "grid-products",
  products_page = true,
  filter = {},
  start = 0,
  limit = 3,
  order = "id ASC"
) {
  const list = type != "add" ? true : false;

  if (products_page) {
    order = $("#order").val() ?? order;
    if (isNaN(parseInt(ls.getItem("page")))) $("#page").val(1);
    if (type == "add") $("#page").val(parseInt($("#page").val()) + 1);
    page = $("#page").val();
    start = type == "add" ? limit * page - limit : 0;
    limit = type == "add" ? limit : page * limit;
    ls.setItem("page", page);
    const serializeForm = serializeFormFilterToQuery("filter-form");
    makeUrlFinal(serializeForm);
    filter = $("#filter-form").serialize();
  }

  if (url) {
    $.ajax({
      url: `${url}/api/products/get_products.php?start=${start}&limit=${limit}&order=${order}`,
      type: "POST",
      data: filter,
      success: (data) => {
        if (data) {
          const products = JSON.parse(data);
          if (products_page && list) reset(div);
          if (products_page) {
            showLoadMore(parseInt(products.products.length) >= limit);
          }
          products.products.forEach((element) => {
            product = createElement(element, products.user);
            $(`.${div}`).append(product);
          });
        } else {
          if (products_page) {
            showLoadMore(false);
          }
        }
      },
    });
  }
}

function createElement(element, user, col = 4) {
  let product = element["data"];
  let product_item = "";
  let price_old = "";
  let promo = "";
  let button_cart = "";
  let text_percentage = "";
  let links_categories = "";

  if (user.minorista != 0) {
    if (![null, 0].includes(product["precio_descuento"])) {
      price_old = `$${product["precio"]}`;
    }
  }
  let price = product["precio_final"] ? "$" + product["precio_final"] : "";
  let img = element["images"][0] != null ? element["images"][0]["thumb"] : "";
  let link = element["link"];

  let title = product["titulo"] != null ? product["titulo"].toUpperCase() : "";
  let user_login = user == "" ? "d-none" : "";
  let fecha = element["nuevo"];

  let hidden_add_favorite = element["favorite"]["data"] != null ? "d-none" : "";
  let hidden_delete_favorite =
    element["favorite"]["data"] != null ? "" : "d-none";

  if (product["promoLleva"] != null && product["promoPaga"] != null) {
    let promo_calulate = (
      (product["precio_final"] * product["promoPaga"]) /
      product["promoLleva"]
    ).toFixed(2);
    promo = `<span class='badge rounded-pill bg-success top-left fs-12'>${product["promoLleva"]}x${product["promoPaga"]} | $${promo_calulate} x UN.</span>`;
  }

  if (product["precio_descuento"]) {
    total = product["precio"];
    percentage = (product["precio_final"] / total) * 100 - 100;
    percentage = Math.floor(percentage);
    if (percentage < -4) {
      text_percentage = percentage + "%";
    }
  }
  if (product["habilitado"] == 1) {
    if (product["stock"] > 0) {
      button_cart = `<div class="d-flex align-items-center justify-content-between">
            <input type="number" step="1" class="form-control" name="stock" id="product-stock-${product["cod"]}" min="1" max="${product["stock"]} "value="1" />
            <button   class="btn btn-sm btn-block btn-product-add btn-hover-primary" onclick="addToCart('','${product["cod"]}','${url}',false)" title="${lang["productos"]["agregar_carrito"]}">
                <i class="fas fa-shopping-cart"></i>
            </button>
        </div>`;
    } else {
      button_cart = `<div class="d-flex align-items-center justify-content-between">${lang["productos"]["sin_stock"]}</div>`;
    }
  }
  if (
    element["data"]["categoria_free_shipping"] == 1 ||
    element["data"]["subcategoria_free_shipping"] == 1 ||
    element["data"]["tercercategoria_free_shipping"] == 1
  ) {
    var envioGratis =
      '<span class="badge badge-success top-right">Envio Gratis</span>';
  } else {
    var envioGratis = "";
  }

  if (product["categoria"] != null) {
    linkCategoriaAnchor = `${url}/productos/b/categoria/${product["categoria"]}`;
    links_categories += `<a class="blog-link theme-color text-uppercase fs-10" href="${linkCategoriaAnchor}" tabindex="0">${product["categoria_titulo"]}</a>`;
    if (product["subcategoria"] != null) {
      linkSubcategoriaAnchor = `${url}/productos/b/categoria/${product["categoria"]}/subcategoria/${product["subcategoria"]}`;
      links_categories += `<span class="blog-link theme-color text-uppercase"> | </span>
        <a class="blog-link theme-color text-uppercase fs-10" href="${linkSubcategoriaAnchor}" tabindex="0">
            ${product["subcategoria_titulo"]}
        </a>`;
      if (product["tercercategoria"] != null) {
        linkTercercategoriaAnchor = `${url}/productos/b/categoria/${product["categoria"]}/subcategoria/${product["subcategoria"]}/tercercategoria/${product["tercercategoria"]}`;
        links_categories += `<span class="blog-link theme-color text-uppercase"> | </span>
        <a class="blog-link theme-color text-uppercase fs-10" href="${linkTercercategoriaAnchor}" tabindex="0">
            ${product["tercercategoria_titulo"]}
        </a>`;
      }
    }
  }

  product_item = `<div class="col-sm-6 col-md-4 col-lg-${col} mb-30">
        <div class="card product-card height-530" >
            <div class="card-body">
                <div class="product-thumbnail position-relative height-300">
                    <span class="badge badge-success top-left">${text_percentage}</span>
                    <span class="badge badge-danger top-right">${fecha}</span>
                    <span class="badge badge-primary bottom-left">${promo}</span>
                    ${envioGratis}
                    <div class="arrival-img text-center">
                        <div class="${user_login} fav-product">
                            <a title="${lang["productos"]["eliminar_fav"]}" class="action wishlist ${hidden_delete_favorite} btn-deleteFavorite-${product["cod"]}"  onclick="deleteFavorite('${product["cod"]}','${product["idioma"]}');">
                              <i class="fa fa-heart text-danger" aria-hidden="true"></i>
                            </a>
                            <a title="${lang["productos"]["agregar_fav"]}" class="action wishlist ${hidden_add_favorite} btn-addFavorite-${product["cod"]}" onclick="addFavorite('${product["cod"]}','${product["idioma"]}');">
                              <i class="fa fa-heart text-white" aria-hidden="true"></i>
                            </a>
                        </div>
                        <a href="${link}">
                            <img class="align-items-center first-img" src="${img}" alt="${title}" />
                        </a>
                    </div> 
                </div>
                <div class="product-desc py-0 px-0 height-130 mt-4"  >
                    ${links_categories}
                    <h3 class="title fs-13">
                        <a href="${link}">${title}</a>
                    </h3>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="product-price">
                            <del class="del fs-18">${price_old}</del>
                            <span class="onsale fs-18">${price}</span>
                        </span>
                    </div>
                </div>
                ${button_cart}
            </div>
        </div>
    </div>`;
  return product_item;
}

function appendProducts(data) {
  if (Array.isArray(data)) {
    if (data.length < limit) {
      showLoadMore(false);
    }
  }
}

function loader(div) {
  $(`.${div}`).append(`
      <div class='col-xl-12 col-lg-12 col-md-12 col-sm-12' id='loader'> 
          <div class='product-wrap mb-10 mt-100 mb-400'> 
              <div class='product-content text-center'> 
                  <i class='fa fa-circle-o-notch fa-spin fa-3x fs-70'></i> 
              </div> 
          </div> 
      </div>`);
}

function reset(div) {
  $(`.${div}`).html("");
}
