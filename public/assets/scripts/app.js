var searchVals;

$(document).ready(function() {
    // variable to hold request
    var request;

    // bind to the submit event of our form
    $("#search-form").submit(function(event) {

        // abort any pending request
        if (request) {
            request.abort();
        }

        var reportType = $("#report-name-hidden").val();
        var requestType = $("#request-type").val();

        var $form = $(this);
        var $inputs = $form.find("input, select, button, textarea");

        var serializedData = "";
        // if submit button was clicked, else something else was the trigger
        if (typeof (event.isTrigger) === 'undefined') {
            var searchVal = $("#search-text-input").length > 0 ? $("#search-text-input").val() : "";
//            var searchVal = $("#search-hidden").length > 0 ? $("#search-hidden").val() : "";
            serializedData = "search=" + searchVal;
            serializedData += "&report-name-hidden=" + reportType;
            if($("#select-partnerid").length > 0) serializedData += "&partnerid=" + $("#select-partnerid").val();
            if($("#select-subtype").length > 0) serializedData += "&subtype=" + $("#select-subtype").val();
            if($("#select-brand").length > 0) serializedData += "&brand=" + $("#select-brand").val();
            if($("#select-status").length > 0) serializedData += "&status=" + $("#select-status").val();
            if (reportType == "total-hits" ||
                    reportType == "monthly-successful" ||
                    reportType == "monthly-reject" ||
                    reportType == "sap-interface" ||
                    reportType == "interim" ||
                    reportType == "successful-failed" ||
                    reportType == "revenue-assurance" ||
                    reportType == "transactions" ||
                    reportType == "daily-logs" ||
                    reportType == "logs-summary"||
                    reportType == "cxp-logs" ||
                    reportType == "daily-report") {
                var dateFrom = $("#datepicker-from").val();
                var dateTo = $("#datepicker-to").val();
                serializedData += "&date-from=" + dateFrom;
                serializedData += "&date-to=" + dateTo;

                if(reportType == "cxp-logs"){
                    var status = $("#select-status").val();
                    serializedData += "&status=" + status;
                }
                if(reportType == "successful-failed" || reportType == "revenue-assurance"){
                    var showtab = $("#show-tab-hidden").val();
                    serializedData += "&showtab=" + showtab;
                }
            }
            else if (reportType == "weblogs") {
                serializedData += "&logtype=" + $("#log-type-hidden").val();
            }
        }
        else {
                serializedData = $form.serialize();
        }

        // let's disable the inputs for the duration of the ajax request
        // Note: we disable elements AFTER the form data has been serialized.
        // Disabled form elements will not be serialized.
        $inputs.prop("disabled", true);

//        if (requestType == 'export') {
//            event.preventDefault();
//            $inputs.prop("disabled", false);
//            window.open("/transactions/export?" + serializedData);
//            $("#request-type").val("report");
//
//            if (reportType == 'successful-detailed' || reportType == 'failed-detailed') {
//                $("#report-name-hidden").val("successful-failed");
//            } else if (reportType == 'active' ||
//                    reportType == 'pending' ||
//                    reportType == 'refunded' ||
//                    reportType == 'canceled') {
//                $("#report-name-hidden").val("revenue-assurance");
//            }
//            request = null;
//            event.preventDefault();
////            return false;
//        }
//        else
        if (reportType == 'transactions') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/transactions/search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'roles') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/roles/search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'pages') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/pages/search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'options') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/options/search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'user') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/user/search",
                type: "get",
                data: serializedData
            });
        }
         else if (reportType == 'time') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/time/search",
                type: "get",
                data: serializedData
            });
        }

         else if (reportType == 'time') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/time/update",
                type: "get",
                data: serializedData
            });
        }
         else if (reportType == 'holiday') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/holiday/add",
                type: "get",
                data: serializedData
            });
        }    else if (reportType == 'holiday') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/holiday/update",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'loggedusers'){
            request = $.ajax({
                url: "/user/loggedsearch",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'weblogs') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/weblogs/search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'userlog') {
            request = $.ajax({
               url: "/weblogs/useradd",
               type: "get",
               data: serializedData
            });
        }
        else if (reportType == 'rolelog'){
            request = $.ajax({
                url: "/weblogs/role",
                type: "get",
                data: serializedData
            })
        }
        else if (reportType == 'pagelog'){
            request = $.ajax({
                url: "/weblogs/page",
                type: "get",
                data: serializedData
            })
        }
        else if (reportType == 'optionlog'){
            request = $.ajax({
                url: "/weblogs/option",
                type: "get",
                data: serializedData
            })
        }
        else if (reportType == 'transaction-details') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/transactions/transaction-details-search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'daily-logs') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/transactions/daily-logs-search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'logs-summary') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/transactions/logs-summary-search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'total-hits') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/transactions/total-hits-search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'monthly-successful') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/transactions/monthly-successful-search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'monthly-reject') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/transactions/monthly-reject-search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'daily-report') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/transactions/daily-report-search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'sap-interface') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/transactions/sap-interface-search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'interim') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/transactions/interim-search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'successful-failed') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/transactions/successful-failed-search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'revenue-assurance') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/transactions/revenue-assurance-search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'cxp-logs') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/transactions/cxp-logs-search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'schedules') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/schedules/search",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'request-getall') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/request/getall",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'empgroup-view') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/empgroup/groupsearch",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'emprole-view') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/empgroup/rolesearch",
                type: "get",
                data: serializedData
            });
        }
        else if (reportType == 'employee-viewall') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/employee/viewallsearch",
                type: "get",
                data: serializedData
            });
        }
		else if (reportType == 'holiday-getall') {
            // fire off the request to /form.php
            request = $.ajax({
                url: "/holiday/getall",
                type: "get",
                data: serializedData
            });
        }
        else {
            request = null;
        }


        if(request!=null){
            // callback handler that will be called on success
            request.done(function(response, textStatus, jqXHR) {
                // log a message to the console
                if (requestType != "export") {
                    var parsedResponse = $.parseJSON(response);
                    if(typeof(parsedResponse.invalid) === 'undefined'){
                        switch (reportType) {
    //                        case 'revenue-assurance':
    //                            var records = parsedResponse.records;
    //                            $.each(records, function(i, row) {
    //                                var tableOutput = parseResultOutput(row);
    //                                var tableId = "#search-tbody-" + i;
    //                                $(tableId).html(tableOutput);
    //                            });
    //                            break;
                            default:
                                console.log(parsedResponse.records.length);
                                if(parsedResponse.records.length>0){
                                    var tableOutput = parseResultOutput(parsedResponse.records);
                                    $("#search-tbody").html(tableOutput);
                                }else{
                                    var colspan = $("table > thead").find("> tr:first > th").length
                                    var noResultHtml = '<tr id="tr-noresults">'
                                                  + '<td colspan="'+colspan+'" style="text-align:center;font-weight:bold;">'
                                                  + ' No results found.'
                                                  + '</td>'
                                                  + '</tr>';
                                    $("#search-tbody").html(noResultHtml);
                                }

                                break;
                        }
                        var paginationOutput = parseResultPaging(parsedResponse);
                        $("#pagination-ul").html(paginationOutput);
                    }else{
                        $("#error_modal_message").html(parsedResponse.msg);
                        $("#error_dialog").modal();
                    }
                    return false;

                }
            });

            // callback handler that will be called on failure
            request.fail(function(jqXHR, textStatus, errorThrown) {
                // log the error to the console
                console.error(
                        "The following error occured: " +
                        textStatus, errorThrown
                        );
            });

            // callback handler that will be called regardless
            // if the request failed or succeeded
            request.always(function() {
                // reenable the inputs
                $inputs.prop("disabled", false);
            });

            // prevent default posting of form
            event.preventDefault();
        }

    });

    $("#export-button").click(exportReport);

//    $("#export-button").click(function() {
//        var serializedData = $("#search-form").serialize();
//        var reportType = $("#report-name-hidden").val();
//        window.open("/transactions/export?" + serializedData);
//
//        if (reportType == 'successful-detailed' || reportType == 'failed-detailed') {
//            $("#report-name-hidden").val("successful-failed");
//        } else if (reportType == 'active' ||
//                reportType == 'pending' ||
//                reportType == 'refunded' ||
//                reportType == 'canceled') {
//            $("#report-name-hidden").val("revenue-assurance");
//        }
//        request = null;
////            return false;
//
////        if ($("#search-form").valid() == true) {
////            $("#request-type").val("export");
////            $("#search-form").submit();
////        }
//    });
});

function exportReport() {
    var serializedData = $("#search-form").serialize();

    // for ticket 1052
    serializedData = serializedData.replace("search-display","search");

    var reportType = $("#report-name-hidden").val();
    window.open("/transactions/export?" + serializedData);

    if (reportType == 'successful-detailed' || reportType == 'failed-detailed') {
        $("#report-name-hidden").val("successful-failed");
    } else if (reportType == 'active' ||
            reportType == 'pending' ||
            reportType == 'refunded' ||
            reportType == 'canceled') {
        $("#report-name-hidden").val("revenue-assurance");
    }
}

// @TODO: Function for binding on-click event to content loaded through ajax.
//$(".pdt-page-a").observe('click',function(event){
//
//    Event.stop(event); // suppress default click behavior, cancel the event
//    /* your onclick code goes here */
//});


function parseResultOutput(records) {
    var tableOutput = "";
//    var records = response.records;
    var reportType = $("#report-name-hidden").val();

    $.each(records, function(i, row) {
        tableOutput += "<tr>";


        if (reportType === 'user') {
            tableOutput += '<td><input type="checkbox" name="userdelete[]" value="' + row.username + '"></td>';
        }

        $.each(row, function(x, column) {
            if (!((reportType == "pages" || reportType == "roles" || reportType == "options" || reportType == 'schedules' || reportType == 'time' || reportType == 'holiday') && (x === "id"))
                    ){
                tableOutput += "<td>" + column + "</td>";
            }
        });

        if (reportType === 'user') {
            tableOutput += '<td>';
            if(row.username!=='superadmin'){
                tableOutput += '<a href="/user/update?username=' + row.username + '">Edit</a>';
                tableOutput += ' | <a href="/user/delete?username=' + row.username + '" onclick="javascript: confDelete(this, \'' + row.username + '\'); return false;">Delete | </a>';
            }
            tableOutput += '<a href="/user/pwdreset?username=' + row.username + '">Reset Password</a></td>';
        }

		if (reportType === 'request-getall') {

            if($("#role-hidden").val() === 'system-administrator' && row.status == "Filed"){
                tableOutput += '<td><a href="/request/approve/' + row.id + '" >Approve</a>';
                tableOutput += ' | <a href="/request/disapprove/' + row.id + '" >Disapprove</a>';
                if($("#current-employee").val() === row.employee_id && row.status == "Filed"){
                    tableOutput += ' | <a href="/request/cancel/' + row.id + '" >Cancel</a></td>';
                }else{
                    tableOutput += '</td>';
                }
            }
            else if(row.employee_id == $("#current-employee").val() && row.status == "Filed"){
                tableOutput += '<td><a href="/request/cancel/' + row.id + '">Cancel</a></td>';
            }
            else{
                tableOutput += '<td>No Action Available</td>';
            }

        }
        else if (reportType === 'roles') {
            if (row.id == '1') {
                tableOutput += '<td></td>';
            } else {
                tableOutput += '<td><a href="/roles/update?id=' + row.id + '">Edit</a>';
                tableOutput += ' | <a href="/roles/delete?id=' + row.id + '" onclick="javascript: confDelete(this, \'' + row.role + '\'); return false;">Delete</a></td>';
            }

        }
        else if (reportType === 'pages') {
            tableOutput += '<td><a href="/pages/update?id=' + row.id + '">Edit</a>';
            tableOutput += ' | <a href="/pages/delete?id=' + row.id + '" onclick="javascript: confDelete(this, \'' + row.pagename + '\'); return false;">Delete</a></td>';
        }
        else if (reportType === 'options') {
            tableOutput += '<td><a href="/options/update/' + row.id + '">Edit</a></td>';
        }
		else if (reportType === 'holiday-getall') {

			if($("#role-hidden").val() === 'system-administrator'){
				tableOutput += '<td><a href="/holiday/update?id=' + row.id + '">Edit</a></td>';
			}
			
        }
          else if (reportType === 'time') {
            if($("#role-hidden").val() === 'system-administrator'){
                tableOutput += '<td><a href="/time/update?id=' + row.id + '">Edit</a></td>';
            }else{
                tableOutput += "<td>No Action Available</td>";
            }
        }
        else if (reportType === 'schedules') {
            if($("#role-hidden").val() === 'system-administrator'){
                tableOutput += '<td><a href="/schedules/update/' + row.id + '">Edit</a></td>';
            }else{
                tableOutput += "<td>No Action Available</td>";
            }
        }
        else if (reportType === 'empgroup-view') {
            if($("#role-hidden").val() === 'system-administrator'){
                tableOutput += '<td><a href="/empgroup/groupupdate?id=' + row.id + '">Edit</a>';
//                tableOutput += ' | <a href="/pages/delete?id=' + row.id + '" onclick="javascript: confDelete(this, \'' + row.pagename + '\'); return false;">Delete</a></td>';
            }else{
                tableOutput += "<td>No Action Available</td>";
            }

        }
        else if (reportType === 'emprole-view') {
            if($("#role-hidden").val() === 'system-administrator'){
                tableOutput += '<td><a href="/empgroup/roleupdate?id=' + row.id + '">Edit</a>';
//                tableOutput += ' | <a href="/pages/delete?id=' + row.id + '" onclick="javascript: confDelete(this, \'' + row.pagename + '\'); return false;">Delete</a></td>';
            }else{
                tableOutput += "<td>No Action Available</td>";
            }

        }
        else if (reportType === 'employee-viewall') {
            tableOutput += '<td><a href="/employee/update/' + row.id + '">Edit</a>';
            tableOutput += '&nbsp;|&nbsp<a href="/employee?id=' + row.id + '">View&nbsp;Details</a></td>';
        }
        tableOutput += "</tr>";

    });

    return tableOutput;
}

function parseResultPaging(response) {

    var recText = parsePageLabel(response);
    var currpage = parseInt(response.currPage);

    var lowerLim = currpage - 2;
    var upperLim = currpage + 2;

    if (lowerLim < 1) {
        lowerLim = 1;
        upperLim = 5;
    }
    if (upperLim > response.pageCount) {
        upperLim = response.pageCount;
        lowerLim = upperLim - 4;
    }

    window.lowerLim = lowerLim;
    window.upperLim = upperLim;
    window.pageCount = response.pageCount;

    var prevPage = (currpage - 1) >= 1 ? currpage - 1 : 1;
    var nextPage = (currpage + 1) <= response.pageCount ? currpage + 1 : response.pageCount;

    var reportType = $("#report-name-hidden").val();

    var paginationOutput = "";
    if (reportType != 'daily-logs')
        paginationOutput += '<li class="disabled"><a href="#" >' + recText + '</a></li>';

    paginationOutput += '<li><a onclick="return selectPage(1)" >&laquo;</a>';
    paginationOutput += '</li><li><a onclick="return selectPage(' + prevPage + ')" href="#">‹</a></li>';
    for (var i = 1; i <= response.pageCount; i++) {
        var styleText = ' style="display:none" ';
        if (i >= lowerLim && i <= upperLim)
            styleText = ' style="display:inline" ';
        var liClass = "";
        if (i === currpage)
            liClass = "active";
        paginationOutput += '<li class="' + liClass + '"><a id="page-a-' + i + '" class="page-a" onclick="return selectPage(' + i + ')"' + styleText + '>' + i + '</a></li>';
    }

    paginationOutput += '<li><a onclick="return selectPage(' + nextPage + ')" href="#">›</a></li>';
    paginationOutput += '<li><a onclick="return selectPage(' + response.pageCount + ')">&raquo;</a></li>';

    return paginationOutput;

}

function parseResultHeader(response) {
    var headerOutput = "<tr>";
    var headers = response.headers;

    $.each(headers, function(i, row) {
        headerOutput += "<th>" + row + "</th>";
    });

    headerOutput += "</tr>";
    return headerOutput;
}

function parsePageLabel(response) {
    var labelText = "";

    var lowerNum = response.limit * (response.currPage - 1) + 1;
    var upperNum = response.limit * (response.currPage - 1) + response.records.length;

    labelText = lowerNum + " to " + upperNum + " of " + response.count;

    return labelText;
}

function selectPage(i) {
    $("#pg-hidden").val(i);
    $("#search-form").submit();
    return false;
}

function sortResultsBy(colName) {
    var currSortCol = $("#sort-col-hidden").val();
    var currSortVal = $("#sort-val-hidden").val();

    $(".sort-span").html('');

    if (colName === currSortCol) {
        if (currSortVal === "ASC"){
            $("#"+colName+"-sort").html('▼');
            $("#sort-val-hidden").val("DESC");
        }
        else{
            $("#"+colName+"-sort").html('▲');
            $("#sort-val-hidden").val("ASC");
        }
    } else {
        $("#sort-col-hidden").val(colName);
        $("#"+colName+"-sort").html('▼');
        $("#sort-val-hidden").val("DESC");
    }

    $("#pg-hidden").val(1);

    $("#search-form").submit();
}

function confDelete(alink) {
    $("#delete_url_global").val(alink.href);
    $("#conf_delete_dialog").modal();
}
