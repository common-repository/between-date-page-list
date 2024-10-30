$ = jQuery;
$(document).ready(function(){
	$(".sbmt-date span").click(function(){
        $(".a.csv-icon").css("display","none");
        var frm_date = $(".frm-date input").val();
        var to_date = $(".to-date input").val();
        if(frm_date != "" && to_date != ""){
        	$(".dwpl-main input").css("border","1px solid #ddd");
        	$('div.blockMe').block({ message: '<div><div class="bubble-loader"><div class="1"></div><div class="2"></div><div class="3"></div></div>',css:
				{
					border: 'medium none !important',
					backgroundColor: 'inherit',
					color: '#FFFFF'
				}
			});
        	$.ajax({
	        	type: 'POST', 
	            url: my_ajax_object.ajax_url,
	            dataType : "JSON",
	            data : {action: "bdpl_between_date_page_list_ajax_fun", 'frm_date' : frm_date , 'to_date' : to_date},
	            success: function(data,textStatus, XMLHttpRequest){
	            	$(".result-block").html(data.date_html);
	            	$("a.csv-icon").css("display","block");
	            	$('div.blockMe').unblock();
                 }
	        });
        }else{
        	if(frm_date == ""){
        		$(".frm-date input").css("border","1px solid red");
        	}else{
        		$(".frm-date input").css("border","1px solid #ddd");
        	}
        	if(to_date == ""){
        		$(".to-date input").css("border","1px solid red");
        	}else{
        		$(".to-date input").css("border","1px solid #ddd");
        	}
        }
    });
        function exportTableToCSV($table, filename) {

        var $rows = $table.find('tr:has(td)'),

            // Temporary delimiter characters unlikely to be typed by keyboard
            // This is to avoid accidentally splitting the actual contents
            tmpColDelim = String.fromCharCode(11), // vertical tab character
            tmpRowDelim = String.fromCharCode(0), // null character

            // actual delimiter characters for CSV format
            colDelim = '","',
            rowDelim = '"\r\n"',

            // Grab text from table into CSV formatted string
            csv = '"' + $rows.map(function (i, row) {
                var $row = $(row),
                    $cols = $row.find('td');

                return $cols.map(function (j, col) {
                    var $col = $(col),
                        text = $col.text();

                    return text.replace(/"/g, '""'); // escape double quotes

                }).get().join(tmpColDelim);

            }).get().join(tmpRowDelim)
                .split(tmpRowDelim).join(rowDelim)
                .split(tmpColDelim).join(colDelim) + '"';

				// Deliberate 'false', see comment below
        if (false && window.navigator.msSaveBlob) {

			var blob = new Blob([decodeURIComponent(csv)], {
	              type: 'text/csv;charset=utf8'
            });
            
            // Crashes in IE 10, IE 11 and Microsoft Edge
            // See MS Edge Issue #10396033: https://goo.gl/AEiSjJ
            // Hence, the deliberate 'false'
            // This is here just for completeness
            // Remove the 'false' at your own risk
            window.navigator.msSaveBlob(blob, filename);
            
        } else if (window.Blob && window.URL) {
						// HTML5 Blob        
            var blob = new Blob([csv], { type: 'text/csv;charset=utf8' });
            var csvUrl = URL.createObjectURL(blob);

            $(this)
            		.attr({
                		'download': filename,
                		'href': csvUrl
		            });
				} else {
            // Data URI
            var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

						$(this)
                .attr({
               		  'download': filename,
                    'href': csvData,
                    'target': '_blank'
            		});
        }
    }

    // This must be a hyperlink
    $(".csv-icon").on('click', function (event) {
        // CSV
        var args = [$('.result-block>table'), 'page-list.csv'];
        
        exportTableToCSV.apply(this, args);
        
        // If CSV, don't do event.preventDefault() or return false
        // We actually need this to be a typical hyperlink
    });
});

$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);