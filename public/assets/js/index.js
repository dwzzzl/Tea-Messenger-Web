
var routes = doc().createElement("script");
	routes.type = "text/javascript";
	routes.src 	= "/assets/js/routes.js";
	routes.id   = "___router";
	routes.onload = function() { route_handle(); };
domId("head").appendChild(routes);

window.addEventListener("hashchange", function() {
	domId("head").innerHTML = " <meta charset=\"UTF-8\"><title></title>";
	route_handle(); 
}, false);