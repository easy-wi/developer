//##############################################
// MONSTA FTP by MONSTA APPS
//##############################################
//
// This file is part of Monsta FTP. Please see index.php for copyright, 
// license and support details.

//##############################################
// AJAX NOTES
//##############################################
//
// The "multiple" attribute for select lists is not supported.
// Javascript file include and function calls must be included *after* the form html.
// Javascript that is returned from the AJAX request cannot be executed by the browser.

//##############################################
// START AJAX
//##############################################

function ajaxStart() {

	var xmlhttp;
	if (window.XMLHttpRequest) {
		xmlhttp=new XMLHttpRequest();
	} else {
		alert(lang_no_xmlhttp);
	}
	
	return xmlhttp;
}

// ###########################################
// DETECT current GET params
// ###########################################
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
        function(m,key,value) {
            if(value.substring(value.length - 1) == "#") {
                vars[key] = value.substring(0, value.length - 1);
            } else {
                vars[key] = value;
            }
        });
    return vars;
}

var urlWithID = 'userpanel.php?w=gs&d=wf&id=' + getUrlVars()["id"];

// ###########################################
// DETECT BROWSER
// ###########################################

var globalBrowser;

function detectBrowser() {

	var userAgent = navigator.userAgent.toLowerCase();

	// IE version check (IE10+ supports file input onChange and drag & drop uploads)
	if (userAgent.indexOf("msie") !=-1) {
    	if (parseFloat((userAgent.match(/.*(?:rv|ie)[\/: ](.+?)([ \);]|$)/) || [])[1]) >= 10)
			globalBrowser = "ie10+";
		else
			globalBrowser = "ie9-";
	}

	// Other browsers
	if (userAgent.indexOf("firefox") !=-1)
		globalBrowser = "firefox";
	if (userAgent.indexOf("safari") !=-1)
		globalBrowser = "safari";
	if (userAgent.indexOf("chrome") !=-1)
		globalBrowser = "chrome"; // Chrome check must follow Safari as userAgent for Chrome includes "Safari"
	if (userAgent.indexOf("opera") !=-1)
		globalBrowser = "opera";
}

// ###########################################
// DETECT OS
// ###########################################

var globalOs;

function detectOs() {

	if (navigator.appVersion.indexOf("Win")!=-1) globalOs = "win";
	if (navigator.appVersion.indexOf("Mac")!=-1) globalOs = "mac";
}

//##############################################
// INITIALISE EVENT LISTENERS (DRAG & DROP)
//##############################################

function listenDropFiles() {

	// Set the drag & drop div ID
	var dropFilesDiv = document.getElementById("ajaxContentWindow");
	var dropFilesCheckDiv = document.getElementById("dropFilesCheckDiv");
	
	var xmlhttp = new XMLHttpRequest();
	
	// Check if upload is supported
	if (xmlhttp.upload) {

		// Add listener to file drop div
		dropFilesDiv.addEventListener("dragover", stopBrowserActions, false);
		dropFilesDiv.addEventListener("dragleave", stopBrowserActions, false);
		dropFilesDiv.addEventListener("drop", captureDropFiles, false);
		
		// D&D supported message
		//dropFilesCheckDiv.innerHTML = lang_support_drop;
		//dropFilesCheckDiv.className = 'dropFilesCheckPassColor';
		
	} else {
	
		// D&D unsupported message
		//dropFilesCheckDiv.innerHTML = lang_no_support_drop;
		//dropFilesCheckDiv.className = 'dropFilesCheckFailColor';
	}
}

//###############################################
// INITIALISE EVENT LISTENERS (HIDE CONTEXT MENU)
//###############################################

function listenContextMenu() {

	if (globalBrowser == "ie")
		document.attachEvent("onclick", hideFileContextMenu);
	else
		document.addEventListener("click", hideFileContextMenu, true);
}

//##############################################
// CAPTURE DROPPED FILES
//##############################################

function captureDropFiles(e) {

	stopBrowserActions(e);

	// Get the dropped files
	var globalFiles = e.target.files || e.dataTransfer.files; // firefox/safari || chrome
	
	// Upload files
	if (globalFiles.length > 0)
		processFileUploads(e,1,0,globalFiles);
}

//##############################################
// PROCESS FILE UPLOADS
//##############################################

var globalFileUploadPreCount;
var globalFileUploadCount;
var globalFileUploadTotal;
var globalFiles = new Array();
var globalPaths = new Array();

function processFileUploads(e,isDrop,isFolder,globalFiles) {

	var xmlhttp = ajaxStart();

	// Check upload is supported
	if (xmlhttp.upload) {
	
		// Turn off repeat button
		if (isDrop == 1 || isFolder == 1)
			hideRepeatButton();
		
		// CHROME - DRAG & DROP FOLDER
		if (globalBrowser == "chrome" && isDrop == 1) {

			//globalFileUploadCount=0;
			var items = e.dataTransfer.items;

			// Check there is at least 1 file
			if (items.length > 0) {
			
				// Resize file listing window
				setFileWindowSize("ajaxContentWindow",0,360);
			
				// Count files to upload
				globalFileUploadPreCount=0;
				for (var i=0; i<items.length; i++) {
					var item = items[i].webkitGetAsEntry();
					traverseFileTree(item,"");
				}
				
				// The uploading of files needs a slight delay to allow the  
				// traverseFileTree() function to start counting the files to upload
				setTimeout( function() { uploadFilesChrome() }, 500);
			}
		}
		
		// CHROME - SELECT FOLDER
		if (isFolder == 1) {
		
			// Reset arrays
			globalFiles = [];
			globalPaths = [];
			
			// Process the files
			var folderContents = e.target.files;
			
			// Check there is at least 1 file
			if (folderContents.length > 0) {
			
				// Resize file listing window
				setFileWindowSize("ajaxContentWindow",0,360);
				
				for (var i=0, j=0, file, filePath, fileName; file=folderContents[i]; i++) {
					
					// Check file is not a folder (represented by a dot)
					if (file.name != ".") {
	
						fileName = file.webkitRelativePath;
						displayTransferWindow(file,fileName,j); // display file in transfer window
						filePath = fileName.replace(file.name,"");
						globalFiles[j] = file; // record file to array
						globalPaths[j] = encodeURIComponent(filePath); // record path to array
						j++;
					}			
				}
				
				// Upload the folder
				uploadFoldersChrome(globalFiles);
			}
		}
		
		// OTHER BROWSERS - DRAG & DROP (OR SELECT FILE/S (ALSO CHROME))
		if (globalBrowser != "chrome" || (isFolder == 0 && isDrop == 0)) {
		
			// Reset this value for Chrome folder drops
			globalFileUploadPreCount=0;
			
			// Set the files input field if not drag & drop
			if (globalFiles == '')
				globalFiles = document.getElementById('uploadFile').files;
			
			// Check there is at least 1 file
			if (globalFiles.length > 0) {

				// Resize file listing window
				setFileWindowSize("ajaxContentWindow",0,360);
			
				// Open transfer window
				for (var i=0, file; file=globalFiles[i]; i++) {
					displayTransferWindow(file,file.name,i);
				}
				
				// Display indicator icon
				showIndicatorDiv();
			
				// Upload first file (subsequent files upload as each one finishes)
				globalFileUploadCount=0;
				globalFileUploadTotal = globalFiles.length;
				fileUploader(globalFiles,"",globalFileUploadCount,0,isDrop);
			}
		}

	} else {
	
		// Submit to iframe if AJAX upload not supported
		submitToIframe("&ftpAction=iframe_upload");
	}
}

//##############################################
// UPLOAD FILES (CHROME DRAG & DROP)
//##############################################

function uploadFilesChrome(globalFolders) {

	globalFileUploadCount=0; // must be set as a var
	var filePath = globalPaths[0]; // parse the first path
	fileUploader(globalFiles,filePath,globalFileUploadCount,0,1);
}

//##############################################
// UPLOAD FOLDER (CHROME)
//##############################################

function uploadFoldersChrome(globalFiles) {

	// This function exists parallel to uploadFilesChrome() because the 
	// value of globalFiles gets reset between processFileUploads() and 
	// uploadFilesChrome().

	globalFileUploadCount=0; // must be set as a var
	globalFileUploadTotal = globalFiles.length;
	var filePath = globalPaths[0]; // parse the first path
	fileUploader(globalFiles,filePath,globalFileUploadCount,1,0);
}

//##############################################
// TRAVERSE FILE TREE (CHROME)
//##############################################

function traverseFileTree(item,filePath) {
	
	// If File
	if (item.isFile) {
		
		item.file(function(file) {

			var fileName = filePath + file.name;
			displayTransferWindow(file,fileName,globalFileUploadPreCount); // display file in transfer window
			globalFiles[globalFileUploadPreCount] = file; // record file to array
			globalPaths[globalFileUploadPreCount] = encodeURIComponent(filePath); // record path to array
			globalFileUploadPreCount++;
		});
	}
	
	// If Directory
	if (item.isDirectory) {

		var dirReader = item.createReader();
		
		// Read list of files/folders and call this function on each
		dirReader.readEntries(function(entries) {
		
			for (var i=0; i<entries.length; i++) {
				traverseFileTree(entries[i], filePath + item.name + "/");
			}
		});
	}
}

//##############################################
// DISPLAY TRANSFER WINDOW
//##############################################

function displayTransferWindow(file,fileName,rowID) {

	rowID++; // Add 1 for table headers

	showPopUp('uploadProgressDiv');
		
	// Append a table row to transfer div
	var table = document.getElementById("uploadProgressTable"); 
	var row = table.insertRow(rowID);
	row.id = "row"+rowID;
	var cell1 = row.insertCell(0); // blank
	var cell2 = row.insertCell(1);
	cell2.innerHTML = formatStrLen(fileName,65,1); // file name
	var cell3 = row.insertCell(2);
	cell3.innerHTML = formatFileSize(file.size); // file size
	var cell4 = row.insertCell(3);
	cell4.innerHTML = '<div class="floatLeft" id="progressParent'+rowID+'">'+lang_transfer_pending+'</div><div class="progressBar" id="percent'+rowID+'"></div>'; // progress bar
	var cell5 = row.insertCell(4);
	cell5.innerHTML = '<div id="timeE'+rowID+'"></div>'; // time elapsed
	var cell6 = row.insertCell(5);
	cell6.innerHTML = '<div id="uploaded'+rowID+'"></div>'; // size uploaded
	var cell7 = row.insertCell(6);
	cell7.innerHTML = '<div id="rate'+rowID+'"></div>'; // transfer rate
	var cell8 = row.insertCell(7);
	cell8.innerHTML = '<div id="timeR'+rowID+'"></div>'; // time remaining
	var cell9 = row.insertCell(8);
	cell9.innerHTML = '<div id="close'+rowID+'"></div>'; // close button (for rejects)
	
	// Set background color of even-numbered rows
	if (rowID%2 == 0)
		row.className='trBg1';
	else
		row.className='trBg0';
}

//##############################################
// FILE UPLOADER
//##############################################

function fileUploader(globalFiles,filePath,rowID,isFolder,isDrop) {
	
	showIndicatorDiv();
	
	// Get file from array
	file = globalFiles[rowID];
	
	rowID++; // Add 1 for table headers
	
	// Check the file size doesn't exceed allowed limit
	if (file.size > upload_limit) {
	
		// Change progress to error message
		document.getElementById('progressParent'+rowID).innerHTML = '<span class="sizeErrorSpan">'+lang_file_size_error+'</span>' // status

		// Add close button to row
		document.getElementById('close'+rowID).innerHTML = '<span class="progressClose" onclick="deleteProgressRow('+rowID+')">x</span>' // status
		
		// Start next transfer
		globalFileUploadCount++;

		// Check if another file needs uploading
		if (globalFileUploadCount < globalFileUploadTotal || globalFileUploadCount < globalFileUploadPreCount) {

			// Set path (if exists)
			if (globalPaths[globalFileUploadCount] != undefined)
				filePath = globalPaths[globalFileUploadCount];
			
			fileUploader(globalFiles,filePath,globalFileUploadCount,isFolder,isDrop);
			
		} else {

			// Reset arrays
			globalFiles = [];
			globalPaths = [];
			
			// Display repeat button
			if (isDrop == 0 && isFolder == 0)
				showRepeatButton();
			
			// Reset form for Chrome
			if (isFolder == 1) {
				resetForm();
			}
			
			// Hide indicator icon
			hidePopUp('indicatorDiv');
		}
		
	} else {
	
		// File size accepted

		var xmlhttp = ajaxStart();
		
		// Set action for change of state (listener)
		xmlhttp.onreadystatechange = function stateChanged() {
		
			// Check if upload has completed
			if(xmlhttp.readyState==4) {
	
				// Set the values of the progress fields to max (finished)
				document.getElementById('progress'+rowID).value = 100; // progress bar
				document.getElementById('percent'+rowID).innerHTML = '100%'; // percent
				document.getElementById('timeR'+rowID).innerHTML = formatSecondsToTime(0); // time remaining
				document.getElementById('progressParent'+rowID).innerHTML = '<span class="transferringSpan">'+lang_transferring_to_ftp+'</span>' // status
				document.getElementById('percent'+rowID).innerHTML = ""; // percent
				document.getElementById('timeE'+rowID).innerHTML = ""; // time elapsed
				//document.getElementById('uploaded'+rowID).innerHTML = formatFileSize(file.size); (commented out because by the time the function returns, the value has already been cleared)
				document.getElementById('uploaded'+rowID).innerHTML = ""; // uploaded
				document.getElementById('rate'+rowID).innerHTML = ""; // transfer rate
				document.getElementById('timeR'+rowID).innerHTML = ""; // time remaining
			
				// Refresh open folder (delay half second to complete progress display)
				setTimeout(function(){ openThisFolder(globalOpenFolder,0) },500)
				
				// Delete the progress row from table (delay half second to complete progress display)
				setTimeout(function(){ deleteProgressRow(rowID) },500)
	
				// Start next transfer
				globalFileUploadCount++;
	
				// Check if another file needs uploading
				if (globalFileUploadCount < globalFileUploadTotal || globalFileUploadCount < globalFileUploadPreCount) {
	
					// Set path (if exists)
					if (globalPaths[globalFileUploadCount] != undefined)
						filePath = globalPaths[globalFileUploadCount]
					
					fileUploader(globalFiles,filePath,globalFileUploadCount,isFolder,isDrop);
					
				} else {
	
					// Reset arrays
					globalFiles = [];
					globalPaths = [];
					
					// Display repeat button
					if (isDrop == 0 && isFolder == 0)
						showRepeatButton();
					
					// Reset form for Chrome
					if (isFolder == 1) {
						resetForm();
						
					}
				}	
			}
		}
		
		// Create the progress bar
		document.getElementById('progressParent'+rowID).innerHTML = '<progress id="progress'+rowID+'"min="0" max="100" value="0"></progress>';		
		
		var start = new Date().getTime(), elapsed = '0.0';
		var time=0;
		var elapsed=0;
		var bytesPerSecond=0;
		var timeToUpload=0;
		var progress=0;
		var progressBar;
		
		// Update progress info
		xmlhttp.upload.onprogress = function(e) {
			if (e.lengthComputable) {
			
				// Get elapsed time  
				time = new Date().getTime() - start;
				elapsed = Math.floor(time / 1000);
	
				// Set the elapsed time
				document.getElementById('timeE'+rowID).innerHTML = formatSecondsToTime(elapsed);
				
				// Set the uploaded amount
				document.getElementById('uploaded'+rowID).innerHTML = formatFileSize(e.loaded);
		
				// Get the transfer rate
				if (elapsed == 0)
					bytesPerSecond = e.loaded; // if less than 1s set xfer rate to file size
				else
					bytesPerSecond = e.loaded/elapsed;
	
				// Set the transfer rate
				document.getElementById('rate'+rowID).innerHTML = formatFileSize(bytesPerSecond) + '/s';
	
				// Get remaining time
				timeToUpload = Math.round((e.total - e.loaded) / bytesPerSecond);
				
				// Set the remaining time
				document.getElementById('timeR'+rowID).innerHTML = formatSecondsToTime(timeToUpload);
				
				// Get the progress
				progressBar = document.getElementById('progress'+rowID);
				progressBar.value = (e.loaded / e.total) * 100;
				
				// Set the progress bar
				//progressBar.innerHTML = Math.round(progressBar.value)+'%'; // Display % for unsupported browsers
				
				// Display %age complete
				document.getElementById('percent'+rowID).innerHTML = Math.round(progressBar.value) + '%';
			}
		};	
		
		// Post form
		xmlhttp.open("POST", urlWithID + "&ftpAction=upload&filePath="+filePath, true);
		xmlhttp.setRequestHeader("Cache-Control", "no-cache");
		xmlhttp.setRequestHeader("X-Filename", file.name);
		xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
		xmlhttp.setRequestHeader("X-File-Size", file.size);
		xmlhttp.setRequestHeader("X-File-Type", file.type);
		xmlhttp.setRequestHeader("Content-Type", "multipart/form-data");
		xmlhttp.send(file);
	}
}

//##############################################
// STOP BROWSER ACTIONS
//##############################################

function stopBrowserActions(e) {
	
	e.stopPropagation();
	e.preventDefault();
}

//##############################################
// PROCESS FORM ONCLICK
//##############################################

function processForm(vars) {
	
	// Display indicator icon
	showIndicatorDiv();

	var xmlhttp = ajaxStart();

	// Get form data
	vars = vars + generateVars();
		
	// Add window dimensions
	vars = vars + "&windowWidth=" + window.innerWidth;
	vars = vars + "&windowHeight=" + window.innerHeight;

	// Return HTML from AJAX to div (when complete)
	xmlhttp.onreadystatechange = function stateChanged() {
		if(xmlhttp.readyState==4) {
			document.getElementById("ajaxContentWindow").innerHTML=xmlhttp.responseText;
		}
	}
	
	// Post form
	xmlhttp.open("POST",urlWithID,true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(vars);
	
	// Hide indicator icon
	hidePopUp('indicatorDiv');
}

//##############################################
// CREATE URL POST VARS
//##############################################

function generateVars() {

	var theForm = document.getElementById('ftpActionForm');
	var vars = '';

	for(i=0;i<theForm.elements.length; i++){

		if(theForm.elements[i].type == "text" || theForm.elements[i].type == "textarea" || theForm.elements[i].type == "checkbox" || theForm.elements[i].type == "select-one" || theForm.elements[i].type == "radio" || theForm.elements[i].type == "hidden" || theForm.elements[i].type == "password" || theForm.elements[i].type == "button") {

			// Text, Texarea, Hidden, Password, Button
			if(theForm.elements[i].type == "text" || theForm.elements[i].type == "textarea" || theForm.elements[i].type == "hidden"|| theForm.elements[i].type == "password" || theForm.elements[i].type == "button"){

				vars += "&";
				vars += theForm.elements[i].name;
				vars += "=";
				vars += encodeURIComponent(theForm.elements[i].value);
			}
			
			// Checkbox
			if(theForm.elements[i].type == "checkbox"){

					vars += "&";
					vars += theForm.elements[i].name;
					vars += "=";
					
					if (theForm.elements[i].checked == true)
						vars += theForm.elements[i].value;
			}
			
			// Radio
			if(theForm.elements[i].type == "radio"){

				if (theForm.elements[i].checked == true) {
					vars += "&";
					vars += theForm.elements[i].name;
					vars += "=";
					vars += theForm.elements[i].value;
				}
			}
			
			// Select (single only - multiple not supported)
			if(theForm.elements[i].type == "select-one"){

				vars += "&";
				vars += theForm.elements[i].name;
				vars += "=";
				if (theForm.elements[i].options[theForm.elements[i].selectedIndex].value != "")
					vars += encodeURIComponent(theForm.elements[i].options[theForm.elements[i].selectedIndex].value);
				else
					vars += encodeURIComponent(theForm.elements[i].options[theForm.elements[i].selectedIndex].text);
			}
		}
	}
	
	return vars;
}

//##############################################
// MAKE POP-UP VISIBLE
//##############################################

function showPopUp(windowID) {

	var theWin = document.getElementById(windowID);
	theWin.style.visibility = 'visible';
	theWin.style.display = '';
	
	// Change cursor to progress
	document.getElementById("ajaxContentWindow").style.cursor = 'progress';
}

//##############################################
// MAKE POP-UP HIDDEN
//##############################################

function hidePopUp(windowID) {

	var theWin = document.getElementById(windowID);
	theWin.style.visibility = 'hidden';
	theWin.style.display = 'none';
	
	// Change cursor from progress to default
	document.getElementById("ajaxContentWindow").style.cursor = 'default';
}

//##############################################
// CENTER POP-UP
//##############################################

function centerPopUp(windowID,popUpWidth,popUpHeight) {

	var popUpLeft;
	var popUpTop;
	
	popUpLeft = (window.innerWidth - popUpWidth) / 2;
	popUpTop = (window.innerHeight - popUpHeight) / 2;
	
	var theWin = document.getElementById(windowID);
	
	theWin.style.position = 'absolute';	
	theWin.style.left = popUpLeft +'px';
	theWin.style.top = popUpTop + 'px';
	theWin.style.width = popUpWidth +'px';
	theWin.style.height = popUpHeight +'px';
}

//##############################################
// WRITE INDICATOR
//##############################################

function showIndicatorDiv() {

	showPopUp('indicatorDiv');
	centerPopUp('indicatorDiv',32,32);
}
	
//##############################################
// CHECKBOX SELECT ALL
//##############################################

function checkboxSelectAll() {

	// Determine if "select all" checkbox is checked
	if (document.getElementById("checkboxSelector").checked == true) {
		var isChecked=1;
		activateActionButtons(1,0);
	} else {
		var isChecked=0;
		activateActionButtons(0,0);
	}
	
	// Go through each checkbox
	var theForm = document.getElementById('ftpActionForm');
	
	for(i=0;i<theForm.elements.length; i++){
		if(theForm.elements[i].type == "checkbox") {
			if (isChecked == 1)
				theForm.elements[i].checked = true;
			else
				theForm.elements[i].checked = false;
		}
	}
}

//##############################################
// SET FILE LISTING WINDOW SIZE
//##############################################

var globalEditorDefaultSize = 170;

function setFileWindowSize(divID,height,dedn) {

	if (dedn == 0)
		dedn = globalEditorDefaultSize;

	if (height == 0) {
		var screenHeight = window.innerHeight;
		var height = screenHeight - dedn;
	}
	
	document.getElementById("ajaxContentWindow").style.height = height+'px';
}

//##############################################
// UPLOAD FILE CHECK
//##############################################

function uploadFileCheck(e) {

	if (document.getElementById("uploadFile").value == "")
		alert(lang_no_file_selected);
	else
		processFileUploads(e,0,0,'')
}

//##############################################
// REFRESH FILE LISTING
//##############################################

function refreshListing() {

	openThisFolder(globalOpenFolder,0);
}

//##############################################
// OPEN FOLDER
//##############################################

var globalOpenFolder;

function openThisFolder(folder,hideRepeatBtn) {

	if (folder == undefined)
		folder = "";
	else
		globalOpenFolder = folder;
	
	// Submit form
	processForm('&ftpAction=openFolder&openFolder='+folder);
	
	// Hide Repeat Upload button
	if (hideRepeatBtn == 1) {
		hideRepeatButton();
		resetForm();
	}
}

//##############################################
// SHOW REPEAT BUTTON
//##############################################

function showRepeatButton() {

	// Files do not upload properly in Firefox
	if (globalBrowser != "firefox") {

		document.getElementById("repeatUploadDiv").style.visibility = 'visible';
		document.getElementById("repeatUploadDiv").style.display = '';
	}
}

//##############################################
// HIDE REPEAT BUTTON
//##############################################

function hideRepeatButton() {

	document.getElementById("repeatUploadDiv").style.visibility = 'hidden';
	document.getElementById("repeatUploadDiv").style.display = 'none';
}

//##############################################
// CHECK FILES ARE SELECTED
//##############################################

function checkFilesSelected() {

	// Go through each checkbnox
	var theForm = document.getElementById('ftpActionForm');
	var isChecked = 0;
	
	for(i=0;i<theForm.elements.length; i++){
		if(theForm.elements[i].type == "checkbox") {
			if (theForm.elements[i].checked == true)
				isChecked=1;
		}
	}
	
	if (isChecked == 0) {
		alert(lang_none_selected);
		return 0;
	} else {
		return 1;
	}
}

//##############################################
// CHECK SINGLE FILE/FOLDER CHECKED
//##############################################

function checkFileChecked() {

	var isChecked=0;

	// Go through each checkbox for a tick
	var theForm = document.getElementById('ftpActionForm');
	
	for(i=0;i<theForm.elements.length; i++){
		if(theForm.elements[i].type == "checkbox" && theForm.elements[i].checked == true)
			isChecked = 1;
	}
	
	// If at least one box is ticked, activate action buttons
	if (isChecked == 1) {
	 	activateActionButtons(1,0);
	} else {
		activateActionButtons(0,0);
	}
}

//##############################################
// ACTIVATE ACTION BUTTONS
//##############################################

function activateActionButtons(active,paste) {

	// Paste button
	if (paste == 1 || globalClipboardAction == 'copy')
		document.getElementById('actionButtonPaste').disabled = false;
	else
		document.getElementById('actionButtonPaste').disabled = true;
	
	// All other buttons
	if (active == 1) {
	
		document.getElementById('actionButtonCut').disabled = false; // cut
		document.getElementById('actionButtonCopy').disabled = false; // copy
		document.getElementById('actionButtonRename').disabled = false; // rename
		document.getElementById('actionButtonDelete').disabled = false; // delete
	
	} else {

		document.getElementById('actionButtonCut').disabled = true;
		document.getElementById('actionButtonCopy').disabled = true;
		document.getElementById('actionButtonRename').disabled = true;
		document.getElementById('actionButtonDelete').disabled = true;

	}
}

//##############################################
// ALERT COPY WARNING
//##############################################

function alertCopyWarning() {

	// This function is not active by default, but can
	// be activated on the actionFunctionCopy() function
	alert('WARNING: As files cannot be copied on the remote server they \n will be downloaded to the client server and uploaded to the \n destination folder. Depending on the size and number of files \n being copied, this may take several minutes.');
}

//##############################################
// SET CLIPBOARD ACTION
//##############################################

var globalClipboardAction;

function setClipboard(type) {
	
	globalClipboardAction = type;
}


//##############################################
// PARSE ERROR TO PARENT
//##############################################

function parseDownloadError(error) {
	
	processForm('&ftpAction=openFolder&error='+error);
}

//##############################################
// DISPLAY FILE CONTEXT MENU
//##############################################

function displayContextMenu(e,file,folder,isLin) {

	stopBrowserActions(e);
	showFileContextMenu(e,file,folder,isLin);
}

//##############################################
// HIDE CONTEXT MENU
//##############################################

function hideFileContextMenu() {

	document.getElementById("contextMenu").style.visibility = 'hidden';
	document.getElementById("contextMenu").style.display = 'none';
}

//##############################################
// SHOW CONTEXT MENU
//##############################################

var globalContextHeight;

function showFileContextMenu(e,file,folder,isLin) {

    var editableExts = new Array();
    editableExts[0] = 'xml';
    editableExts[1] = 'vdf';
    editableExts[2] = 'cfg';
    editableExts[3] = 'config';
    editableExts[4] = 'ini';
    editableExts[5] = 'conf';
    editableExts[6] = 'gam';
    editableExts[7] = 'txt';
    editableExts[8] = 'log';
    editableExts[9] = 'smx';
    editableExts[10] = 'sp';
    editableExts[11] = 'db';
    editableExts[12] = 'lua';
    editableExts[13] = 'prop';
    editableExts[14] = 'properties';
    editableExts[15] = 'example';

	globalContextHeight=12; // top and bottom padding
	// Set function for paste
	if (file == "" && (globalClipboardAction == "cut" || globalClipboardAction == "copy"))
		var pasteOnclick = "actionFunctionPaste('"+folder+"')";
	else
		var pasteOnclick = "";

	var menuHTML="";
	
	// Open folder or Download file
	if (folder != "")
		menuHTML += createContextMenuItem(lang_context_open,"openThisFolder('"+folder+"',1)",1);
	if (file != "")	
		menuHTML += createContextMenuItem(lang_context_download,"window.location='?dl="+file+"'",1);
	
	if (folder != "" || file != "") {
	
		// Check if file is editable
		if (file != "") {
			var extension = file.substring(file.lastIndexOf('.')+1);
			if (editableExts.indexOf(extension) > -1) {
                menuHTML += createContextMenuItem(lang_context_edit,"actionFunctionEdit('"+file+"')",1);
                menuHTML += createContextMenuItem(lang_context_template,"actionFunctionTemplate('"+file+"')",1);
            }
		}
	
		menuHTML += createContextMenuItem(lang_context_cut,"actionFunctionCut('"+file+"','"+folder+"')",0);
		menuHTML += createContextMenuItem(lang_context_copy,"actionFunctionCopy('"+file+"','"+folder+"')",0);
		menuHTML += createContextMenuItem(lang_context_paste,pasteOnclick,0);
		menuHTML += createContextMenuItem(lang_context_rename,"actionFunctionRename('"+file+"','"+folder+"')",0);
		menuHTML += createContextMenuItem(lang_context_delete,"actionFunctionDelete('"+file+"','"+folder+"')",0);

	} else {
	
		menuHTML += createContextMenuItem(lang_context_cut,"",0);
		menuHTML += createContextMenuItem(lang_context_copy,"",0);
		menuHTML += createContextMenuItem(lang_context_paste,pasteOnclick,0);
	}
	
	// Load the menu HTML into the menu DIV
	document.getElementById("contextMenu").innerHTML = menuHTML;
	
	// Position div to cursor
	positionDivToCursor(e,"contextMenu");
}

//##############################################
// CREATE CONTEXT MENU ITEM
//##############################################

function createContextMenuItem(label,onclick,divider) {

	if (onclick == "")
		var menuHTML = '<div class="contextMenuDivInactive" class="contextMenuInactive">'+label+'</div>';
	else
		var menuHTML = '<div class="contextMenuDiv" onclick="'+onclick+'" onmouseover="this.className=\'contextMenuDivMouseOver\';" onmouseout="this.className=\'contextMenuDiv\';">'+label+'</div>';

	// Add horizontal divider
	if (divider == 1) {
		menuHTML += '<div class="contextMenuDivider"></div>';
		globalContextHeight = parseInt(globalContextHeight) + 12;
	}

	// Set height of the menu
	globalContextHeight = parseInt(globalContextHeight) + 29;
		
	return menuHTML;
}


//##############################################
// ACTION FUNCTION - EDIT
//##############################################

function actionFunctionEdit(file) {

	var vars = '&ftpAction=edit&file='+file;
	processForm(vars);
}


//##############################################
// ACTION FUNCTION - TEMPLATE CREATE
//##############################################

function actionFunctionTemplate(file) {

    var vars = '&ftpAction=template&file='+file;
    processForm(vars);
}

//##############################################
// ACTION FUNCTION - CUT
//##############################################

function actionFunctionCut(file,folder) {

	var vars = '&ftpAction=cut&folderSingle='+folder+'&fileSingle='+file;

	// Check if a file or folder is set or 1+ checkboxes selected
	if (file != '' || folder != '' || checkFilesSelected() == 1) {
		processForm(vars);
		setClipboard('cut');
		activateActionButtons(0,1);
	}
}

//##############################################
// ACTION FUNCTION - COPY
//##############################################

function actionFunctionCopy(file,folder) {

	var vars = '&ftpAction=copy&folderSingle='+folder+'&fileSingle='+file;

	// Check if a file or folder is set or 1+ checkboxes selected
	if (file != '' || folder != '' || checkFilesSelected() == 1) {
		//alertCopyWarning();
		processForm(vars);
		setClipboard('copy');
		activateActionButtons(0,1);
	}
}

//##############################################
// ACTION FUNCTION - PASTE
//##############################################

function actionFunctionPaste(folder) {

	var vars = '&ftpAction=paste';
	
	// Check if a folder has been right-clicked on for paste
	if (folder != "")
		vars += '&rightClickFolder='+folder;

	processForm(vars);
	activateActionButtons(0,0);
	
	// Reset clipboard action for cut
	if (globalClipboardAction == "cut")
		globalClipboardAction = "";
}

//##############################################
// ACTION FUNCTION - RENAME
//##############################################

function actionFunctionRename(file,folder) {

	var vars = '&ftpAction=rename&folderSingle='+folder+'&fileSingle='+file;

	// Check if a file or folder is set or 1+ checkboxes selected
	if (file != '' || folder != '' || checkFilesSelected() == 1) {
		processForm(vars);
	}
}

//##############################################
// ACTION FUNCTION - DELETE
//##############################################

function actionFunctionDelete(file,folder) {

	var vars = '&ftpAction=delete';
	
	if (file != '' || folder != '')
		vars += '&folderSingle='+folder+'&fileSingle='+file;

	// Check if a file or folder is set or 1+ checkboxes selected
	if (file != '' || folder != '' || checkFilesSelected() == 1) {
		processForm(vars);
		activateActionButtons(0,0);
	}
}

//##############################################
// ACTION FUNCTION - LOGOUT
//##############################################

function actionFunctionLogout() {

	document.location.href='?logout=1'
}

//##############################################
// SELECT FILE (ADD BORDER)
//##############################################

// IE adds a dotted border around a link that has been clicked, other browsers
// do not. This function adds a border to identify which link has been right-
// clicked on.

function selectFile(theId,checkIE) {

	if (checkIE == 0 || (checkIE == 1 && globalBrowser != "ie")) {

		// Remove any existing borders
		unselectFiles();

		// Add border to item
        document.getElementById(theId).style.border = '1px dotted gray';
        document.getElementById(theId).style.marginLeft = '-1px';
        document.getElementById(theId).style.marginRight = '-1px';
	}
}

//##############################################
// UNSELECT FOLDER
//##############################################

function unselectFolder(folder) {

	var href = document.getElementById(folder);

	href.style.border='';
	href.style.marginLeft = '0px';
	href.style.marginRight = '0px';
}

//##############################################
// UNSELECT FILES
//##############################################

function unselectFiles() {

	// Go through each <a> tag and remove any borders
	var hrefs = document.getElementsByTagName("A");
	for (var i = 0; i < hrefs.length; i++) {
		hrefs[i].style.border='';
		hrefs[i].style.marginLeft = '0px';
		hrefs[i].style.marginRight = '0px';
	}
}

//##############################################
// SET DRAG FILE
//##############################################

var globalDragFile;

function setDragFile(file,folder) {
	
	if (file != "")
		globalDragFile = file;
	if (folder != "")
		globalDragFile = folder;
}

//##############################################
// DRAG FILE
//##############################################

function dragFile(e) {

	stopBrowserActions(e);
}

//##############################################
// DROP FILE
//##############################################

function dropFile(folder) {

	var vars = '&ftpAction=dragDrop&dragFile='+globalDragFile+'&dropFolder='+folder;
	processForm(vars);
	activateActionButtons(0,0);	
}

//##############################################
// POSITION DIV TO CURSOR
//##############################################

function positionDivToCursor(e,divId) {
	
	var mousex = e.clientX;
	var mousey = e.clientY;
	var innerHeight = window.innerHeight;
	
	// Adjust Y for IE
	if (globalBrowser != "ie")
		mousey = parseInt(mousey) + 15;
	
	// Adjust height
	if ((parseInt(mousey) + globalContextHeight) > innerHeight)
		mousey = mousey - globalContextHeight;
	
	// Set coordinates for context menu and display
	document.getElementById(divId).style.left = mousex + 'px';
	document.getElementById(divId).style.top = mousey + 'px';
	document.getElementById(divId).style.visibility = 'visible';
	document.getElementById(divId).style.display = '';
}

//##############################################
// FORMAT FILE SIZE
//##############################################

function formatFileSize(size) {
	
	if (size < 1024) {
		size = Math.round(size)+' '+lang_size_b;
	} else if (size < (1024*1024)) {
		size = Math.round(size/1024)+' '+lang_size_kb;
	} else if (size < (1024*1024*1024)) {
		size = ((size/1024)/1024).toFixed(1)+' '+lang_size_mb;
	} else if (size < (1024*1024*1024*1024)) {
		size = (((size/1024)/1024)/1024).toFixed(1)+' '+lang_size_gb;
	}
	
	return size;
}

//##############################################
// FORMAT SECONDS TO TIME (00:00:00)
//##############################################

function formatSecondsToTime(s) {

	var time='';

	var h = Math.floor(s / 3600);
	if (h > 0)
		s = s - (h * 3600);

	var m = Math.floor(s / 60);
	if (m > 0)
		s = s - (m * 60);
	
	if (h < 10)
		time += '0';
		
	time += h + ':';
	
	if (m < 10)
		time += '0';
		
	time += m + ':';

	if (s < 10)
		time += '0';
	
	time += s;

	return time;
}

//##############################################
// FORMAT STRING TO LENGTH
//##############################################

function formatStrLen(str,n,elipse) {

	if (str.length > n) {

		str = str.substr(0,n);
	
		if (elipse == 1)
			str = str + '...';
	}

	return str;
}

//##############################################
// DELETE ROW FROM UPLOAD PROGRESS TABLE
//##############################################

function deleteProgressRow(rowID) {

	// Delete the row
 	var row = document.getElementById("row"+rowID);
    row.parentNode.removeChild(row);
	
	// Check if transfer table can be closed
	var rowCount = document.getElementById("uploadProgressTable").rows.length;
	
	if (rowCount == 1) {
		hidePopUp("uploadProgressDiv");
		setFileWindowSize("ajaxContentWindow",0,0);
	}	
}

//##############################################
// CHOOSE FILE TO UPLOAD (ONCLICK)
//##############################################

function fileChoose(e) {
	
	document.getElementById("uploadFile").click();
}

//##############################################
// CHOOSE FOLDER TO UPLOAD (ONCLICK)
//##############################################

function dirChoose(e) {
	
	document.getElementById("uploadDir").click();
}

//##############################################
// RESET FORM
//##############################################

function resetForm() {

	// Required for file onChange to work in Chrome
	document.ftpActionForm.reset();
}

//##############################################
// CLOSE EDITOR
//##############################################

function submitToIframe(vars) {

	var theForm = document.forms["ftpActionForm"];
	
	// Submit the form post to the iframe
	theForm.target = 'ajaxIframe';
	theForm.action = urlWithID + '&' + vars;
	theForm.submit();
	
	// Reset values
	theForm.target = '';
	theForm.action = urlWithID;

    processForm(vars);
}

//##############################################
// DISPLAY UPLOAD BUTTONS
//##############################################

function displayUploadButtons() {

	var html="";

	html += '<div class="floatLeft10">';

	// IE Upload File (for < IE9)
	if (globalBrowser == "ie9-") {
		html += '<input type="file" name="uploadFile" id="uploadFile" multiple onChange="processFileUploads(event,0,0,\'\')"> ';
		html += '<input type="button" value="'+lang_btn_upload_file+'" onClick="uploadFileCheck(event)" class="btn btn-primary">';
	}
	
	// Non-IE9 Upload File
	if (globalBrowser != "ie9-") {
		html += '<input type="button" value="'+lang_btn_upload_files+'" onClick="fileChoose(event)" class="btn btn-primary">';
	}
	
	html += '</div>';
	
	// Repeat Button
	html += '<div id="repeatUploadDiv" style="visibility: hidden; display: none">';
		html += '<div class="floatLeft10">';
			html += '<input type="button" value="'+lang_btn_upload_repeat+'" onclick="processFileUploads(event,0,0,\'\')" class="btn btn-primary">';
		html += '</div>';
	html += '</div>';
	
	// Chrome Upload Folder
	if (globalBrowser == "chrome") {
		html += '<div class="floatLeft">';
			html += '<input type="button" value="'+lang_btn_upload_folder+'" onClick="dirChoose(event)" class="btn btn-primary">';
			html += '<div class="uploadHiddenDiv">';
				html += '<input type="file" name="uploadDir" id="uploadDir" onChange="processFileUploads(event,0,1,\'\')" webkitdirectory directory>';
			html += '</div>';
		html += '</div>';
	}
	
	// Non-IE9 Upload File Setter (hidden)
	if (globalBrowser != "ie9-") {
		html += '<div class="uploadHiddenDiv">';
			html += '<input type="file" name="uploadFile" id="uploadFile" onChange="processFileUploads(event,0,0,\'\')" class="btn btn-primary"';
			
			// Check for Win/Safari combo, as multiple not supported
			if (globalOs == "win" && globalBrowser == "safari") {
			} else {
				html += " multiple";
			}
						
			html += '>';
		html += '</div>';
	}

	// Write the HTML to the div
	document.getElementById("uploadButtonsDiv").innerHTML = html;
}

//##############################################
// ADJUST BUTTON WIDTH (FOR LANGUAGES)
//##############################################

function adjustButtonWidth(str) {

	if (str.length > 12)
		return 'inputButtonNf';
	else
		return "inputButton";
}

//##############################################
// EXECUTE FUNCTIONS ON LOAD
//##############################################

detectBrowser();
detectOs();
listenDropFiles();
listenContextMenu();
setFileWindowSize("ajaxContentWindow",0,0);
displayUploadButtons();
