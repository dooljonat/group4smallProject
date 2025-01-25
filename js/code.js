const urlBase = 'http://localhost/LAMPAPI';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";

function doLogin()
{
	userId = 0;
	firstName = "";
	lastName = "";
	
	let login = document.getElementById("loginName").value;
	let password = document.getElementById("loginPassword").value;
//	var hash = md5( password );
	
	document.getElementById("loginResult").innerHTML = "";

	let tmp = {login:login,password:password};
//	var tmp = {login:login,password:hash};
	let jsonPayload = JSON.stringify( tmp );
	
	let url = urlBase + '/Login.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				let jsonObject = JSON.parse( xhr.responseText );
				userId = jsonObject.id;
		
				if( userId < 1 )
				{		
					document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
					return;
				}
		
				// Save current user information to browser cookies
				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;
				saveCookie();
	
				// Redirect to contacts page
				window.location.href = "contacts.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("loginResult").innerHTML = err.message;
	}
}

function doRegister()
{
	console.log("Attempting to register a new user...");
	
	// Get user information from HTML forms
	let first = document.getElementById("firstName").value;
	let last  = document.getElementById("lastName").value;
	let login = document.getElementById("loginName").value;
	let password = document.getElementById("loginPassword").value;
	
	// Get register result element (for error messages)
	document.getElementById("registerResult").innerHTML = "";

	// Create JSON payload
	let tmp = {first:first, last:last, login:login, password:password};
//	var tmp = {login:login,password:hash};
	let jsonPayload = JSON.stringify( tmp );
	
	// Get URL for Register.php API
	let url = urlBase + '/Register.' + extension;

	// Send request to API to create new user
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				console.log(JSON.stringify(xhr.responseText));

				let jsonObject = JSON.parse( xhr.responseText );
				userId = jsonObject.id;

				console.log(userId);
		
				// If the response returns an invalid userId,
				//  display the error in registerResult
				if( userId < 1 )
				{		
					document.getElementById("registerResult").innerHTML = jsonObject.error;
					return;
				}
	
				// Redirect to login screen
				window.location.href = "index.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("registerResult").innerHTML = err.message;
	}
}

function addContact()
{
	// NOTE TO READERS: I'm not sure if handling the cookies in add_contact.html
	// how color.html/contacts.html does it is necessary, as in,
	// logging users out if the cookies return null,AddCon
	// but it's probably better security and doesn't hurt for now

	console.log("Attempting to add a new contact...");

	// Grab new contact information from add_contact.html form
	let first = document.getElementById("firstName").value;
	let last  = document.getElementById("lastName").value;
	let phoneNumber = document.getElementById("phoneNumber").value;
	let email = document.getElementById("email").value;
	
	document.getElementById("createNewContactResult").innerHTML = "";

	// Create JSON payload
	let tmp = {first:first, last:last, phoneNumber:phoneNumber, email:email, currentUserId:userId};
	let jsonPayload = JSON.stringify( tmp );

	// Get URL for AddContact.php API
	let url = urlBase + '/AddContact.' + extension;

	// Send request to API to add new contact
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				// Get response text from request
				console.log(JSON.stringify(xhr.responseText));
				let jsonObject = JSON.parse( xhr.responseText );
			
				// If there was an error while adding new contact, display it
				if (jsonObject.error != "")
				{
					document.getElementById("createNewContactResult").innerHTML = jsonObject.error;
				}

				// No errors
				else
				{
					document.getElementById("createNewContactResult").innerHTML = "Contact has been added";
				}
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("registerResult").innerHTML = err.message;
	}
}

function saveCookie()
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
}

function readCookie()
{
	userId = -1;
	let data = document.cookie;
	let splits = data.split(",");
	for(var i = 0; i < splits.length; i++) 
	{
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		if( tokens[0] == "firstName" )
		{
			firstName = tokens[1];
		}
		else if( tokens[0] == "lastName" )
		{
			lastName = tokens[1];
		}
		else if( tokens[0] == "userId" )
		{
			userId = parseInt( tokens[1].trim() );
		}
	}
	
	if( userId < 0 )
	{
		window.location.href = "index.html";
	}
	else
	{
		document.getElementById("userName").innerHTML = "Logged in as " + firstName + " " + lastName;
	}
}

function doLogout()
{
	userId = 0;
	firstName = "";
	lastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}

function addColor()
{
	let newColor = document.getElementById("colorText").value;
	document.getElementById("colorAddResult").innerHTML = "";

	let tmp = {color:newColor,userId,userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/AddColor.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("colorAddResult").innerHTML = "Color has been added";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("colorAddResult").innerHTML = err.message;
	}
}

function searchColor()
{
	let srch = document.getElementById("searchText").value;
	document.getElementById("colorSearchResult").innerHTML = "";
	
	let colorList = "";

	let tmp = {search:srch,userId:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/SearchColors.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("colorSearchResult").innerHTML = "Color(s) has been retrieved";
				let jsonObject = JSON.parse( xhr.responseText );
				
				for( let i=0; i<jsonObject.results.length; i++ )
				{
					colorList += jsonObject.results[i];
					if( i < jsonObject.results.length - 1 )
					{
						colorList += "<br />\r\n";
					}
				}
				
				document.getElementsByTagName("p")[0].innerHTML = colorList;
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("colorSearchResult").innerHTML = err.message;
	}
}

function searchContacts()
{
	// NOTE TO COLLABORATORS:
	//  Only adding search by first name for now,
	//  later add extra form to search by last name too
	let firstNameSearch = document.getElementById("firstNameSearchText").value;

	// Clear old results
    document.getElementById("contactSearchResult").innerHTML = "";
    document.getElementById("editContactResult").innerHTML = "";
    document.getElementById("saveContactResult").innerHTML = "";
    document.getElementById("deleteContactResult").innerHTML = "";

	// Create json object
	let tmp = {firstNameSearch:firstNameSearch, lastNameSearch:"", userId:userId};
	let jsonPayload = JSON.stringify( tmp );

	// Get URL for search contacts API
	let url = urlBase + '/SearchContacts.' + extension;
	
	// Send json object as POST request to API
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			// If successful...
			if (this.readyState == 4 && this.status == 200) 
			{
				console.log(JSON.stringify(xhr.responseText));
				let jsonObject = JSON.parse( xhr.responseText ); // Get Response

				// If there was an error while searching contact, display it
				if (jsonObject.error != "")
				{
					document.getElementById("contactSearchResult").innerHTML = jsonObject.error;
				}

				// No errors
				else
				{
					// Parse results and init table
					let contactList = jsonObject.results;
					let contactTable = "";

					// Add each contact to table
					for( let i=0; i < jsonObject.results.length; i++ )
					{
						contactTable += `
							<tr contactID="${contactList[i].id}">
								<td class="firstName">${contactList[i].firstName}</td>
								<td class="lastName">${contactList[i].lastName}</td>
								<td class="phone">${contactList[i].phone}</td>
								<td class="email">${contactList[i].email}</td>
								<td>
                                    <button type="button" onclick="editContact(${contactList[i].id});"><i class="fas fa-pencil-alt"></i></button>
                                    <button type="button" onclick="deleteContact(${contactList[i].id});"><i class="fas fa-trash-alt"></i></button>
                                </td>
								</tr>
							`;
						        
							// Old text buttons if icons are not allowed
							//	<td>
							// 		<button type="button" onclick="editContact(${contactList[i].id});">Edit</button>
							//		<button type="button" onclick="deleteContact(${contactList[i].id});">Delete</button>
							// 	</td>
					}
					
					// Display table
					document.getElementById("contactList").innerHTML = contactTable;

					document.getElementById("contactSearchResult").innerHTML = "Contacts(s) have been retrieved";
				}
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("contactSearchResult").innerHTML = err.message;
	}
	
}

function editContact(contactID){
	// Clear old results
    document.getElementById("contactSearchResult").innerHTML = "";
    document.getElementById("editContactResult").innerHTML = "";
    document.getElementById("saveContactResult").innerHTML = "";
    document.getElementById("deleteContactResult").innerHTML = "";

	// Open the entry
	let row = document.querySelector("tr[contactID='" + contactID + "']");
    if (!row) return;

    // Get the current values
    let firstName = row.querySelector(".firstName").innerText;
    let lastName = row.querySelector(".lastName").innerText;
    let phone = row.querySelector(".phone").innerText;
    let email = row.querySelector(".email").innerText;

	// Edit the entry
	row.innerHTML = `
		<td><input type="text" class="editFirstName" value="${firstName}"></td>
        <td><input type="text" class="editLastName" value="${lastName}"></td>
        <td><input type="text" class="editPhone" value="${phone}"></td>
        <td><input type="text" class="editEmail" value="${email}"></td>
		<td><button type="button" onclick="saveContact(${contactID});"><i class="fas fa-check"></i></button></td>
	`;
	// Old text buttons if icons are not allowed
	//	<td><button type='button' onclick='saveContact(${contactID});'>Save</button></td>
	}

function saveContact(contactID){
	// Clear old results
    document.getElementById("contactSearchResult").innerHTML = "";
    document.getElementById("editContactResult").innerHTML = "";
    document.getElementById("saveContactResult").innerHTML = "";
    document.getElementById("deleteContactResult").innerHTML = "";

	// Open the entry
	let row = document.querySelector("tr[contactID='" + contactID + "']");
	if (!row) return;

	// Get the new values
	let firstName = row.querySelector(".editFirstName").value;
    let lastName = row.querySelector(".editLastName").value;
    let phone = row.querySelector(".editPhone").value;
    let email = row.querySelector(".editEmail").value;

	// Create JSON payload
	let tmp = {first:firstName, last:lastName, phoneNumber:phone, email:email, currentUserId:userId, contactId:contactID};
	let jsonPayload = JSON.stringify( tmp );

	// Get URL for edit contact API
	let url = urlBase + '/EditContact.' + extension;

	// Send request to API to add new contact
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				console.log(JSON.stringify(xhr.responseText));
				let jsonObject = JSON.parse( xhr.responseText );

				// If there was an error while searching contact, display it
				if (jsonObject.error != "")
				{
					document.getElementById("saveContactResult").innerHTML = jsonObject.error;
				}

				// No errors
				else
				{
					row.innerHTML = `
						<td class="firstName">${firstName}</td>
						<td class="lastName">${lastName}</td>
						<td class="phone">${phone}</td>
						<td class="email">${email}</td>
						<td>
							<button type="button" onclick="editContact(${contactID});"><i class="fas fa-pencil-alt"></i></button>
							<button type="button" onclick="deleteContact(${contactID});"><i class="fas fa-trash-alt"></i></button>
						</td>
					`;
					// Old text buttons if icons are not allowed
					//	<td>
					// 		<button type="button" onclick="editContact(${contactID});">Edit</button>
					//		<button type="button" onclick="deleteContact(${contactID});">Delete</button>
					// 	</td>
					
					document.getElementById("editContactResult").innerHTML = "Contact has been saved";
				}
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("saveContactResult").innerHTML = err.message;
	}
}

function deleteContact(contactID){
	// Open the entry so we can remove it
	let row = document.querySelector("tr[contactID='" + contactID + "']");
	if (!row) return;

	let firstName = row.querySelector(".firstName").innerText;
    let lastName = row.querySelector(".lastName").innerText;

	// Confirm deletion with user
    if (!confirm("Are you sure you want to delete " + firstName + " " + lastName + " as a contact?")) {
        return;
    }

	// Clear old results
    document.getElementById("contactSearchResult").innerHTML = "";
    document.getElementById("editContactResult").innerHTML = "";
    document.getElementById("saveContactResult").innerHTML = "";
    document.getElementById("deleteContactResult").innerHTML = "";


	// Create JSON payload
	let tmp = {currentUserId:userId, contactId:contactID};
	let jsonPayload = JSON.stringify( tmp );

	// Get URL for edit contact API
	let url = urlBase + '/DeleteContact.' + extension;

	// Send request to API to add new contact
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				console.log(JSON.stringify(xhr.responseText));
				let jsonObject = JSON.parse( xhr.responseText );

				// If there was an error while saving contact, display it
				if (jsonObject.error != "")
				{
					document.getElementById("deleteContactResult").innerHTML = jsonObject.error;
				}
				else
				{
					row.remove();
					document.getElementById("deleteContactResult").innerHTML = "Contact " + firstName + " " + lastName + " has been deleted";
				}
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("deleteContactResult").innerHTML = err.message;
	}
}
