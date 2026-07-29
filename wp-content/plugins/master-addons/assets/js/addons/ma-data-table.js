(function(){
//#region dev/js/addons/free/ma-data-table.js
/**
* Start data table widget script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_DataTable = function($scope, $) {
		var a = $scope.find(".jltma-data-table-container"), n = a.data("source"), r = a.data("sourcecsv");
		if (1 == a.data("buttons")) var l = "Bfrtip";
		else l = "frtip";
		if ("custom" == n) {
			var i = $scope.find("table thead tr th").length;
			$scope.find("table tbody tr").each(function() {
				if ($(this).find("td").length < i) {
					var t = i - $(this).find("td").length;
					$(this).append(new Array(++t).join("<td></td>"));
				}
			}), $scope.find(".jltma-data-table").DataTable({
				dom: l,
				paging: a.data("paging"),
				pagingType: "numbers",
				pageLength: a.data("pagelength"),
				info: a.data("info"),
				scrollX: !0,
				searching: a.data("searching"),
				ordering: a.data("ordering"),
				buttons: [
					{
						extend: "csvHtml5",
						text: JLTMA_DATA_TABLE.csvHtml5
					},
					{
						extend: "excelHtml5",
						text: JLTMA_DATA_TABLE.excelHtml5
					},
					{
						extend: "pdfHtml5",
						text: JLTMA_DATA_TABLE.pdfHtml5
					},
					{
						extend: "print",
						text: JLTMA_DATA_TABLE.print
					}
				],
				language: {
					lengthMenu: JLTMA_DATA_TABLE.lengthMenu,
					zeroRecords: JLTMA_DATA_TABLE.zeroRecords,
					info: JLTMA_DATA_TABLE.info,
					infoEmpty: JLTMA_DATA_TABLE.infoEmpty,
					infoFiltered: JLTMA_DATA_TABLE.infoFiltered,
					search: "",
					searchPlaceholder: JLTMA_DATA_TABLE.searchPlaceholder,
					processing: JLTMA_DATA_TABLE.processing
				}
			});
		} else if ("csv" == n) ({ init: function(t) {
			var a = (t = t || {}).csv_path || "", n = $scope.element || $("#table-container"), r = $scope.csv_options || {}, l = $scope.datatables_options || {}, i = $scope.custom_formatting || [], s = {};
			$.each(i, function(e, t) {
				var a = t[0];
				s[a] = t[1];
			});
			var d = $("<table class=\"jltma-data-table cell-border\" style=\"width:100%;visibility:hidden;\">");
			n.empty().append(d), $.when($.get(a)).then(function(t) {
				for (var a = $.csv.toArrays(t, r), n = $("<thead></thead>"), i = a[0], o = $("<tr></tr>"), c = 0; c < i.length; c++) o.append($("<th></th>").text(i[c]));
				n.append(o), d.append(n);
				for (var m = $("<tbody></tbody>"), p = 1; p < a.length; p++) for (var _ = $("<tr></tr>"), g = 0; g < a[p].length; g++) {
					var b = $("<td></td>"), f = s[g];
					f ? b.html(f(a[p][g])) : b.text(a[p][g]), _.append(b), m.append(_);
				}
				d.append(m), d.DataTable(l);
			});
		} }).init({
			csv_path: r,
			element: a,
			datatables_options: {
				dom: l,
				paging: a.data("paging"),
				pagingType: "numbers",
				pageLength: a.data("pagelength"),
				info: a.data("info"),
				scrollX: !0,
				searching: a.data("searching"),
				ordering: a.data("ordering"),
				buttons: [
					{
						extend: "csvHtml5",
						text: JLTMA_DATA_TABLE.csvHtml5
					},
					{
						extend: "excelHtml5",
						text: JLTMA_DATA_TABLE.excelHtml5
					},
					{
						extend: "pdfHtml5",
						text: JLTMA_DATA_TABLE.pdfHtml5
					},
					{
						extend: "print",
						text: JLTMA_DATA_TABLE.print
					}
				],
				language: {
					lengthMenu: JLTMA_DATA_TABLE.lengthMenu,
					zeroRecords: JLTMA_DATA_TABLE.zeroRecords,
					info: JLTMA_DATA_TABLE.info,
					infoEmpty: JLTMA_DATA_TABLE.infoEmpty,
					infoFiltered: JLTMA_DATA_TABLE.infoFiltered,
					search: "",
					searchPlaceholder: JLTMA_DATA_TABLE.searchPlaceholder,
					processing: JLTMA_DATA_TABLE.processing
				}
			}
		});
		$scope.find(".jltma-data-table").css("visibility", "visible");
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/jltma-data-table.default", JLTMA_DataTable);
	});
})(jQuery, window.elementorFrontend);
/**
* End data table widget script
*/
//#endregion
})();
