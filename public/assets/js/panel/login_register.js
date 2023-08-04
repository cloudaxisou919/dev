( () => {
	"use strict";

	const showPassBtn = document.querySelector( '.show-password' );
	showPassBtn?.addEventListener( 'click', ev => {
		ev.preventDefault();
		const passField = document.querySelector( '#password' );
		const currentPassFieldType = passField.getAttribute( 'type' );
		passField.setAttribute( 'type', currentPassFieldType === 'password' ? 'text' : 'password' );
	} )
} )();


//LOGIN
function LoginForm() {
	"use strict";

	document.getElementById( "LoginFormButton" ).disabled = true;
	document.getElementById( "LoginFormButton" ).innerHTML = magicai_localize.please_wait;
	document.querySelector( '#app-loading-indicator' )?.classList?.remove( 'opacity-0' );

	var email = $( "#email" ).val();
	if ( email == "" ) {
		toastr.error( magicai_localize.missing_email);
		document.getElementById( "LoginFormButton" ).disabled = false;
		document.getElementById( "LoginFormButton" ).innerHTML = magicai_localize.sign_in;
		document.querySelector( '#app-loading-indicator' )?.classList?.add( 'opacity-0' );
		return false;
	}
	var password = $( "#password" ).val();
	if ( password == "" ) {
		toastr.error( magicai_localize.missing_password );
		document.getElementById( "LoginFormButton" ).disabled = false;
		document.getElementById( "LoginFormButton" ).innerHTML = magicai_localize.sign_in;
		document.querySelector( '#app-loading-indicator' )?.classList?.add( 'opacity-0' );
		return false;
	}

	var formData = new FormData();
	formData.append( 'email', $( "#email" ).val() );
	formData.append( 'password', $( "#password" ).val() );
	// Ajax Post
	$.ajax( {
		type: "post",
		url: "/login",
		data: formData,
		contentType: false,
		processData: false,
        cache: false,
		success: function ( data ) {
			toastr.success( magicai_localize.login_redirect );
			setTimeout( function () {
				location.reload();
				document.querySelector( '#app-loading-indicator' )?.classList?.add( 'opacity-0' );
			}, 200 );
		},
		error: function ( data ) {
			if ( data.responseJSON.errors ) {
				var err = data.responseJSON.errors;
				$.each( err, function ( index, value ) {
					toastr.error( value );
				} );
			} else if ( data.responseJSON.message ) {
				toastr.error( data.responseJSON.message );
			}
			document.getElementById( "LoginFormButton" ).disabled = false;
			document.getElementById( "LoginFormButton" ).innerHTML = magicai_localize.sign_in;
			document.querySelector( '#app-loading-indicator' )?.classList?.add( 'opacity-0' );
		}
	} );
	return false;
}

//REGISTER
function RegisterForm() {
	"use strict";

	document.getElementById( "RegisterFormButton" ).disabled = true;
	document.getElementById( "RegisterFormButton" ).innerHTML = magicai_localize.please_wait;
	document.querySelector( '#app-loading-indicator' )?.classList?.remove( 'opacity-0' );

	var formData = new FormData();
	formData.append( 'name', $( "#name_register" ).val() );
	formData.append( 'surname', $( "#surname_register" ).val() );
	formData.append( 'password', $( "#password_register" ).val() );
	formData.append( 'password_confirmation', $( "#password_confirmation_register" ).val() );
	formData.append( 'email', $( "#email_register" ).val() );
	if ( $( '#affiliate_code' ).val() != 'undefined' ) {
		formData.append( 'affiliate_code', $( "#affiliate_code" ).val() );
	} else {
		formData.append( 'affiliate_code', null );
	}

	$.ajax( {
		type: "post",
		url: "/register",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success( magicai_localize.register_redirect );
			setTimeout( function () {
				location.reload();
				document.querySelector( '#app-loading-indicator' )?.classList?.add( 'opacity-0' );
			}, 1500 );
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
            var type = data.responseJSON.type;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );

            if (type === 'confirmation'){
                setTimeout( function () {
                    location.href = '/login';
                    document.querySelector( '#app-loading-indicator' )?.classList?.add( 'opacity-0' );
                }, 2500 );
            }else {
                document.getElementById( "RegisterFormButton" ).disabled = false;
                document.getElementById( "RegisterFormButton" ).innerHTML = magicai_localize.signup;
                document.querySelector( '#app-loading-indicator' )?.classList?.add( 'opacity-0' );
            }
		}
	} );
	return false;
}


//PASSWORD RESET
function PasswordResetMailForm() {
	"use strict";

	document.getElementById( "PasswordResetFormButton" ).disabled = true;
	document.getElementById( "PasswordResetFormButton" ).innerHTML = magicai_localize.please_wait;
	document.querySelector( '#app-loading-indicator' )?.classList?.remove( 'opacity-0' );

	var formData = new FormData();
	formData.append( 'email', $( "#password_reset_email" ).val() );

	$.ajax( {
		type: "post",
		url: "/forgot-password",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success( magicai_localize.password_reset_link );
			document.querySelector( '#app-loading-indicator' )?.classList?.add( 'opacity-0' );
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			document.getElementById( "PasswordResetFormButton" ).disabled = false;
			document.getElementById( "PasswordResetFormButton" ).innerHTML = "Send Instructions!";
			document.querySelector( '#app-loading-indicator' )?.classList?.add( 'opacity-0' );
		}
	} );
	return false;
}

function PasswordReset( password_reset_code ) {
	"use strict";

	document.getElementById( "PasswordResetFormButton" ).disabled = true;
	document.getElementById( "PasswordResetFormButton" ).innerHTML = magicai_localize.please_wait;
	document.querySelector( '#app-loading-indicator' )?.classList?.remove( 'opacity-0' );

	var formData = new FormData();
	formData.append( 'password', $( "#password_register" ).val() );
	formData.append( 'password_confirmation', $( "#password_confirmation_register" ).val() );
	formData.append( 'password_reset_code', password_reset_code );

	$.ajax( {
		type: "post",
		url: "/forgot-password/save",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success( magicai_localize.password_reset_done );
			setTimeout( function () {
				location.href = '/dashboard';
				document.querySelector( '#app-loading-indicator' )?.classList?.add( 'opacity-0' );
			}, 1250 );
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			document.getElementById( "PasswordResetFormButton" ).disabled = false;
			document.getElementById( "PasswordResetFormButton" ).innerHTML = magicai_localize.password_reset;
			document.querySelector( '#app-loading-indicator' )?.classList?.add( 'opacity-0' );
		}
	} );
	return false;
}

