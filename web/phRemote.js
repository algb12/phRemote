// Location of the phRemote browser API
APIURL = "src/PhRemoteAPI.php";

// Load initial functions
window.onload = function() {
    loadInitValues();
}

// Cross-browser AJAX function
function ajax(url, callback, data, x) {
    try {
        x = new(this.XMLHttpRequest || ActiveXObject)('MSXML2.XMLHTTP.3.0');
        x.open(data ? 'POST' : 'GET', url, 1);
        x.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        x.onreadystatechange = function() {
            x.readyState > 3 && callback && callback(x.responseText, x);
        };
        x.send(data)
    } catch (e) {
        window.console && console.log(e);
    }
};

// When page finishes loading, populates datafields
function loadInitValues() {
    var elems = document.getElementsByName('datafield');
    var dataArr = [];

    // Collates all datafields into an array
    for (var elem in elems) {
        if (elems.hasOwnProperty(elem)) {
            var data = {
                module: null,
                field: null
            };
            data.module = document.getElementsByName('datafield')[elem].getAttribute('data-module');
            data.field = document.getElementsByName('datafield')[elem].getAttribute('data-field');
            dataArr.push(data);
        }
    }

    // Turns datafields array into JSON
    var dataJSON = JSON.stringify(dataArr);

    // Sends datafield array to phRemote server
    var queryparams = "op=getInitData&dataJSON=" + dataJSON;

    console.log("POST request to: " + APIURL + " with params: " + queryparams);

    // Runs AJAX query, response is the values for the datafields returned by the phRemote server
    ajax(APIURL, function(response) {
        console.log("Server responded: " + response);

        // Turns returned JSON into an array
        var dataArr = JSON.parse(response);

        // Populates all datafields with corresponding values
        for (var elem in dataArr) {
            if (dataArr.hasOwnProperty(elem)) {
                var field = document.getElementById(dataArr[elem].field);
                var value = dataArr[elem].value;

                field.innerHTML = value;
                console.log(dataArr[elem].field + ': ' + value);
            }
        }
    }, queryparams);
}

// Executes a command by sending it to the phRemote server
function exec(caller) {
    var module = caller.getAttribute('data-module');
    var action = caller.getAttribute('data-action');
    var queryparams = "op=exec&module=" + module + "&action=" + action;
    if (caller.getAttribute('data-post-elem') !== null) {
        var elem = document.getElementsByName(caller.getAttribute('data-post-elem'))[0];
        queryparams += "&value=" + elem.value;
    }
    if (caller.getAttribute('type') === 'range') {
        queryparams += "&value=" + caller.value;
    }
    console.log("POST request to: " + APIURL + " with params: " + queryparams);

    // Sends command to phRemote server and updates datafield if applicable
    ajax(APIURL, function(response) {
        console.log("Server responded: " + response);

        // Turns JSON resposne from the phRemote server into an array
        var dataArr = JSON.parse(response);

        // If applicable, populates each datafield with its corresponding value
        if (dataArr.callback !== null) {
            var callback = dataArr.callback;
            for (var elem in callback) {

                if (callback.hasOwnProperty(elem)) {
                    var field = document.getElementById(dataArr.callback[elem].field);
                    var value = dataArr.callback[elem].value;

                    field.innerHTML = value;
                    console.log(dataArr.callback[elem].field + ': ' + value);
                }
            }
        }
    }, queryparams);
}
