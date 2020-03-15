/*=========================================================================================
    File Name: ag-grid.js
    Description: Aggrid Table
    --------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(document).ready(function() {
  /*** COLUMN DEFINE ***/
  var columnDefs = [
    {
      headerName: "First Name",
      field: "firstname",
      editable: true,
      sortable: true,
      filter: true,
      width: 175,
      filter: true,
      checkboxSelection: true,
      headerCheckboxSelectionFilteredOnly: true,
      headerCheckboxSelection: true
    },
    {
      headerName: "Last Name",
      field: "lastname",
      editable: true,
      sortable: true,
      filter: true,
      width: 175
    },
    {
      headerName: "Company",
      field: "company",
      editable: true,
      sortable: true,
      filter: true,
      width: 250
    },
    {
      headerName: "City",
      field: "city",
      editable: true,
      sortable: true,
      filter: true,
      width: 125
    },
    {
      headerName: "Country",
      field: "country",
      editable: true,
      sortable: true,
      filter: true,
      width: 150
    },
    {
      headerName: "State",
      field: "state",
      editable: true,
      sortable: true,
      filter: true,
      width: 125
    },
    {
      headerName: "Zip",
      field: "zip",
      editable: true,
      sortable: true,
      filter: true,
      width: 125
    },
    {
      headerName: "Email",
      field: "email",
      editable: true,
      sortable: true,
      filter: true,
      width: 260,
      pinned: "left"
    },
    {
      headerName: "Followers",
      field: "followers",
      editable: true,
      sortable: true,
      filter: true,
      width: 150
    }
  ];

  /*** GRID OPTIONS ***/
  var gridOptions = {
    columnDefs: columnDefs,
    rowSelection: "multiple",
    floatingFilter: true,
    filter: true,
    pagination: true,
    paginationPageSize: 20,
    pivotPanelShow: "always",
    colResizeDefault: "shift",
    animateRows: true,
    resizable: true
  };

  /*** DEFINED TABLE VARIABLE ***/
  var gridTable = document.getElementById("myGrid");

  /*** GET TABLE DATA FROM URL ***/

  agGrid
    .simpleHttpRequest({ url: "data/ag-grid-data.json" })
    .then(function(data) {
      gridOptions.api.setRowData(data);
    });

  /*** FILTER TABLE ***/
  function updateSearchQuery(val) {
    gridOptions.api.setQuickFilter(val);
  }

  $(".ag-grid-filter").on("keyup", function() {
    updateSearchQuery($(this).val());
  });

  /*** CHANGE DATA PER PAGE ***/
  function changePageSize(value) {
    gridOptions.api.paginationSetPageSize(Number(value));
  }

  $(".sort-dropdown .dropdown-item").on("click", function() {
    var $this = $(this);
    changePageSize($this.text());
    $(".filter-btn").text("1 - " + $this.text() + " of 500");
  });

  /*** EXPORT AS CSV BTN ***/
  $(".ag-grid-export-btn").on("click", function(params) {
    gridOptions.api.exportDataAsCsv();
  });

  /*** INIT TABLE ***/
  new agGrid.Grid(gridTable, gridOptions);

  /*** SET OR REMOVE EMAIL AS PINNED DEPENDING ON DEVICE SIZE ***/

  if ($(window).width() < 768) {
    gridOptions.columnApi.setColumnPinned("email", null);
  } else {
    gridOptions.columnApi.setColumnPinned("email", "left");
  }
  $(window).on("resize", function() {
    if ($(window).width() < 768) {
      gridOptions.columnApi.setColumnPinned("email", null);
    } else {
      gridOptions.columnApi.setColumnPinned("email", "left");
    }
  });
});
