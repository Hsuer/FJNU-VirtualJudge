$(function(){
	$("#addBtn").click(function(){
		addRow();
	});
	
	$(document).on('click', '.deleteRow', function(){
		if($("#addTable tr.tr_problem:visible").length > 1) {
			$(this).parent().parent().remove();
		}
		if ($("#addTable tr.tr_problem:visible").length < 26){
			$("#addBtn").show();
		}
		if ($("#addTable tr.tr_problem:visible").length < 1){
			addRow();
		}
	});
	
	$(document).on('change', '[name=OJs]', function(){
		last = "vj";
		updateTitle($(this).parent().parent());
	});
	
	$(document).on('focus', '[name=probNums]', function(){
		$row = $(this).parent().parent();
		last = $("[name=probNums]", $row).val();
		thread = window.setInterval(function(){
			updateTitle($row);
		}, 100 );
	});

	$(document).on("blur", '[name=probNums]', function(){
		window.clearInterval(thread);
		updateTitle($(this).parent().parent());
	});

	$("#addTable tr.tr_problem:visible").each(function(){
		updateTitle($(this), true);
	});
	
	if ($("#addTable tr.tr_problem:visible").length < 1){
		addRow();
	}

	$(".clk_select").click(function(){
		this.select();
	});
	$(".clk_select").blur(function(){
		if (!this.value || this.value.match(/^\s+$/)){
			this.value = 0;
		}
	});
});

var oj, pid, title;
var last;
var proInf;
function updateTitle($row, init){
	if (!init && $("[name=probNums]", $row).val() == last) {
		return;
	}
	last = $("[name=probNums]", $row).val();
	gettitle( $("[name=OJs]", $row).val(), $("[name=probNums]", $row).val(), $row );
}

function sendtocrawl(id) {
	PNotify.prototype.options.styling = "bootstrap3";
	PNotify.prototype.options.styling = "fontawesome";
	$("#"+id).addClass("disabled");
	$("#"+id+" i").addClass("fa-spin");
	$.get("http://localhost/OnlineJudge/problem/crawl/"+id, function(result) {
	    if(result.status == 1) {
	        new PNotify({
	            type: 'info',
	            icon: false,
	            text: '<i class="fa fa-info-circle"></i> ' + result.msg,
	            delay: 1000
	        });
	        query(id);
	    }
	    else {
	        new PNotify({
	            type: 'warning',
	            icon: false,
	            text: '<i class="fa fa-warning"></i> ' + result.msg,
	            delay: 2000
	        });
	    }
	});
}

function query(id) {
	PNotify.prototype.options.styling = "bootstrap3";
	PNotify.prototype.options.styling = "fontawesome";
    var int = setInterval(function() {
        $.ajax({
            type: "GET",
            url: "http://localhost/OnlineJudge/problem/query/"+id,
            dataType: "json",
            success: function(result) {
                if(result.status == 1) {
                    new PNotify({
                        type: 'success',
                        icon: false,
                        text: '<i class="fa fa-check"></i> '+result.oj +'-'+ result.id + ' is added to list.',
                        delay: 3000
                    });
                    clearInterval(int);
              		updateTitle($("#"+id).parent().parent(), true);
                }
            }
        });
    },1000);
}

function gettitle( oj, pid, row ){
	if ( pid == 0 || pid == null ) {
		row.children().eq(-1).html("<span class='label label-info'>Waiting input</span>");
		return ;
	} else {
	    row.children().eq(-1).html("<span class='label label-warning'>Loading...</span>");
	}
	var url = 'http://localhost/OnlineJudge/problem/query/'+oj+pid;
	// console.log(url);
	$.ajax({
		url: url,
		type: 'get',
		dataType: 'json',

		error: function(){ 
			var id = oj+pid;
 			row.children().eq(-1).html('<button id="'+id+'" onclick="sendtocrawl(\''+id+'\')" type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i>&nbsp; <b>Click to crawl</b></button>');
		}, 
		success: function(data) {
			if (data){
				vid = data['vid'];
				title = data['title'];
				row.children().eq(-1).html('<a target="_blank" href="http://localhost/problem/'+vid+'">' + data['title'] +'</a>');
				$("[class=vids]", row).val( vid );
				$(".protitles", row).val( title );
			} else {	
				row.children().eq(-1).html("<span class='label label-danger'>No such problem!</span>");
			}
		}
	});
}

function addRow(){
	var $originRow;
	if ($("#addTable tr.tr_problem:visible").length){
		$originRow = $("tr#addRow").prev();
	} else {
		$originRow = $("tr#addRow");
		$originRow.children().eq(-1).html("<span class='label label-info'>Waiting input</span>");
	}
	$newRow = $originRow.clone();
	$("[name=OJs]", $newRow).val($("[name=OJs]", $originRow).val());

	$newRow.removeAttr("id");
	$(":input", $newRow).removeAttr("id");
	
	var probNum = $("[name=probNums]", $newRow);
	if (probNum.val().match(/^\s*\d+\s*$/)){
		probNum.val(parseInt(probNum.val()) + 1);
	} else {  
		probNum.val("");
	}
	$newRow.insertBefore("tr#addRow").show();
	updateTitle($newRow);
	
	if ($("#addTable tr.tr_problem:visible").length >= 26){
		$("#addBtn").hide();
	}
}

// function updateNum(){
// 	$("#addTable tr.tr_problem:visible").each(function(index){
// 		$last = $("td:last-child", $(this)); 
// 		if ($last.html().charAt(1) == 'a' || $last.html().charAt(1) == 'A'){
// 			$last.prev().html(1000 + index);
// 		} else {
// 			$last.prev().html("");
// 		}
// 	});
// }