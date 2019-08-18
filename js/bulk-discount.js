(function($) {
  "use strict";

  $.fn.bulk_discount = function() {
    var $this = $(this);
    var $table = $this.find("table.bulk-discount");
    var initData = JSON.parse($table.attr("data-init"));

    //Setup functions

    function inputChange() {
      var data = [];
      $table.find("tbody tr").each(function() {
        data.push({
          qty: $(this)
            .find("input.qty")
            .val(),
          ppu: $(this)
            .find("input.ppu")
            .val()
        });
      });
      $this.find("input[type='hidden']").val(JSON.stringify(data));
      $table
        .closest(".woocommerce_variable_attributes")
        .find(".wc_input_price")
        .change();
    }

    function rebind() {
      $this
        .find("input")
        .off("change", inputChange)
        .on("change", inputChange);

      $this
        .find("button.remove")
        .off("click")
        .on("click", function() {
          $(this)
            .closest("tr")
            .remove();
          inputChange();
        });

      $this
        .find("button.add")
        .off("click")
        .on("click", function() {
          addRow(0, 0);
          rebind();
        });
    }

    function addRow(qty, ppu) {
      $table
        .find("tbody")
        .append(
          "<tr><td><div style='width:25px;height:25px;cursor:all-scroll;'></div></td><td><input class='qty' type='number' value='" +
            qty +
            "'></td><td><input type='number' class='ppu' value='" +
            ppu +
            "'></td><td><button type='button' class='button remove' style='width: 100%;'>-</button></td></tr>"
        );
    }

    //Init

    for (var i = 0; i < initData.length; i++) {
      addRow(initData[i].qty, initData[i].ppu);
    }

    //Draggable rows using jquery ui
    $table
      .find("tbody")
      .sortable({
        beforeStop: function() {
          inputChange();
        }
      })
      .disableSelection();

    //Handlers
    rebind();
    inputChange();

    return $this;
  };
})(jQuery);
