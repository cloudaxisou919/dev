const generateBtn = document.getElementById( "send_message_button" );
const stopBtn = document.getElementById( "stop_button" );
const promptInput = document.getElementById( "prompt" );
let controller = null; // Store the AbortController instance
const guest_id = document.getElementById( "guest_id" ).value;
const guest_event_id = document.getElementById( "guest_event_id" ).value;
const guest_look_id = document.getElementById( "guest_look_id" ).value;
const guest_product_id = document.getElementById( "guest_product_id" ).value;
const streamUrl = $( 'meta[name=stream-url]' ).attr( 'content' );

const generate = async ( message_no, creativity, maximum_length, number_of_results, prompt ) => {
	"use strict";
	const submitBtn = document.getElementById( "openai_generator_button" );
	const typingEl = document.querySelector( '.tox-edit-area > .lqd-typing' );
	
	const chunk = [];
	let streaming = true;
	var result = '';

	const nIntervId = setInterval( function () {
		if ( chunk.length == 0 && !streaming ) {
			submitBtn.classList.remove( 'lqd-form-submitting' );
			document.querySelector( '#app-loading-indicator' )?.classList?.add( 'opacity-0' );
			document.querySelector( '#workbook_regenerate' )?.classList?.remove( 'hidden' );
			submitBtn.disabled = false;
			saveResponse( prompt, result, message_no )
			clearInterval( nIntervId );
		}

		const text = chunk.shift();
		if ( text ) {
			result += text;
			tinyMCE.activeEditor.setContent( result, { format: 'raw' } );
			typingEl?.classList?.add( 'lqd-is-hidden' );
		}
	}, 20 );

	if (stream_type == 'backend') {

		const eventSource = new EventSource( `${ streamUrl }?message=${ prompt }` );
		eventSource.addEventListener( 'data', function ( event ) {
			const data = JSON.parse( event.data );
			if ( data.message !== null ) {
				chunk.push( data.message.replace( /(?:\r\n|\r|\n)/g, ' <br> ' ) );
			}
		} );

		// finished eventSource
		eventSource.addEventListener( 'stop', function ( event ) {
			streaming = false;
			eventSource.close();
		} );
	} else {
		const prompt1= atob(guest_event_id);
		const prompt2= atob(guest_look_id);
		const prompt3= atob(guest_product_id);

		const bearer = prompt1+prompt2+prompt3;

		let guest_id2 = atob(guest_id);

		const messages = [];
		messages.push({
			role: "system",
			content: "You are a helpful assistant."
		});
		messages.push({
			role: "user",
			content: prompt
		});

		try {
			const response = await fetch(guest_id2, {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					Authorization: `Bearer ${bearer}`,
				},
				body: JSON.stringify({
					model: "gpt-3.5-turbo-16k",
					messages: messages,
					stream: true, // For streaming responses
				}),
			});
			
			if(response.status != 200) {
				throw response;
			}
			// Read the response as a stream of data
			const reader = response.body.getReader();
			const decoder = new TextDecoder("utf-8");
			let result = '';

			while (true) {
				// if (window.console || window.console.firebug) {
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

					if (content) {
						chunk.push( content.replace( /(?:\r\n|\r|\n)/g, ' <br> ' ) );
					}
				}
			}
		} catch (error) {
			switch(error.status) {
				case 429:
					toastr.error('Api Connection Error. You hit the rate limites of openai requests. Please check your Openai API Key.');
					break;
				default:
					toastr.error('Api Connection Error. Please contact system administrator via Support Ticket. Error is: API Connection failed due to API keys.');
			}
			submitBtn.classList.remove( 'lqd-form-submitting' );
			document.querySelector( '#app-loading-indicator' )?.classList?.add( 'opacity-0' );
			document.querySelector( '#workbook_regenerate' )?.classList?.remove( 'hidden' );
			submitBtn.disabled = false;
			typingEl?.classList?.add( 'lqd-is-hidden' );
			streaming = false;
		}
	}
};

function saveResponse( input, response, message_no ) {
	"use strict";
	var formData = new FormData();
	formData.append( 'input', input );
	formData.append( 'response', response );
	formData.append( 'message_id', message_no );
	jQuery.ajax( {
		url: '/dashboard/user/openai/low/generate_save',
		type: 'POST',
		data: formData,
		contentType: false,
		processData: false,
	} );
	return false;
}

function calculateWords( sentence ) {

	// Count words in the sentence
	let wordCount = 0;

	if ( /^[\u4E00-\u9FFF]+$/.test( sentence ) ) {
		// For Chinese, count the number of characters as words
		wordCount = sentence.length;
	} else {
		// For other languages, split the sentence by word boundaries using regular expressions
		const words = sentence.split( /\b\w+\b/ );
		wordCount = words.length;
	}

	return wordCount;
}
