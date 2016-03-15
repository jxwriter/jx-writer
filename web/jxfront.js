var JX = {};

JX.Server = function(){

	this.baseUrl = "http://jxwriter.local/app_dev.php/";

	this.variables = new JX.Vars();
	this.requestScene = function(sceneId, callbackSuccess, callbackError){

		if (!callbackError) {
			callbackError=function(e, responseText){
				console.error(e);
				console.error(responseText);
			}
		}

		var request = new XMLHttpRequest();
		var params = "?" + this.variables.toString();
		var url = this.baseUrl + "api/scene/" + sceneId + params;
		
		request.open('GET', url, true);
		request.onload = function(e){
			var json;
			try {
				json = JSON.parse(this.responseText);
			} catch(exception) {
				callbackError(exception, this.responseText);
			}

			if (json) {
				callbackSuccess(json);
			}
			
		}

		request.onerror = function(e){
			callbackError(e);
		}

		request.send();
	}
}

JX.Vars = function(){
	this.variables = {};

	this.init = function(name, value){
		
		if(value == undefined) {
			value = 0;
		}

		this.variables[name] = value;
	}

	this.add = function(name, value){
		this.variables[name] += value;
	}

	this.toString = function(){
		var temp = [];

		for (key in this.variables) {
			temp.push(key + "=" + this.variables[key]);
		};

		return temp.join("&");
	}
}