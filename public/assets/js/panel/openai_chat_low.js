const streamUrl = $( 'meta[name=stream-url]' ).attr( 'content' );

$( document ).ready( function () {
	"use strict";
	updateChatButtons()
	$( ".chats-container" ).stop().animate( { scrollTop: $( ".chats-container" )[ 0 ]?.scrollHeight }, 200 );
	$( "#scrollable_content" ).stop().animate( { scrollTop: $( "#scrollable_content" ).outerHeight() }, 200 );

	$( '.chat-list-ul' ).on( 'click', 'a', function () {
		const parentLi = $( this ).parent();
		parentLi.siblings().removeClass( 'active' );
		parentLi.addClass( 'active' );
	} );

	function saveChatNewTitle( chatId, newTitle ) {

		var formData = new FormData();
		formData.append( 'chat_id', chatId );
		formData.append( 'title', newTitle );

		$.ajax( {
			type: "post",
			url: "/dashboard/user/openai/chat/rename-chat",
			data: formData,
			contentType: false,
			processData: false,
		} );
		return false;

	}

	function deleteChatItem( chatId, chatTitle ) {
		if ( confirm( `Are you sure you want to remove ${ chatTitle }?` ) ) {
			var formData = new FormData();
			formData.append( 'chat_id', chatId );

			$.ajax( {
				type: "post",
				url: "/dashboard/user/openai/chat/delete-chat",
				data: formData,
				contentType: false,
				processData: false,
				success: function ( data ) {
					//Remove chat li
					$( "#" + chatId ).hide();
					$( "#chat_area_to_hide" ).hide();
				},
				error: function ( data ) {
					var err = data.responseJSON.errors;
					if ( err ) {
						$.each( err, function ( index, value ) {
							toastr.error( value );
						} );
					} else {
						toastr.error( data.responseJSON.message );
					}
				},
			} );
		}

	}

	$( '.chat-list-ul' ).on( 'click', '.chat-item-delete', ev => {
		const button = ev.currentTarget;
		const parent = button.closest( 'li' );
		const chatId = parent.getAttribute( 'id' );
		const chatTitle = parent.querySelector( '.chat-item-title' ).innerText;
		deleteChatItem( chatId, chatTitle );
	} );

	$( '.chat-list-ul' ).on( 'click', '.chat-item-update-title', ev => {
		const button = ev.currentTarget;
		const parent = button.closest( '.chat-list-item' );
		const title = parent.querySelector( '.chat-item-title' );
		const chatId = parent.getAttribute( 'id' );
		const currentText = title.innerText;

		function setEditMode( mode ) {

			if ( mode === 'editStart' ) {
				parent.classList.add( 'edit-mode' );

				title.setAttribute( 'data-current-text', currentText );
				title.setAttribute( 'contentEditable', true );
				title.focus();
				window.getSelection().selectAllChildren( title );
			} else if ( mode === 'editEnd' ) {
				parent.classList.remove( 'edit-mode' );

				title.removeAttribute( 'contentEditable' );
				title.removeAttribute( 'data-current-text' );
			}

		}

		function keydownHandler( ev ) {
			const { key } = ev;
			const escapePressed = key === 'Escape';
			const enterPressed = key === 'Enter';

			if ( !escapePressed && !enterPressed ) return;

			ev.preventDefault();

			if ( escapePressed ) {
				title.innerText = currentText;
			}

			if ( enterPressed ) {
				saveChatNewTitle( chatId, title.innerText );
			}

			setEditMode( 'editEnd' );
			document.removeEventListener( 'keydown', keydownHandler );
		}

		// if alreay editting then turn the edit button to a save button
		if ( title.hasAttribute( 'contentEditable' ) ) {
			setEditMode( 'editEnd' );
			document.removeEventListener( 'keydown', keydownHandler );
			return saveChatNewTitle( chatId, title.innerText );
		}

		$( '.chat-list-ul .edit-mode' ).each( ( i, el ) => {
			const title = el.querySelector( '.chat-item-title' );
			title.innerText = title.getAttribute( 'data-current-text' );
			title.removeAttribute( 'data-current-text' );
			title.removeAttribute( 'contentEditable' );
			el.classList.remove( 'edit-mode' );
		} );

		setEditMode( 'editStart' );

		document.addEventListener( 'keydown', keydownHandler );
	} );

} );

/*

DO NOT FORGET TO ADD THE CHANGES TO BOTH FUNCTION makeDocumentReadyAgain and the document ready function on the top!!!!

 */
function makeDocumentReadyAgain() {
	updateChatButtons()
	$( document ).ready( function () {
		"use strict";

		$( ".chats-container" ).stop().animate( { scrollTop: $( ".chats-container" )[ 0 ]?.scrollHeight }, 200 );
		$( "#scrollable_content" ).stop().animate( { scrollTop: $( "#scrollable_content" ).outerHeight() }, 200 );

		$( '.chat-list-ul' ).on( 'click', 'a', function () {
			const parentLi = $( this ).parent();
			parentLi.siblings().removeClass( 'active' );
			parentLi.addClass( 'active' );
		} );

		function saveChatNewTitle( chatId, newTitle ) {

			var formData = new FormData();
			formData.append( 'chat_id', chatId );
			formData.append( 'title', newTitle );

			$.ajax( {
				type: "post",
				url: "/dashboard/user/openai/chat/rename-chat",
				data: formData,
				contentType: false,
				processData: false,
			} );
			return false;

		}

		function deleteChatItem( chatId, chatTitle ) {
			if ( confirm( `Are you sure you want to remove ${ chatTitle }?` ) ) {
				var formData = new FormData();
				formData.append( 'chat_id', chatId );

				$.ajax( {
					type: "post",
					url: "/dashboard/user/openai/chat/delete-chat",
					data: formData,
					contentType: false,
					processData: false,
					success: function ( data ) {
						//Remove chat li
						$( "#" + chatId ).hide();
						$( "#chat_area_to_hide" ).hide();
					},
					error: function ( data ) {
						var err = data.responseJSON.errors;
						if ( err ) {
							$.each( err, function ( index, value ) {
								toastr.error( value );
							} );
						} else {
							toastr.error( data.responseJSON.message );
						}
					},
				} );
				return false;
			}

		}

		$( '.chat-list-ul' ).off( 'click', '.chat-item-delete' );
		$( '.chat-list-ul' ).on( 'click', '.chat-item-delete', ev => {
			const button = ev.currentTarget;
			const parent = button.closest( 'li' );
			const chatId = parent.getAttribute( 'id' );
			const chatTitle = parent.querySelector( '.chat-item-title' ).innerText;
			deleteChatItem( chatId, chatTitle );
		} );

		$( '.chat-list-ul' ).off( 'click', '.chat-item-update-title' );
		$( '.chat-list-ul' ).on( 'click', '.chat-item-update-title', ev => {
			const button = ev.currentTarget;
			const parent = button.closest( '.chat-list-item' );
			const title = parent.querySelector( '.chat-item-title' );
			const chatId = parent.getAttribute( 'id' );
			const currentText = title.innerText;

			function setEditMode( mode ) {

				if ( mode === 'editStart' ) {
					parent.classList.add( 'edit-mode' );

					title.setAttribute( 'data-current-text', currentText );
					title.setAttribute( 'contentEditable', true );
					title.focus();
					window.getSelection().selectAllChildren( title );
				} else if ( mode === 'editEnd' ) {
					parent.classList.remove( 'edit-mode' );

					title.removeAttribute( 'contentEditable' );
					title.removeAttribute( 'data-current-text' );
				}

			}

			function keydownHandler( ev ) {
				const { key } = ev;
				const escapePressed = key === 'Escape';
				const enterPressed = key === 'Enter';

				if ( !escapePressed && !enterPressed ) return;

				ev.preventDefault();

				if ( escapePressed ) {
					title.innerText = currentText;
				}

				if ( enterPressed ) {
					saveChatNewTitle( chatId, title.innerText );
				}

				setEditMode( 'editEnd' );
				document.removeEventListener( 'keydown', keydownHandler );
			}

			// if alreay editting then turn the edit button to a save button
			if ( title.hasAttribute( 'contentEditable' ) ) {
				setEditMode( 'editEnd' );
				document.removeEventListener( 'keydown', keydownHandler );
				return saveChatNewTitle( chatId, title.innerText );
			}

			$( '.chat-list-ul .edit-mode' ).each( ( i, el ) => {
				const title = el.querySelector( '.chat-item-title' );
				title.innerText = title.getAttribute( 'data-current-text' );
				title.removeAttribute( 'data-current-text' );
				title.removeAttribute( 'contentEditable' );
				el.classList.remove( 'edit-mode' );
			} );

			setEditMode( 'editStart' );

			document.addEventListener( 'keydown', keydownHandler );
		} );

	} );
}


function escapeHtml( html ) {
	var text = document.createTextNode( html );
	var div = document.createElement( 'div' );
	div.appendChild( text );
	return div.innerHTML;
}

function updateChatButtons() {
	setTimeout( function () {
		const generateBtn = document.getElementById( "send_message_button" );
		const stopBtn = document.getElementById( "stop_button" );
		const promptInput = document.getElementById( "prompt" );
		let controller = null; // Store the AbortController instance
		let scrollLocked = false;
		let nIntervId = null;
		let chunk = [];
		let streaming = true;

		const generate = async ( ev ) => {
			"use strict";
			ev?.preventDefault();
			// Alert the user if no prompt value
			const promptInputValue = promptInput.value;
			if ( !promptInputValue || promptInputValue.length === 0 || promptInputValue.replace( /\s/g, '' ) === '' ) {
				return toastr.error( 'Please fill the message field.' );
			}

			const chatsContainer = $( ".chats-container" );
			const userBubbleTemplate = document.querySelector( '#chat_user_bubble' ).content.cloneNode( true );
			const aiBubbleTemplate = document.querySelector( '#chat_ai_bubble' ).content.cloneNode( true );

			if ( generateBtn.classList.contains( 'submitting' ) ) return;

			const prompt1 = atob( guest_event_id );
			const prompt2 = atob( guest_look_id );
			const prompt3 = atob( guest_product_id );

			const chat_id = $( '#chat_id' ).val();

			const bearer = prompt1 + prompt2 + prompt3;
			// Disable the generate button and enable the stop button
			generateBtn.disabled = true;
			generateBtn.classList.add( 'submitting' );
			stopBtn.disabled = false;
			userBubbleTemplate.querySelector( '.chat-content' ).innerHTML = promptInputValue;
			promptInput.value = '';
			chatsContainer.append( userBubbleTemplate );

			// Create a new AbortController instance
			controller = new AbortController();
			const signal = controller.signal;

			let responseText = '';

			const aiBubbleWrapper = aiBubbleTemplate.firstElementChild;
			aiBubbleWrapper.classList.add( 'loading' );
			aiBubbleTemplate.querySelector( '.chat-content' ).innerHTML = responseText;
			chatsContainer.append( aiBubbleTemplate );

			chatsContainer[ 0 ].scrollTo( 0, chatsContainer[ 0 ].scrollHeight );

			messages.push( {
				role: "user",
				content: promptInputValue
			} );

			let guest_id2 = atob( guest_id );

			function onBeforePageUnload( e ) {
				e.preventDefault();
				e.returnValue = '';
			}

			function onWindowScroll() {
				if ( chatsContainer[ 0 ].scrollTop + chatsContainer[ 0 ].offsetHeight >= chatsContainer[ 0 ].scrollHeight ) {
					scrollLocked = true;
				} else {
					scrollLocked = false;
				}
			}

			// to prevent from reloading when generating respond
			window.addEventListener( 'beforeunload', onBeforePageUnload );

			chatsContainer[ 0 ].addEventListener( 'scroll', onWindowScroll );

			// started eventSource
			const prompt = document.getElementById( "prompt" ).value;
			
			chunk = [];
			streaming = true;
			nIntervId = setInterval( function () {
				if ( chunk.length == 0 && !streaming ) {
					messages.push( {
						role: "assistant",
						content: aiBubbleWrapper.querySelector( '.chat-content' ).innerHTML
					} );

					saveResponse( promptInputValue, aiBubbleWrapper.querySelector( '.chat-content' ).innerHTML, chat_id )

					generateBtn.disabled = false;
					generateBtn.classList.remove( 'submitting' );
					aiBubbleWrapper.classList.remove( 'loading' );
					stopBtn.disabled = true;
					controller = null; // Reset the AbortController instance

					jQuery( ".chats-container" ).stop().animate( { scrollTop: jQuery( ".chats-container" )[ 0 ]?.scrollHeight }, 200 );
					jQuery( "#scrollable_content" ).stop().animate( { scrollTop: jQuery( "#scrollable_content" ).outerHeight() }, 200 );

					window.removeEventListener( 'beforeunload', onBeforePageUnload );
					chatsContainer[ 0 ].removeEventListener( 'scroll', onWindowScroll );
					clearInterval( nIntervId );
				}

				const text = chunk.shift();
				if ( text ) {
					aiBubbleWrapper.classList.remove( 'loading' );
					aiBubbleWrapper.querySelector( '.chat-content' ).innerHTML += text;
					chatsContainer[ 0 ].scrollTo( 0, chatsContainer[ 0 ].scrollHeight );
				}

			}, 20 );
			
			if (stream_type == 'backend') {
				const eventSource = new EventSource( `${ streamUrl }/?message=${ promptInputValue }&category=${category.id}` );

				eventSource.addEventListener( 'data', function ( event ) {
					const data = JSON.parse( event.data );
					if ( data.message !== null )
						chunk.push( data.message);
				} );
	
				// finished eventSource
				eventSource.addEventListener( 'stop', function ( event ) {
					streaming = false;
					eventSource.close();
				} );
			} else {
				try {
					// console.log(messages);
					// Fetch the response from the OpenAI API with the signal from AbortController
					const response = await fetch(guest_id2, {
						method: "POST",
						headers: {
							"Content-Type": "application/json",
							Authorization: `Bearer ${bearer}`,
						},
						body: JSON.stringify({
							model: "gpt-3.5-turbo-16k",
							messages: messages,
							max_tokens: 2000,
							stream: true, // For streaming responses
						}),
						signal, // Pass the signal to the fetch request
					});
					
					if(response.status != 200) {
						throw response;
					}
					// Read the response as a stream of data
					const reader = response.body.getReader();
					const decoder = new TextDecoder("utf-8");
					let result = '';

					while (true) {
						// if ( window.console || window.console.firebug ) {
						// 	console.clear();
						// }
						const { done, value } = await reader.read();
						if (done) {
							streaming = false;
							break;
						}
						// Massage and parse the chunk of data
						const chunk1 = decoder.decode(value);
						const lines = chunk1.split("\n");
				
						const parsedLines = lines
							.map((line) => line.replace(/^data: /, "").trim()) // Remove the "data: " prefix
							.filter((line) => line !== "" && line !== "[DONE]") // Remove empty lines and "[DONE]"
							.map((line) => {
								try {
									return JSON.parse(line);
								} catch (ex) {
									console.log(line);
								}
								return null;
							}); // Parse the JSON string
						
						for (const parsedLine of parsedLines) {
							if (!parsedLine) continue;
							const { choices } = parsedLine;
							const { delta } = choices[0];
							const { content } = delta;
							// const { finish_reason } = choices[0];

							if (content) {
								chunk.push(content);
							}
						}
					}

				} catch ( error ) {
					// Handle fetch request errors
					console.log("Error:", error);
					if (signal.aborted) {
						aiBubbleWrapper.querySelector('.chat-content').innerHTML = "Request aborted by user. Not saved.";
						generateBtn.disabled = false;
						generateBtn.classList.remove('submitting');
						document.getElementById("chat_form").reset();
					} else {
						console.log("Error:", error);
						switch(error.status) {
							case 429:
								aiBubbleWrapper.querySelector('.chat-content').innerHTML = "Api Connection Error. You hit the rate limites of openai requests. Please check your Openai API Key.";
								break;
							default:
								aiBubbleWrapper.querySelector('.chat-content').innerHTML = "Api Connection Error. Please contact system administrator via Support Ticket. Error is: API Connection failed due to API keys.";
						}
						generateBtn.disabled = false;
						generateBtn.classList.remove('submitting');
						document.getElementById("chat_form").reset();
						streaming = false
					}
				}
			}
		};

		const stop = () => {
			// Abort the fetch request by calling abort() on the AbortController instance
			if ( controller ) {
				controller.abort();
				controller = null;
				chunk = [];
				streaming = false;
			}
		};
		promptInput.addEventListener( 'keypress', ev => {
			if ( ev.keyCode == 13 ) {
				ev.preventDefault();
				return generate();
			}
		} );

		generateBtn.addEventListener( "click", generate );
		stopBtn.addEventListener( "click", stop );

	}, 100 );

}


function openChatAreaContainer( chat_id ) {
	"use strict";

	var formData = new FormData();
	formData.append( 'chat_id', chat_id );

	$.ajax( {
		type: "post",
		url: "/dashboard/user/openai/chat/open-chat-area-container",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			$( "#load_chat_area_container" ).html( data.html );
			makeDocumentReadyAgain();
			setTimeout( function () {
				$( ".chats-container" ).stop().animate( { scrollTop: $( ".chats-container" )[ 0 ].scrollHeight }, 200 );
			}, 750 );
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			if ( err ) {
				$.each( err, function ( index, value ) {
					toastr.error( value );
				} );
			} else {
				toastr.error( data.responseJSON.message );
			}
		},
	} );
	return false;
}

function startNewChat( category_id ) {
	"use strict";
	var formData = new FormData();
	formData.append( 'category_id', category_id );

	$.ajax( {
		type: "post",
		url: "/dashboard/user/openai/chat/start-new-chat",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			$( "#load_chat_area_container" ).html( data.html );
			$( "#chat_sidebar_container" ).html( data.html2 );
			makeDocumentReadyAgain();

			setTimeout( function () {
				$( ".chats-container" ).stop().animate( { scrollTop: $( ".chats-container" ).outerHeight() }, 200 );
			}, 750 );

		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			if ( err ) {
				$.each( err, function ( index, value ) {
					toastr.error( value );
				} );
			} else {
				toastr.error( data.responseJSON.message );
			}
		},
	} );
	return false;
}



$( document ).ready( function () {
	$( "#chat_search_word" ).on( 'keyup', function () {
		return searchChatFunction();
	} );
} );


function searchChatFunction() {
	"use strict";

	const categoryId = $( '#chat_search_word' ).data( "category-id" );
	const formData = new FormData();
	formData.append( '_token', document.querySelector( "input[name=_token]" )?.value );
	formData.append( 'search_word', document.getElementById( 'chat_search_word' ).value );
	formData.append( 'category_id', categoryId );

	$.ajax( {
		type: "POST",
		url: '/dashboard/user/openai/chat/search',
		data: formData,
		contentType: false,
		processData: false,
		success: function ( result ) {
			$( "#chat_sidebar_container" ).html( result.html );
			$( document ).trigger( 'ready' );
		}
	} );
}


