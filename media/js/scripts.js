$(document).ready(function(){

	/* Progress bar in top */
	var options = {
		bg: '#c1c1c1',
		target: document.getElementById('body'),
		id: 'mynano'
	};
	var nanobar = new Nanobar( options );
	nanobar.go(1);
	nanobar.go(100);

	/* Ask question variables */
	var busy = false;
	var ask_header_modal_content = '';

	/* Getting user timezone */
	getusertimezone();

	/* Functions for image thumbnails ratio and popup images */
	$(".img").imgLiquid();
	$('.image-link').magnificPopup({
		type:'image',
		removalDelay: 300,
		mainClass: 'mfp-fade'
	});

	/* Opening ask question modal */
	$('#askModal').on('shown.bs.modal', function () {
	  	ask_header_modal_content = $('#askModal').html();
	  	$("#add_question").focus();
		$('#add_question').bind('keyup',function(){
			var _0 = $(this).val();
			if(_0.length>8){
				$('#step_1').removeClass("disabled");
			}
			else {
				$('#step_1').addClass("disabled");
			}
		});
	});

	/* Opening report modal */
	$('#reportModal').on('show.bs.modal', function (event) {
	  	var button = $(event.relatedTarget);
	  	var id = button.data('id');
	  	var type = button.data('type');
	  	var modal = $(this);
	  	modal.find('#r_id').val(id);
	  	modal.find('#r_type').val(type);
	});

	/* Opening edit question modal */
	$('#editqModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);
	  	var id = button.data('id');
	  	$('#id_p').val(id);
	  	$('#id_p').val(id);
		var e_q = $('#db_qq_'+id).val();
		$('#add_question_p').val(e_q);
		var e_d = $('#db_qd_'+id).val();
		$('#add_description_p').val(e_d);
		var e_c = $('#db_qc_'+id).val();
		$.ajax({
			url: 'ajax/getcategoriesbyid',
			async: true,
			type: "POST",
			data: "ajax=1&categories="+e_c,
			success: function(_2){
				var _01 = JSON.stringify(_2);
				var _00 = JSON.parse(_01);
				if(_00.response!=''){
					$('.content_last_p').html(_00.response);
					$.ajax({
				        type: "POST",
				        url: "ajax/select_category_p",
				        cache: false,
				        success: function(html){
				            $(".results_p").html(html);
				            $("#results").select2();
				        }
				    });
				}
			}
		});
	});

	/* Opening edit answer modal */
	$('#editaModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);
	  	var id = button.data('id');
	  	$('#answer_id').val(id);
	  	var e_answer = $('#db_a_'+id).val();
		
		tinymce.PluginManager.add('mention', function(editor, url){
			editor.addButton('mention', {
			    title: get_text(485),
			    text: '@',
		        icon: false,
			    onclick: function(){
			      	editor.windowManager.open({
			      		title: get_text(486),
		        		body: [
							{type: 'textbox', name: 'title', label: 'Username'}
						],
						onsubmit: function(e){
							editor.insertContent('@'+e.data.title);
						}
					});
				}
			});
		});

	 	tinyMCE.PluginManager.add('stylebuttons', function(editor, url){
		    ['pre', 'p', 'code', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'].forEach(function(name){
		   	   	editor.addButton("style-" + name, {
		           	tooltip: "H " + name,
		            text: name.toUpperCase(),
		            onClick: function(){ editor.execCommand('mceToggleFormat', false, name); },
		            onPostRender: function(){
		                var self = this, setup = function(){
		                    editor.formatter.formatChanged(name, function(state){
		                        self.active(state);
		                    });
		                };
		                editor.formatter ? setup() : editor.on('init', setup);
		            }
		        })
		     });
		});

		tinymce.init({
			selector: "#answer_text_p",
			theme: "modern",
			menubar: false,
			plugins: [
				"autolink image lists fullscreen",
				"insertdatetime nonbreaking save contextmenu directionality",
				"paste stylebuttons mention placeholder"
			],
			toolbar1: "bold italic underline style-h2 blockquote numlist bullist | image mention fullscreen ",
			width: '100%',
			height: 50,
			setup: function(editor){
				editor.on('init', function(){
					editor.execCommand('mceFocus', false, 'main-text');
					editor.execCommand('mceAutoResize');
					tinymce.get('answer_text_p').selection.setContent(e_answer);
				});
			}
		});
	});

	/* Make question with uppercase */
	$('#add_question, #answer_text').bind('keyup',function(){
		if(this.value[0] != this.value[0].toUpperCase()){
            var start = this.selectionStart;
            var end = this.selectionEnd; 
            this.value = this.value[0].toUpperCase() + this.value.substring(1);
            this.setSelectionRange(start, end);
        }
	});

	/* Closing ask question modal */
	$('#askModal').on('hidden.bs.modal', function () {
		$('#askModal').html(ask_header_modal_content);
		$('.loadingask').addClass('none');
	});

	/* Closing report modal */
	$('#reportModal').on('hidden.bs.modal', function () {
		$('#r_id').val('');
		$('#r_type').val('');
		$('#r_reason').val('');
		$('#reportModal .success').addClass('none');
		$('#reportModal #rq').html('500');
		$('#reportModal .btn-primary').removeClass('none');
	    $('#reportModal .sent').removeClass('none');
	});

	/* Closing edit answer modal */
	$('#editaModal').on('hidden.bs.modal', function () {
		$('#answer_id').val('');
		tinymce.get('answer_text_p').setContent('');
		tinymce.execCommand('mceRemoveEditor', true, 'answer_text_p');
	});

	/* Checking if answer has length > 2 characters */
	$('#answer_text').bind('keyup',function(){
		var _0 = $(this).val();
		if(_0.length>2){
			$('#answer_btn').removeClass("disabled");
		}
		else {
			$('#answer_btn').addClass("disabled");
		}
	});

	/* Disable submit button after choosing topics */
	$("#sbmt").click(function () {
       $(".sbmt").attr("disabled", true);
       $('#sbmt').submit();
     });

	/* Events for inputs for validating */
	$('#username').bind('keyup',checkUsername);
	$("#username").keyup(function(){
	    var value = $(this).val().replace(/[^-a-zA-Z0-9]/g, "");
	    $(this).val(value)
	})
	$('#password').bind('change keyup copy paste cut',function(){
		$(this).removeClass("highlightRed");
		var _0 = $(this).val();
    	if(_0.length>2){
			$('#submit-login').removeClass("disabled");
		}
	});
    
	$('#email').bind('keyup',checkEmail);
	$('#password2').bind('keyup',function(){
		$(this).removeClass("highlightRed");
	});
	$('#captcha').bind('keyup',function(){
		$(this).removeClass("highlightRed");
	});
	$('#name').bind('keyup',function(){
		$(this).removeClass("highlightRed");
		validationOK("name");
	});

	/* Create error messages in top clickable for fadeout action */
	$('#errors').click(function(){
		$('#errors').fadeOut();
	});
	
	/* Events for ajax search */
	$('#search_input').bind('keyup',search);
	$('#search_input').focus(function(){
        $('.results_frame').on('mousedown', function(event) {
	   		event.preventDefault();
		});
	}).blur(function(){
  		$('#search_input').val('');
	    search();
	});
});

/* Methods for adding categories to array and remove them */
jQuery.fn.extend({
	addToArray: function(value){
	    return this.filter(":input").val(function(i, v){
	        var arr = v.split(',');
	        arr.push(value);
	        return arr.join(',');
	    }).end();
	},
	removeFromArray: function(value){
	    return this.filter(":input").val(function(i, v){
	        return $.grep(v.split(','), function(val){  
	            return val != value;
	        }).join(',');
	    }).end();
	}
});

/* Method for highlight errors for input at registration */
jQuery.fn.highlight = function (str, className){
    var regex = new RegExp(str, "gi");
    return this.each(function (){
        $(this).contents().filter(function(){
            return this.nodeType == 3 && regex.test(this.nodeValue);
        }).replaceWith(function(){
            return (this.nodeValue || "").replace(regex, function(match){
                return "<span class=\"" + className + "\">" + match + "</span>";
            });
        });
    });
};

/* Adding notification counter at page`s title */
function notification_title(){
	counter = $('.notification_counter').html();
	document.title = '(' + counter + ') ' + document.title;
}


/* Geting translated words from header.tpl */
function get_text(word_id) {
	return $("#w_"+word_id).html();
}

/* Function for checking email address if is valid and is not used by another users */
function checkEmail(){
    var _0 = $("#email").val();
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if(filter.test(_0)){
    	$("#email").removeClass("highlightRed");
    	validationOK("email");
        $.ajax({
			url: 'ajax/checkemail',
			async: true,
			type: "POST",
			data: {
	            ajax: 1,
	            email: _0
	        },
	        success: function (_1){
			var _2 = JSON.parse(_1);
	            if(_2.error){
	                validationError("email", _2.error);
	            } else {
	                validationOK("email");
	            }
	        }
	    });
    }
    else {
        validationError("email", get_text(470));
    }
}

/* Function for getting user`s timezone and sending via AJAX to server */
function getusertimezone(){
	var timezone = jstz.determine();
	var tzname = timezone.name(); 
	$.ajax({
		url: 'ajax/tz',
		async: true,
		type: "POST",
		data: "tzname="+tzname+"&ajax=1"
    });
}

/* Function fow showing errors at validation */
function validationError(el, _0){
    var _1 = $("#" + el + "Status");
    if(!_1){
        return;
    }
    $(_1).html(_0);
    $(_1).removeClass("validationOK");
    $(_1).addClass("validationError");
    return false;
}

/* Function for removing validation errors */
function validationOK(el){
    var _1 = $("#" + el + "Status");
    if(!_1){
        return;
    }
    $(_1).html("");
    $(_1).removeClass("validationError");
    $(_1).addClass("validationOK");
    return true;
}

/* Function for checking username if is not already used */
function checkUsername(){
    var _0 = $("#username").val();
    if(_0.length>2){
	    $("#username").removeClass("highlightRed");
	    $.ajax({
			url: 'ajax/checkusername',
			async: true,
			type: "POST",
			data: {
	            ajax: 1,
	            username: _0
	        },
	        success: function (_1){
				var _2 = JSON.parse(_1);
	            if(_2.error){
	                validationError("username", _2.error);
	            } else {
	                validationOK("username");
	            }
	        }
	    });
	}
}

/* Function used for ajax search */
function search(){
    var _0 = $("#search_input").val();
    if(_0.length>1){
	    $.ajax({
	    	url: 'ajax/search',
			async: true,
			type: "POST",
			data: "ajax=1&query="+_0,
			success: function(_2){
				var _01 = JSON.stringify(_2);
				var _00 = JSON.parse(_01);
				if(_00.response){
					$('.results_frame').html(_00.response);
				}
				else {
					$('.results_frame').html('');
				}
	        }
	    });
	}
	else {
		$('.results_frame').html('');
	}
}

/* Function return if email is valid */
function IsEmail(sEmail){
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if(filter.test(sEmail)){
        return true;
    }
    else {
        return false;
    }
}

/* Function used at registration - check if all fields are completed */
function checkProfileSettings(){
	obj = [];
    _0 = 0;
	_1 = $("#username");
	_2 = $("#name");
	_3 = $("#email");
	_4 = $("#captcha");
	obj.push(_1);
	obj.push(_2);
	obj.push(_3);
	obj.push(_4);
	$.each(obj, function(){
		this.removeClass("highlightRed");
		if(!this.val()){
			this.addClass("highlightRed");
			_0++;
		}
	});
    if($("#usernameStatus").hasClass("validationError")){
        $("#username").addClass("highlightRed");
        _0++;
    }
    if($("#password") && $("#password").is(":visible")){
        if($("#password").val() == "" || $("#password2").val() == ""){
            $("#password").addClass("highlightRed");
            $("#password2").addClass("highlightRed");
            _0++;
        } 
		else {
            if($("#password").val() != $("#password2").val()){
                $("#password").addClass("highlightRed");
                validationError("password", get_text(471));
                _0++;
            } 
			else {
                validationOK("password");
                $("#password").removeClass("highlightRed");
                validationOK("password2");
                $("#password2").removeClass("highlightRed");
            }
        }
    }
    if($("#email")){
		emailval = $("#email").val();
        if(!IsEmail(emailval)){
            $("email").addClass("highlightRed");
            error = get_text(470);
            validationError("email", error);
            _0++;
        } 
		else {
            validationOK("email");
        }
    }
    if($("#captcha").hasClass("validationError")){
        $("#captcha").addClass("highlightRed");
        _0++;
    }
    return _0;
}

/* Function for checking if all fields are completed and showing errors */
function setupComplete(){
    var _0 = 0;
    _0 += checkProfileSettings();
    if(!$('#terms').is(':checked')){
        $("#terms").addClass("highlightRed");
        $("#termsLabel").addClass("highlightRed");
        _0++;
    } else {
        $("#terms").removeClass("highlightRed");
        $("#termsLabel").removeClass("highlightRed");
    }
    if(!_0){
        return;
    } 
	else {
        return false;
    }
}

function answerComplete(){
	$("#answer_btn").addClass('disabled');
	$("#answer_btn .fa-abtn").removeClass('none');
    var editorContent = tinyMCE.get('answer_text').getContent();
    if(editorContent!=''){
     	return;
    }
    else {
    	alert(get_text(489));
    	$("#answer_btn").removeClass('disabled');
		$("#answer_btn .fa-abtn").addClass('none');
        return false;
    }
}

function socialComplete(){
    var _0 = 0;
    if(!$("#username").val()){
		$("#username").addClass("highlightRed");
		validationError("username", get_text(474));
		_0++;
	}
	else {
		$("#username").removeClass("highlightRed");
	}
	if(!$("#name").val()){
		$("#name").addClass("highlightRed");
		validationError("name", get_text(475));
		_0++;
	}
	else {
		$("#name").removeClass("highlightRed");
	}
	if(!$("#email").val()){
		$("#email").addClass("highlightRed");
		_0++;
	}
	else {
		$("#email").removeClass("highlightRed");
	}
	if($("#email")){
		emailval = $("#email").val();
        if(!IsEmail(emailval)){
            $("email").addClass("highlightRed");
            validationError("email", get_text(476));
            _0++;
        } 
		else {
            validationOK("email");
        }
    }
    if(!_0){
        return;
    } 
	else {
        return false;
    }
}

function checkAccountSettings(){
    var _0 = 0;
	if(!$("#name").val()){
		$("#name").addClass("highlightRed");
		_0++;
	}
	else {
		$("#name").removeClass("highlightRed");
	}
	if(!$("#email").val()){
		$("#email").addClass("highlightRed");
		_0++;
	}
	else {
		$("#email").removeClass("highlightRed");
	}
	if(!$("#bio").val().length>50){
		$("#bio").addClass("highlightRed");
		_0++;
	}
	else {
		$("#bio").removeClass("highlightRed");
	}
    if($("#password") && $("#password").is(":visible")){
        if($("#password2").val() == ""){
            $("#password").addClass("highlightRed");
            $("#password2").addClass("highlightRed");
            _0++;
        } 
		else {
                validationOK("password");
                $("#password").removeClass("highlightRed");
                validationOK("password2");
                $("#password2").removeClass("highlightRed");
        }
    }
    if($("#email")){
		emailval = $("#email").val();
        if(!IsEmail(emailval)){
            $("email").addClass("highlightRed");
            validationError("email", get_text(470));
            _0++;
        } 
		else {
            validationOK("email");
        }
    }
    return _0;
}

function changePassword(){
    $("#changePasswordLink").hide();
    $("#changePassword").show();
    $("#password").show().focus();
    $("#changePassword2").show();
    $("#password2").show();
}

function checkWebsite(){
    if($("#website")){
         if($("#website").val().match(/^http/)){
            $.noop()
        }
        else {
            var cur_val = $("#website").val(); 
            $("#website").val('http://' + cur_val);
        } 
    }
    $("#saveProfileButton").attr('disabled', 'disabled');
    $("#saveProfileIndicator").show();
    $("#profileForm").submit();
}

function deletePhoto(){
    $("#photoPreview").hide();
    $("#photoUpload").show();
    $("#deletephoto").val('1');
}

function saveSettings(){
	$("#saveSettingsButton").addClass('disabled');
	$("#saveSettingsIndicator").show();
    var _0 = checkAccountSettings();
    if(_0){
    	$("#saveSettingsButton").removeClass('disabled');
    	$("#saveSettingsIndicator").hide();
        return true;
    }
    $("#settingsForm").submit();
}

function like(_0){
	$("#btn-"+_0).addClass('disabled');
	$("#btn-"+_0+" .fa-spinner").removeClass('none');
	$.ajax({
		url: 'ajax/like',
		async: true,
		type: "POST",
		data: "id="+_0+"&ajax=1",
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			var _3 = $(".countlike"+_0).html();
			if(_00.type == 'delete'){ 
				_3--;
				$("#btn-"+_0).removeClass("unfollow_b").addClass("follow_b"); 
				$("#btn-"+_0+" .unfollow_text").addClass("none"); 
				$("#btn-"+_0+" .follow_text").removeClass("none"); 
			}
			else {
				_3++;
				$("#btn-"+_0).removeClass("follow_b").addClass("unfollow_b"); 
				$("#btn-"+_0+" .follow_text").addClass("none"); 
				$("#btn-"+_0+" .unfollow_text").removeClass("none");
			}
			$(".countlike"+_0).html(_3);
			$("#btn-"+_0).removeClass('disabled');
			$("#btn-"+_0+" .fa-spinner").addClass('none');
		},
		error: function (_6){
			location.reload();
        }
    });
}

function upvote(_0){
	$("#aa_"+_0+" #btn-"+_0).addClass('disabled');
	$("#aa_"+_0+" #btn-"+_0+" .fa-spinner").removeClass('none');
	$.ajax({
		url: 'ajax/upvote',
		async: true,
		type: "POST",
		data: "id="+_0+"&ajax=1",
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			var _3 = $("#aa_"+_0+" .countlike"+_0).html();
			if(_00.type == 'add'){ 
				_3++;
				$("#aa_"+_0+" #btn-"+_0).removeClass("follow_b").addClass("unfollow_b"); 
				$("#aa_"+_0+" #btn-"+_0+" .follow_text").addClass("none"); 
				$("#aa_"+_0+" #btn-"+_0+" .unfollow_text").removeClass("none");
			}
			else {
				_3--;
				$("#aa_"+_0+" #btn-"+_0).removeClass("unfollow_b").addClass("follow_b"); 
				$("#aa_"+_0+" #btn-"+_0+" .unfollow_text").addClass("none"); 
				$("#aa_"+_0+" #btn-"+_0+" .follow_text").removeClass("none"); 
			}
			$("#aa_"+_0+" .countlike"+_0).html(_3);
			$("#aa_"+_0+" #btn-"+_0).removeClass('disabled');
			$("#aa_"+_0+" #btn-"+_0+" .fa-spinner").addClass('none');
		},
		error: function (_6){
			location.reload();
        }
    });
}

function checkLoginForm(){
	if(!$("#username").val()){
        $("#username").addClass("highlightRed").focus();
        ok = false;
    }
	else { 
		if(!$("#password").val()){
	        $("#password").addClass("highlightRed").focus();
	        ok = false;
	    }
		else {
			var ok = true;
		}
	}
    return ok;
}

/* Function for loading stream - first posts */
function stream(type){
	id = $("#last_value").val();
	if('#stream'){
		id = $("#l_v").val();
	}
	busy = false;
	$.ajax({
		url: 'ajax/stream',
		async: true,
		type: "POST",
		data: "ajax=1&type="+type+"&last_id="+id,
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			$("#stream").html(_00.response);
			$(".loadingstream").css("display","none");
			$(".img").imgLiquid();
			$('.image-link').magnificPopup({type:'image',removalDelay: 300, mainClass: 'mfp-fade'});
			$("#last_value").val(_00.last_id);
			var remain = $("#remain").val(_00.remain);
			if(_00.remain>0){
				$(window).scroll(function(){
					if($(window).scrollTop()+$(window).height() > $("#stream").height() && !busy){
						busy = true;
						var id = $("#last_value").val();
						var remain = $("#remain").val();
						if(remain>0){
							stream_load(type,id);
						}
						if(_00.remain<1){
							$('#stream').append('<p class="end_stream">'+get_text(472)+'</p>');
						}
					}
				});
			}
			else {
				$('#stream').append('<p class="end_stream">'+get_text(472)+'</p>');
			}
			window.setInterval(function(){
				if($('.MoreStoriesIndicator').hasClass('none')){
					var first_value = $('.feed_item:first').attr('time');
					$("#first_value").val(first_value);
					$.ajax({
						url: 'ajax/check_stream',
						async: true,
						type: "POST",
						data: "ajax=1&type="+type+"&id="+first_value,
						success: function(_2){
							var _01 = JSON.stringify(_2);
							var _00 = JSON.parse(_01);
							if(_00.response!='0'){
								$('.MoreStoriesIndicator').removeClass('none');
							}
						}
					});
				}
			}, 5000);
		}
	});
}

/* Function for loading stream - next ajax posts */
function stream_load(type, last_id){						
	$(".loadingstream").css("display","block");
	$.ajax({
		url: 'ajax/stream',
		async: true,
		type: "POST",
		data: "ajax=1&type="+type+"&last_id="+last_id,
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			$(".loadingstream").css("display","none");
			if(_00.response!=""){
				$(".loadingstream").css("display","none");
				$('#stream').append(_00.response);
				$(".img").imgLiquid();
				$('.image-link').magnificPopup({type:'image',removalDelay: 300, mainClass: 'mfp-fade'});
				
				$("#last_value").val(_00.last_id);
				$("#remain").val(_00.remain);
				if(_00.remain<1){
					$('#stream').append('<p class="end_stream">'+get_text(472)+'</p>');
				}
				busy = false;
			}
		},
		error: function (_6){
			$("#remain").val("0");
		}
	});
}

/* Function for message for new questions in stream */
function new_stream(type){
	$("html, body").animate({ scrollTop: 0 }, "slow" ,function(){
		$('#stream').html('');
		$(".loadingstream").css("display","block");
		location.reload();
	});
}

/* Function for getting answers on question page */
function get_answers(q_id){
	id = $("#last_value").val();
	if('#comments'){
		id = 0;
	}
	busy = false;
	$.ajax({
		url: 'ajax/get_answers',
		async: true,
		type: "POST",
		data: "ajax=1&q_id="+q_id+"&last_id="+id,
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			if(_00.response!=''){
				$("#comments").html(_00.response);
			}
			else {
				$("#comments").html('<center><small>' + get_text(477) + '</small></center>');
			}
			$(".loadingstream").css("display","none");
			$(".img").imgLiquid();
			
			$('.image-link').magnificPopup({type:'image',removalDelay: 300, mainClass: 'mfp-fade'});
			$("#last_value").val(_00.last_id);
			var remain = $("#remain").val(_00.remain);
			if(_00.remain>0){
				$(window).scroll(function(){
					if($(window).scrollTop()+$(window).height() > $("#comments").height() && !busy){
						busy = true;
						var id = $("#last_value").val();
						var remain = $("#remain").val();
						if(remain>0){
							answers_load(q_id,id);
						}
					}
				});
			}
		}
	});
}

function answers_load(q_id, last_id){						
	$(".loadingstream").css("display","block");
	$.ajax({
		url: 'ajax/get_answers',
		async: true,
		type: "POST",
		data: "ajax=1&q_id="+q_id+"&last_id="+last_id,
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			$(".loadingstream").css("display","none");
			if(_00.response!=""){
				$(".loadingstream").css("display","none");
				$("#comments").append(_00.response);
				$(".img").imgLiquid();
				
				$('.image-link').magnificPopup({type:'image',removalDelay: 300, mainClass: 'mfp-fade'});
				$("#last_value").val(_00.last_id);
				$("#remain").val(_00.remain);
				busy = false;
			}
		},
		error: function (_6){
			$("#remain").val("0");
		}
	});
}

function category_questions(c_id){
	id = $("#last_value").val();
	busy = false;
	$.ajax({
		url: 'ajax/get_category_questions',
		async: true,
		type: "POST",
		data: "ajax=1&c_id="+c_id+"&last_id="+id,
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			$("#questions").html(_00.response);
			$(".loadingstream").css("display","none");
			$(".img").imgLiquid();
			$('.image-link').magnificPopup({
				type:'image',
				removalDelay: 300,
				mainClass: 'mfp-fade'
			});
			$("#last_value").val(_00.last_id);
			var remain = $("#remain").val(_00.remain);
			if(_00.remain>0){
				$(window).scroll(function(){
					if($(window).scrollTop()+$(window).height() > $("#questions").height() && !busy){
						busy = true;
						var id = $("#last_value").val();
						var remain = $("#remain").val();
						if(remain>0){
							category_questions_load(c_id,id);
						}
						if(_00.remain<1){
							$('#questions').append('<p class="end_stream">'+get_text(472)+'</p>');
						}
					}
				});
			}
			else {
				$('#questions').append('<p class="end_stream">'+get_text(472)+'</p>');
			}
		}
	});
}

function category_questions_load(c_id, last_id){						
	$(".loadingstream").css("display","block");
	$.ajax({
		url: 'ajax/get_category_questions',
		async: true,
		type: "POST",
		data: "ajax=1&c_id="+c_id+"&last_id="+last_id,
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			$(".loadingstream").css("display","none");
			if(_00.response!=""){
				$(".loadingstream").css("display","none");
				$('#questions').append(_00.response);
				$(".img").imgLiquid();
				$('.image-link').magnificPopup({type:'image',removalDelay: 300, mainClass: 'mfp-fade'});
				$("#last_value").val(_00.last_id);
				$("#remain").val(_00.remain);
				if(_00.remain<1){
					$('#questions').append('<p class="end_stream">'+get_text(472)+'</p>');
				}
				busy = false;
			}
		},
		error: function (_6){
			$("#remain").val("0");
		}
	});
}

function users_questions(user_id){
	id = $("#last_value").val();
	busy = false;
	$.ajax({
		url: 'ajax/get_users_questions',
		async: true,
		type: "POST",
		data: "ajax=1&user_id="+user_id+"&last_id="+id,
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			$("#questions").html(_00.response);
			$(".loadingstream").css("display","none");
			$(".img").imgLiquid();	
			$('.image-link').magnificPopup({type:'image',removalDelay: 300, mainClass: 'mfp-fade'});
			$("#last_value").val(_00.last_id);
			var remain = $("#remain").val(_00.remain);
			if(_00.remain>0){
				$(window).scroll(function(){
					if($(window).scrollTop()+$(window).height() > $("#questions").height() && !busy){
						busy = true;
						var id = $("#last_value").val();
						var remain = $("#remain").val();
						if(remain>0){
							users_questions_load(user_id,id);
						}
						if(_00.remain<1){
							$('#questions').append('<p class="end_stream">'+get_text(472)+'</p>');
						}
					}
				});
			}
			else {
				$('#questions').append('<p class="end_stream">'+get_text(472)+'</p>');
			}
		}
	});
}

function users_questions_load(user_id, last_id){						
	$(".loadingstream").css("display","block");
	$.ajax({
		url: 'ajax/get_users_questions',
		async: true,
		type: "POST",
		data: "ajax=1&user_id="+user_id+"&last_id="+last_id,
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			$(".loadingstream").css("display","none");
			if(_00.response!=""){
				$(".loadingstream").css("display","none");
				$('#questions').append(_00.response);
				$(".img").imgLiquid();
				$('.image-link').magnificPopup({type:'image',removalDelay: 300, mainClass: 'mfp-fade'});
				$("#last_value").val(_00.last_id);
				$("#remain").val(_00.remain);
				if(_00.remain<1){
					$('#questions').append('<p class="end_stream">'+get_text(472)+'</p>');
				}
				busy = false;
			}
		},
		error: function (_6){
			$("#remain").val("0");
		}
	});
}

function users_answers(user_id){
	id = $("#last_value").val();
	busy = false;
	$.ajax({
		url: 'ajax/get_users_answers',
		async: true,
		type: "POST",
		data: "ajax=1&user_id="+user_id+"&last_id="+id,
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			$("#questions").html(_00.response);
			$(".loadingstream").css("display","none");
			$(".img").imgLiquid();
			$('.image-link').magnificPopup({
				type:'image',
				removalDelay: 300,
				mainClass: 'mfp-fade'
			});
			$("#last_value").val(_00.last_id);
			var remain = $("#remain").val(_00.remain);
			if(_00.remain>0){
				$(window).scroll(function(){
					if($(window).scrollTop()+$(window).height() > $("#questions").height() && !busy){
						busy = true;
						var id = $("#last_value").val();
						var remain = $("#remain").val();
						if(remain>0){
							users_answers_load(user_id,id);
						}
						if(_00.remain<1){
							$('#questions').append('<p class="end_stream">'+get_text(472)+'</p>');
						}
					}
				});
			}
			else {
				$('#questions').append('<p class="end_stream">'+get_text(472)+'</p>');
			}
		}
	});
}

function users_answers_load(user_id, last_id){						
	$(".loadingstream").css("display","block");
	$.ajax({
		url: 'ajax/get_users_answers',
		async: true,
		type: "POST",
		data: "ajax=1&user_id="+user_id+"&last_id="+last_id,
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			$(".loadingstream").css("display","none");
			if(_00.response!=""){
				$(".loadingstream").css("display","none");
				$('#questions').append(_00.response);
				$(".img").imgLiquid();
				$('.image-link').magnificPopup({
					type:'image',
					removalDelay: 300,
					mainClass: 'mfp-fade'
				});
				$("#last_value").val(_00.last_id);
				$("#remain").val(_00.remain);
				if(_00.remain<1){
					$('#questions').append('<p class="end_stream">'+get_text(472)+'</p>');
				}
				busy = false;
			}
		},
		error: function (_6){
			$("#remain").val("0");
		}
	});
}

/* Function for setting verfied badge */
function verified(username,type){
	if(type==0 || type==1){
		var id = $("."+username+"").val();
		$.ajax({
			url: 'ajax/sticker',
			async: true,
			type: "POST",
			data: "ajax=1&user="+username+"&verified="+type
		});
	}
	else {
		alert("Error. Value should be 0 for disabling or 1 for enabling!");
	}
}

function ins2pos(str, id){
   var TextArea = document.getElementById(id);
   var val = TextArea.value;
   var before = val.substring(0, TextArea.selectionStart);
   var after = val.substring(TextArea.selectionEnd, val.length);
   TextArea.value = before + str + after;
   setCursor(TextArea, before.length + str.length);
}

function setCursor(elem, pos){
   if(elem.setSelectionRange){
      elem.focus();
      elem.setSelectionRange(pos, pos);
   } else if(elem.createTextRange){
      var range = elem.createTextRange();
      range.collapse(true);
      range.moveEnd('character', pos);
      range.moveStart('character', pos);
      range.select();
   }
}

/* Function for loading leaderboard */
function toppeople(){
	var loader = '<center><i class="fa fa-spinner fa-spin fa-3x"></i></center>';
	$("#topcontainer").html(loader);
	$.ajax({
		url: 'ajax/toppeople',
		async: true,
		type: "POST",
		data: "ajax=1",
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			$("#topcontainer").html(_00.response);
			$(".img").imgLiquid();
		}
	});
}

/* Function for follow topic */
function follow(_0){
	$.ajax({
		url: 'ajax/follow',
		async: true,
		type: "POST",
		data: "id="+_0
    });
    $('#follow-btn-'+_0).removeClass('follow_b').addClass('unfollow_b');
    $('#follow-btn-'+_0+' .ff').css("display","none");
    $('#follow-btn-'+_0+' .uu').css("display","inline-block");
    var number = $('#follow-btn-'+_0+' .count').html();
    if(parseInt(number)<100){
    	number++;
    	$('#follow-btn-'+_0+' .count').html(number);
    }
    $('#follow-btn-'+_0).attr("onclick","unfollow("+_0+")");
    return false;
}

/* Function for unfollow topic */
function unfollow(_0){
	$.ajax({
		url: 'ajax/unfollow',
		async: true,
		type: "POST",
		data: "id="+_0
    });
    $('#follow-btn-'+_0).removeClass('unfollow_b').addClass('follow_b');
    $('#follow-btn-'+_0+' .uu').css("display","none");
    $('#follow-btn-'+_0+' .ff').css("display","inline-block");
    var number = $('#follow-btn-'+_0+' .count').html();
    if(parseInt(number)<100){
    	number--;
    	$('#follow-btn-'+_0+' .count').html(number);
    }
    $('#follow-btn-'+_0).attr("onclick","follow("+_0+")");
    return false;
}

function toggleDiv(divId){
	$("#"+divId).toggle();
}

/* Function for selecting categories after registration */
function selectCategory(_0){
	var number = parseInt($("#number").val());
	if($("#category_"+_0).hasClass("following")){
		$("#category_"+_0).removeClass("following");
		$("#categories").removeFromArray(_0);
		$("#number").val(number-1);
		number--;
	}
	else {
		$("#category_"+_0).addClass("following");
		$("#categories").addToArray(_0);
		$("#number").val(number+1);
		number++;
	}
	if($("#number").val()<5){
		$(".enough").addClass("none");
		$(".needed").removeClass("none");
		var remain = 5 - number;
		$("#counter").html(remain);
		$("#sbmt").addClass("disabled");
	}
	else {
		$(".needed").addClass("none");
		$(".enough").removeClass("none");
		$("#sbmt").removeClass("disabled");
	}
	return false;
}

/* 2-nd step at asking question */
function next_ask_step(){
	var _0 = $('#add_question').val();
	if(_0.slice(-1)!='?'){
    	$(".add_q_error").html(get_text(478));
        return false;
    }
    if(_0.length>250){
    	$(".add_q_error").html(get_text(479));
        return false;
    }
    $('.loadingask').removeClass('none');
	$('#add_question').prop("disabled", true).addClass("disabled-ask");
	$('#question-data').val(_0);
	$('#add_description').prop("disabled", true).addClass("disabled-ask");
	var _1 = $('#add_description').val();
	$('.add_image').addClass('none');
	$('#description-data').val(_1);
	if(_1.length>2000){
    	$(".add_q_error").html(get_text(480));
        return false;
    }
	if(_0.length>8){
		$.ajax({
			url: 'ajax/checkquestion',
			async: true,
			type: "POST",
			data: "ajax=1&question="+_0,
			success: function(_2){
				var _01 = JSON.stringify(_2);
				var _00 = JSON.parse(_01);
				if(_00.response!=''){
					$('.ask-step-1-title').addClass('none');
					$('.ask-step-2-title').removeClass('none');
					$('.ask-step-2-question').html(_0);
					$('.loadingask').addClass('none');
					$('.content_row').addClass('none');
					$('.content_last').html(_00.response);
					$('.question-details').removeClass('none');
					$('#step_1').addClass('none');
					$('#step_2').removeClass('none');
					_0 = _0.replace('?', '');
					_0 = _0.replace(',', '');
					_0 = _0.replace("'", '');
					var split=_0.split(" ");
					for(var i=0; i<split.length; i++){
						$(".content_last .question-ask-box a").highlight(split[i], "highlight");
					}
				}
				else {
					last_ask_step();
				}
			}
		});
	}
}

/* Function which show hidden question description and image input */
function add_question_description(){
	$('.add_desc_link').addClass('none');
	$('#add_description').removeClass('none');
	$('#numd').removeClass('none');
	$('.add_image').removeClass('none');
}

/* Last step at asking question */
function last_ask_step(){
	var _1 = $('#question-data').val();
	$('.ask-step-3-question').html(_1);
	select_category();
	if($("#set_category").val()){
		var _1 = _1+' '+$("#set_category").val();
	}
	$('.ask-step-1-title').addClass('none');
	$('.ask-step-2-title').addClass('none');
	$('.ask-step-3-title').removeClass('none');
	$('.content_row').addClass('none');
	$('.content_last').html(get_text(481));
	$('.loadingask').addClass('none');
	$('.question-details').addClass('none');
	$('#step_1').addClass('none');
	$('#step_2').addClass('none');
	$('#step_3').removeClass('none');
	$('.question-categories').removeClass('none');
	$('#step_3').prop("disabled", false);
}

/* Checking for categories at adding question */
function check_for_categories(){
	if($('#askModal input[type=checkbox]:checked').length!=0){
		$("#askForm").submit();
	}
	else {
		$(".error_cat").html(get_text(473));
		return false;
	}
}

/* Checking categories before save editing question */
function check_for_categories_e(){
	if($('#editqModal input[type=checkbox]:checked').length!=0){
		$("#editqModal .btn-primary").addClass('disabled');
		$("#editqModal .fa-abtn").removeClass('none');
		$("#editqForm").submit();
	}
	else {
		$(".error_cat_e").html(get_text(473));
		return false;
	}
}

/* Function for searching category at asking question*/
function search_category(){
    var query_value = $('input#search_category').val();
    if(query_value!==''){
        $.ajax({
            type: "POST",
            url: "ajax/search_category",
            data: { query: query_value },
            cache: false,
            success: function(html){
                $(".results").html(html);
                $('.loadingask').addClass('none');
            }
        });
    }
    return false;
}

/* Function which add category from select box to categories list on asking question */
function select_category(){
    $.ajax({
        type: "POST",
        url: "ajax/select_category",
        cache: false,
        success: function(html){
            $(".results").html(html);
            $("#results").select2();
        }
    });
    return false;
}

/* Function for getting notifications */
function notify(){
    $.ajax({
	    type: "POST",
	    url: "ajax/notify",
	    success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			if(_00.response>0){
				if($('.notification_counter').hasClass("none")){
		        	$('.notification_counter').removeClass('none');
		        }
		        $('.notification_counter').html(_00.response);
		        notification_title();
		    }
		    else {
		    	$('.notification_counter').addClass('none');
		    }
	    }
	});
	window.setInterval(function(){
		$.ajax({
		    type: "POST",
		    url: "ajax/notify",
		    success: function(_2){
				var _01 = JSON.stringify(_2);
				var _00 = JSON.parse(_01);
				if(_00.response>0){
					if($('.notification_counter').hasClass("none")){
			        	$('.notification_counter').removeClass('none');
			        }
			        $('.notification_counter').html(_00.response);
			    }
			    else {
			    	$('.notification_counter').addClass('none');
			    }
		    }
		});
	}, 5000);
}

function add_category_to_question(id, name, followers){
	if(!$('#cat-'+id).length){
		$('input#search_category').val('');
		$(".results").html('');
		$('.content_last').append("<div class='question-category-box'><label><input type='checkbox' id='cat-"+id+"' name='categories[]' value='"+id+"' checked='yes'><span>"+name+" - "+followers+" "+get_text(118)+"</span></label></div>");
	}
}

function add_category_to_question_select(id, name, followers){
	if(id!=''){
		$("#results option[value='"+id+"']").remove();
		if(!$('#cat-'+id).length){
			//$("#results").val('');
			$('.content_last').prepend("<div class='question-category-box'><label><input type='checkbox' id='cat-"+id+"' name='categories[]' value='"+id+"' checked='yes'><span>"+name+" - "+followers+" "+get_text(118)+"</span></label></div>");
		}
	}
}

function add_category_to_question_select_p(id, name, followers){
	if(id!=''){
		$("#results option[value='"+id+"']").remove();
		if(!$('#cat-'+id).length){
			$('.content_last_p').prepend("<div class='question-category-box'><label><input type='checkbox' id='cat-"+id+"' name='categories[]' value='"+id+"' checked='yes'><span>"+name+" - "+followers+" "+get_text(118)+"</span></label></div>");
		}
	}
}

/* Function for opening mention box in answer box */
function open_mention_box(){
	if($('.mention_box').hasClass("none")){
		$('.mention_box').removeClass("none");
		$('.add_mention').addClass("add_mention_opened");
		$('#mention_username').focus();
		$(window).keydown(function(event){
		    if(event.keyCode == 13 && $('.add_mention').hasClass("add_mention_opened")){
		    	save_mention();
		      	return false;
		    }
		});
	}
	else {
		$('.mention_box').addClass("none");
		$('.add_mention').removeClass("add_mention_opened");
	}
}

/* Function for opening mention box in edit answer modal */
function open_mention_box_p(){
	if($('.mention_box_p').hasClass("none")){
		$('.mention_box_p').removeClass("none");
		$('.add_mention_p').addClass("add_mention_opened");
		$('#mention_username_p').focus();
		$(window).keydown(function(event){
		    if(event.keyCode == 13 && $('.add_mention_p').hasClass("add_mention_opened")){
		    	save_mention();
		      	return false;
		    }
		});
	}
	else {
		$('.mention_box_p').addClass("none");
		$('.add_mention_p').removeClass("add_mention_opened");
	}
}

/* Function for adding mention in answer box */
function save_mention(){
	var username = $('#mention_username').val();
	if(username!=''){
		ins2pos(' @'+username+' ', 'answer_text');
	}
	$('.mention_box').addClass("none");
	$('.add_mention').removeClass("add_mention_opened");
	$('#mention_username').val('');
}

/* Function for adding mention in edit answer modal */
function save_mention_p(){
	var username = $('#mention_username_p').val();
	if(username!=''){
		ins2pos(' @'+username+' ', 'answer_text_p');
	}
	$('.mention_box_p').addClass("none");
	$('.add_mention_p').removeClass("add_mention_opened");
	$('#mention_username_p').val('');
}

/* Function for deleting question from stream page */
function q_delete(id){
	if(id!=''){
		if(confirm(get_text(482))){
			$.ajax({
	            type: "POST",
	            url: "ajax/q_delete",
	            data: { id: id },
	            cache: false,
	            success: function(html){
	            	$("#qq_"+id).fadeOut();
	            }
	        });
	    }
	}
}

/* Function for deleting question from question page */
function q_delete_r(id){
	if(id!=''){
		if(confirm(get_text(483))){
			$.ajax({
	            type: "POST",
	            url: "ajax/q_delete",
	            data: { id: id },
	            cache: false,
	            success: function(html){
	            	alert(get_text(484));
	            	window.location.replace('site/stream');
	            }
	        });
	    }
	}
}

/* Function for deleting answer */
function a_delete(id){
	if(id!=''){
		if(confirm(get_text(483))){
			$.ajax({
	            type: "POST",
	            url: "ajax/a_delete",
	            data: { id: id },
	            success: function(html){
	            	$("#aa_"+id).fadeOut();
	            }
	        });
	    }
	}
}

/* Send report function from modal */
function send_report(){
	var r_id = $('#r_id').val();
	var r_type = $('#r_type').val();
	var r_reason = $('#r_reason').val();
	if(r_id!='' && r_type!='' && r_reason!=''){
		$.ajax({
	        type: "POST",
	        url: "ajax/report",
	        data: { r_type: r_type, r_id: r_id, r_reason: r_reason }
	    });
	    $('#reportModal .btn-primary').addClass('none');
	    $('#reportModal .sent').addClass('none');
	    $('#reportModal .success').removeClass('none');
	}
}

/* Function for saving edited answer */
function edit_answer_save(){
	$("#editaModal .btn-primary").addClass('disabled');
	$("#editaModal .fa-abtn").removeClass('none');
	var e_id = $('#answer_id').val();
	var e_answer = tinyMCE.get('answer_text_p').getContent();
	if(e_answer!=''){
		$.ajax({
	        type: "POST",
	        url: "ajax/edit_answer",
	        data: { id: e_id, answer: e_answer },
	        success: function(_2){
				var _01 = JSON.stringify(_2);
				var _00 = JSON.parse(_01);
	        	$('#aa_'+e_id+' .comment').html(_00.response);
	          	$('#db_a_'+e_id).val(e_answer);
	          	$("#editaModal .btn-primary").removeClass('disabled');
				$("#editaModal .fa-abtn").addClass('none');
				$('#editaModal').modal('hide');
	        }
	    });
	}
}

/* Change image for edit question modal */
function edit_question_image(){
	$('.q_p_t').fadeOut(function(){
		$('.add_image_p').removeClass('none');
	});
}

/* Function for getting all notifications on notification page */
function get_notifications(type){
	id = $("#last_value").val();
	busy = false;
	$.ajax({
		url: 'ajax/get_notifications',
		async: true,
		type: "POST",
		data: "ajax=1&type="+type+"&last_id="+id,
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			$("#notifications").html(_00.response);
			$(".loadingstream").css("display","none");
			$(".img").imgLiquid();
			$('.image-link').magnificPopup({type:'image',removalDelay: 300, mainClass: 'mfp-fade'});
			$("#last_value").val(_00.last_id);
			var remain = $("#remain").val(_00.remain);
			if(_00.remain>0){
				$(window).scroll(function(){
					if($(window).scrollTop()+$(window).height() > $("#notifications").height() && !busy){
						busy = true;
						var id = $("#last_value").val();
						var remain = $("#remain").val();
						if(remain>0){
							get_notifications_load(type,id);
						}
						if(_00.remain<1){
							$('#notifications').append('<center>' + get_text(487) + '</center>');
						}
					}
				});
			}
			else {
				$('#stream').append('<center>' + get_text(487) + '</center>');
			}
		}
	});
}

/* Function for loading more notification from via ajax */
function get_notifications_load(type, last_id){						
	$(".loadingstream").css("display","block");
	$.ajax({
		url: 'ajax/get_notifications',
		async: true,
		type: "POST",
		data: "ajax=1&type="+type+"&last_id="+last_id,
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			$(".loadingstream").css("display","none");
			if(_00.response!=""){
				$(".loadingstream").css("display","none");
				$('#notifications').append(_00.response);
				$(".img").imgLiquid();
				$('.image-link').magnificPopup({type:'image',removalDelay: 300, mainClass: 'mfp-fade'});
				$("#last_value").val(_00.last_id);
				$("#remain").val(_00.remain);
				if(_00.remain<1){
					$('#notifications').append('<center>' + get_text(487) + '</center>');
				}
				busy = false;
			}
		},
		error: function (_6){
			$("#remain").val("0");
		}
	});
}

/* Function for marking notifications read */
function readed_notifications(type){
	if(type!=''){
		$.ajax({
			url: 'ajax/readed_notifications',
			async: true,
			type: "POST",
			data: "ajax=1&type="+type,
			success: function(_2){
				var _01 = JSON.stringify(_2);
				var _00 = JSON.parse(_01);
			}
		});
		$(".Notif").removeClass('new').addClass('seen');
		$('.notification_counter').html('');
		$('.notification_counter').addClass('none');
	}
}

/* Function for loading points information in user profile */
function get_points(user_id){
	id = $("#last_value").val();
	busy = false;
	$.ajax({
		url: 'ajax/get_points',
		async: true,
		type: "POST",
		data: "ajax=1&user_id="+user_id+"&last_id="+id,
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			$("#points").html(_00.response);
			$(".loadingstream").css("display","none");
			$("#last_value").val(_00.last_id);
			var remain = $("#remain").val(_00.remain);
			if(_00.remain>0){
				$(window).scroll(function(){
					if($(window).scrollTop()+$(window).height() > $("#points").height() && !busy){
						busy = true;
						var id = $("#last_value").val();
						var remain = $("#remain").val();
						if(remain>0){
							get_points_load(user_id,id);
						}
						if(_00.remain<1){
							$('#points').append('<center>' + get_text(488) + '</center>');
						}
					}
				});
			}
			else {
				$('#points').append('<center>No more points</center>');
			}
		}
	});
}

function get_points_load(user_id, last_id){						
	$(".loadingstream").css("display","block");
	$.ajax({
		url: 'ajax/get_points',
		async: true,
		type: "POST",
		data: "ajax=1&user_id="+user_id+"&last_id="+last_id,
		success: function(_2){
			var _01 = JSON.stringify(_2);
			var _00 = JSON.parse(_01);
			$(".loadingstream").css("display","none");
			if(_00.response!=""){
				$(".loadingstream").css("display","none");
				$('#points').append(_00.response);
				$("#last_value").val(_00.last_id);
				$("#remain").val(_00.remain);
				if(_00.remain<1){
					$('#points').append('<center>' + get_text(488) + '</center>');
				}
				busy = false;
			}
		},
		error: function (_6){
			$("#remain").val("0");
		}
	});
}

function expand_topic(){
	$("#new_topic").css("height","auto");
}

function upload_new_image_for_topic(){
	$("#link_to_new_topic_image").addClass('none');
	$("#new_topic_image").removeClass('none');
}

function upload_new_image_for_question(){
	$("#link_to_new_question_image").addClass('none');
	$("#new_question_image").removeClass('none');
}

function show_page_content(id){
	if(id!=''){
		var page = '';
		if(id==1){
			page = 'about_us';
		}
		if(id==2){
			page = 'points';
		}
		if(id==3){
			page = 'privacy';
		}
		if(id==4){
			page = 'terms';
		}
		
		if($("#"+page+"_block").hasClass(page+'_block_opened')){
			$("#"+page+"_block").removeClass(page+'_block_opened');
		}
		else {
			$("#"+page+"_block").addClass(page+'_block_opened');
		}
	}
}