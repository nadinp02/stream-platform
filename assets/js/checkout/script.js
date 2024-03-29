//Activar revisiones de horarios y fechas
$("#envio").change(function() {
    const $this = $(this); // Cache $(this)
    const dataVal = $this.find(':selected').data('option'); 
    hideByOption(dataVal);
});


// DATE RANGE PICKER
$(".dateSelectRangeShipping").daterangepicker({
  showDropdowns: true,
  rangeDatePicker: true,
  timePicker: true,
  timePicker24Hour: true,
  //para deshabilitar una fecha especifica
  // isInvalidDate: function(date) {
  //     return !!(['12-25'].indexOf(date.format('MM-DD')) > -1);
  // },
  ranges: {
    Hoy: [moment().startOf("day"), moment()],
    "Proximos 7 Días": [moment().add(6, "days").startOf("day"), moment()],
    "Proximos 15 Días": [moment().add(14, "days").startOf("day"), moment()],
    "Proximos 30 Días": [moment().add(29, "days").startOf("day"), moment()],
    "Este Mes": [moment().startOf("month"), moment().endOf("month")],
    "Mes que viene": [
      moment().add(1, "month").startOf("month"),
      moment().add(1, "month").endOf("month"),
    ],
  },
  locale: {
    format: "DD/MM/YYYY HH:mm:ss",
    separator: " - ",
    applyLabel: "Aplicar",
    cancelLabel: "Cancelar",
    fromLabel: "Desde",
    toLabel: "Hasta",
    customRangeLabel: "Personalizado",
    weekLabel: "W",
    daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
    monthNames: [
      "Enero",
      "Febrero",
      "Marzo",
      "Abril",
      "Mayo",
      "Junio",
      "Julio",
      "Agosto",
      "Septiembre",
      "Octubre",
      "Noviembre",
      "Diciembre",
    ],
    firstDay: 1,
  },
  startDate: moment(),
  minDate: moment(),
  alwaysShowCalendars: true,
});

// DATE RANGE PICKER
$(".dataSelectShipping").daterangepicker({
  showDropdowns: true,
  singleDatePicker: true,
  timePicker: true,
  timePicker24Hour: true,
  locale: {
    format: "DD/MM/YYYY HH:mm:ss",
    separator: " - ",
    applyLabel: "Aplicar",
    cancelLabel: "Cancelar",
    fromLabel: "Desde",
    toLabel: "Hasta",
    customRangeLabel: "Personalizado",
    weekLabel: "W",
    daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
    monthNames: [
      "Enero",
      "Febrero",
      "Marzo",
      "Abril",
      "Mayo",
      "Junio",
      "Julio",
      "Agosto",
      "Septiembre",
      "Octubre",
      "Noviembre",
      "Diciembre",
    ],
    firstDay: 1,
  },
  startDate: moment(),
  minDate: moment(),
  alwaysShowCalendars: true,
});

function checkedBox(id) {
  $(".boxChecked").removeClass("checkedBox");
  $("#" + id).addClass("checkedBox");
}

function hideByOption(option) {
  $("#dataMaster").removeClass("d-none");
  if (option == 0) {
    $("#dataMaster").removeClass("d-none");
    $("#dataMaster").addClass("d-none");
  }
  if (option == 1) {
    $("#dataMaster").removeClass("d-none");
    $(".data").addClass("d-none");
  }
  if (option == 2) {
    $("#dataMaster").removeClass("d-none");
    $(".data").removeClass("d-none");
    $("#rango-fecha").addClass("d-none");
  }
  if (option == 3) {
    $("#dataMaster").removeClass("d-none");
    $(".data").removeClass("d-none");
    $("#fecha").addClass("d-none");
  }
}

function deleteShippingOption() {
  if (screen.width < 1000) {
    $(".envioDesktop").remove();
  } else {
    $(".envioMobile").remove();
  }
}

function deletePaymentOption() {
  if (screen.width < 1000) {
    $(".pagoDesktop").remove();
  } else {
    $(".pagoMobile").remove();
  }
}

function saveToPdf(cod) {
  $.ajax({
    url: url + "/api/pedidos/saveToPdf.php",
    type: "GET",
    data: {
      cod: cod,
    },
    success: function (data) {},
  });
}

function printContent(id) {
  var restorepage = $("body").html();
  var printcontent = $("#" + id).clone();

  $("body").empty().html(printcontent);
  window.print();
  $("body").html(restorepage);
}

$(".deleteConfirm").on("click", function (e) {
  e.preventDefault();

  Swal.fire({
    title: "¿ESTÁS SEGURO DE ELIMINAR ESTE REGISTRO?",
    text: "No podrás recuperar este registro, una vez borrado.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, eliminar",
    cancelButtonText: "Cancelar",
  }).then((willDelete) => {
    if (willDelete.isConfirmed) {
      $.ajax({
        url: url + "/api/pedidos/deleteDetail.php",
        type: "POST",
        data: {
          id: $(this).attr("id"),
        },
        success: function (data) {
          data = JSON.parse(data);
          if (data["status"]) {
            $("#d-" + data["id"]).remove();
            Swal.fire({
              icon: "success",
              title: "¡FUE ELIMINADO EXITOSAMENTE!",
              showConfirmButton: false,
              timer: 1500,
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "¡OCURRIO UN ERROR!",
              showConfirmButton: false,
              timer: 1500,
            });
          }
        },
      });
    }
  });
});
