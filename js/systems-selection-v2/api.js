class SystemsSelectionApi {
	constructor(http) {
		this._http = http;
		this.client = '/systems-selection-api/';
	}

	getCode(phone) {
		return this.request(this.client + 'get-code/', {phone});
	}

	loadJSON() {
		return this.request(this.client + 'load-json/');
	}

	loadSystems(ids) {
		return this.request(this.client + 'load-systems/', {ids: ids});
	}

	submitForm(params) {
		return this.request(this.client + 'submit-form/', {params});
	}

	verifyCode(code) {
		return this.request(this.client + 'verify-code/', {code});
	}

	request(url, params = {}) {
		return new Promise((resolve, reject) =>
			this._http.get(url, {params: params}).then(response => {
				if(response.data.success === true) {
					resolve(response.data.dataSet);
				} else {
					reject(new Error(response));
				}
			}).catch(error => console.log(error))
		);
	}
}

export default SystemsSelectionApi;